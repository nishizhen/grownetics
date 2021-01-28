<?php

namespace App\Model\Table;

use Cake\Cache\Cache;
use Cake\ORM\Table;
use Cake\Log\Log;
use Cake\Validation\Validator;
use SoftDelete\Model\Table\SoftDeleteTrait;
use Cake\ORM\TableRegistry;

use InfluxDB\Point;
use InfluxDB\Client;
use InfluxDB\Database;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

use App\Lib\SystemEventRecorder;

/**
 * Devices Model
 *
 * @property \App\Model\Table\DatapointsTable|\Cake\ORM\Association\HasMany $Datapoints
 * @property \App\Model\Table\OutputsTable|\Cake\ORM\Association\HasMany $Outputs
 * @property \Cake\ORM\Association\HasMany $Raw
 * @property \App\Model\Table\SensorsTable|\Cake\ORM\Association\HasMany $Sensors
 *
 * @method \App\Model\Entity\Device get($primaryKey, $options = [])
 * @method \App\Model\Entity\Device newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Device[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Device|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Device patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Device[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Device findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @property \App\Model\Table\MapItemsTable|\Cake\ORM\Association\BelongsTo $MapItems
 * @mixin \App\Model\Behavior\EnumBehavior
 * @mixin \App\Model\Behavior\NotifierBehavior
 */
class DevicesTable extends Table
{
  use SoftDeleteTrait;

  public $enums = array(
    'status' => array(
      'Disabled',
      'Enabled',
      'Rebooting',
      'Active',
      # Hasn't been heard from in a while. Used to prevent multiple 'device down' notices.
      'Offline'
    ),
    'type' => array(
      'Sensing',
      'Control'
    )
  );


  /**
   * Initialize method
   *
   * @param array $config The configuration for the Table.
   * @return void
   */
  public function initialize(array $config)
  {
    parent::initialize($config);

    $this->setTable('devices');
    $this->setDisplayField('label');
    $this->setPrimaryKey('id');

    $this->addBehavior('Timestamp');
    $this->addBehavior('Enum');
    $this->addBehavior('Notifier', [
      'notification_level' => 1
    ]);
    $this->addBehavior('Mappable');
    $this->addBehavior('Organization');

    $this->hasMany('Outputs', [
      'foreignKey' => 'device_id'
    ]);
    $this->hasMany('Raw', [
      'foreignKey' => 'device_id'
    ]);
    $this->hasMany('Sensors', [
      'foreignKey' => 'device_id'
    ]);
  }

  /**
   * Default validation rules.
   *
   * @param \Cake\Validation\Validator $validator Validator instance.
   * @return \Cake\Validation\Validator
   */
  public function validationDefault(Validator $validator)
  {
    $validator
      ->integer('id')
      ->allowEmpty('id', 'create');

    $validator
      ->requirePresence('label', 'create')
      ->notEmpty('label');

    $validator
      ->dateTime('last_message')
      ->allowEmpty('last_message');

    $validator
      ->boolean('deleted')
      ->allowEmpty('deleted');

    $validator
      ->dateTime('deleted_date')
      ->allowEmpty('deleted_date');

    $validator
      ->integer('refresh_rate')
      ->allowEmpty('refresh_rate');

    return $validator;
  }

  /*
     * Check if a notification is required based on the last number of offline devices
     * found compared to the current number, or if the last timestamp was updated more
     * than 24 hours ago.
     *
     * @return True if notification is required, False if not
     */
  public function checkNotificationRequired($devices_offline)
  {
    $sf = new \SensioLabs\Consul\ServiceFactory(['base_uri' => 'http://consul:8500']);
    $kv = $sf->get('kv');
    try {
      $result = $kv->get('devices/offline', ['raw' => true])->getBody();
    } catch (\Exception $e) {
      $result = 0;
    }

    if ($result != $devices_offline) {
      return true;
    } else if ($result == $devices_offline) {
      try {
        $consul_time = $kv->get('devices/offline_time', ['raw' => true])->getBody();
      } catch (\Exception $e) {
        $consul_time = strtotime('now');
        $kv->put('devices/offline_time', time());
      }

      $cutoff = strtotime('-24 hours', time());
      return $consul_time <= $cutoff ? true : false;
    }
  }

  public function updateKvStore($device_number)
  {
    $current_time = time();
    $sf = new \SensioLabs\Consul\ServiceFactory(['base_uri' => 'http://consul:8500']);
    $kv = $sf->get('kv');
    try {
      $kv->put('devices/offline', $device_number);
      $kv->put('devices/offline_time', $current_time);
    } catch (\Exception $e) {
    }
  }

  public function updateStatuses($shell)
  {
    $this->Notifications = TableRegistry::get('Notifications');
    $this->RuleActions = TableRegistry::get('RuleActions');

    // Load all Devices and set any that haven't been herd from in 30 seconds to reboot
    // This lets the wifi reset board know to reset it's device when it hits consul
    // Send an alert after 10 minutes that a device hasn't been heard from
    $deviceAlertTime = 10; // Minutes
    $deviceAlertTimeSecs = $deviceAlertTime * 60;
    $offline_devices = [];
    $online_devices = [];
    $device_online_percent = 0;
    $recorder = new SystemEventRecorder();

    $devices = $this->find('all');
    foreach ($devices as $device) {
      $cachedDevice = Cache::read('device-' . $device->id);
      if ($device->reboot_rate)
        $deviceRebootTime = $device->reboot_rate;
      else {
        $deviceRebootTime = 30;
      }

      if (!isset($cachedDevice['last_message'])) {
        $cachedDevice['last_message'] = date("Y-m-d H:i:s");
      }

      if ($cachedDevice['last_message'] < date("Y-m-d H:i:s", time() - $deviceRebootTime)) {
        if ($cachedDevice['last_message'] < date("Y-m-d H:i:s", time() - $deviceAlertTimeSecs)) {
          $cachedDevice['status'] = $this->enumValueToKey('status', 'Offline');
          $offline_devices[] = $device;
        } else {
          $cachedDevice['status'] = $this->enumValueToKey('status', 'Rebooting');
        }
        $rebooting = 1;
      } else {
        $online_devices[] = $device;
        $cachedDevice['status'] = $this->enumValueToKey('status', 'Active');
        $rebooting = 0;
      }
      Cache::write('device-' . $device->id, $cachedDevice);

      if ($device->type == $this->enumValueToKey('type', 'Sensing')) {
        # Only auto-reboot sensing devices.
        try {
          $sf = new \SensioLabs\Consul\ServiceFactory(['base_uri' => 'http://consul:8500']);
          $kv = $sf->get('kv');
          $kv->put('devices/' . $device->id . '/reboot', $rebooting);
        } catch (\Exception $e) {
        }
      }
    }

    if (count($offline_devices) > 0) {
      $notify_needed = $this->checkNotificationRequired(count($offline_devices));
      if ($notify_needed == true) {
        $this->updateKvStore(count($offline_devices));
        $message = '';
        if (count($offline_devices) > 5) {
          $message = count($offline_devices) . ' Devices';
        } else {
          foreach ($offline_devices as $device) {
            $message .= '[Device ' . $device->id . ", ";
          }
          $message = rtrim($message, ', ');
        }
        $notificationData = array(
          'source_type' => $this->Notifications->enumValueToKey('source_type', 'Device'),
          'status' => $this->Notifications->enumValueToKey('status', 'Queued'),
          'message' => (count($offline_devices) > 1) ? $message . " haven't been heard from in " . $deviceAlertTime . " minutes" : $message . " hasn't been heard from in " . $deviceAlertTime,
          'notification_level' => $this->RuleActions->enumValueToKey('notification_level', 'Dashboard Alert')
        );
        $notification = $this->Notifications->newEntity($notificationData);
        if (!$this->Notifications->save($notification)) {
          $this->out("Save failed");
        }
        try {
          $recorder->recordEvent(
            'system_events',
            'device_offline',
            count($offline_devices)
          );
        } catch (\Exception $e) {
          print_r($e);
          Log::write('error', 'Writing system event to influxdb failed');
        }
      }
    }

    foreach ($online_devices as $device) {
      try {
        $recorder->recordEvent(
          'system_events',
          'device_online',
          1,
          [
            'device_id' => $device->id,
            'version' => $device->version,
            'api_id' => $device->api_id
          ]
        );
      } catch (\Exception $e) {
        Log::write('error', 'Writing system event to influxdb failed');
      }
    }
    if (count($devices->toArray())) {
      $device_online_percent = count($online_devices) / count($devices->toArray()) * 100;
      try {
        $recorder->recordEvent(
          'system_events',
          'device_online_percent',
          $device_online_percent
        );
      } catch (\Exception $e) {
        print_r($e);
        Log::write('error', 'Writing system event to influxdb failed');
      }
    }
  }

  public function updateDeviceInfo($device, $data)
  {
    if (isset($data['date'])) {
      $date = $data['date'];
      $device->last_message = date("Y-m-d H:i:s", strtotime($date));
    } else {
      $date = null;
      $device->last_message = date("Y-m-d H:i:s");
    }
    $device['status'] = $this->enumValueToKey('status', 'Active');
    $device->dontNotify = true;

    if (isset($data['v'])) {
      $device->version = $data['v'];
    }
    Cache::write('device-' . $data['id'], $device);
  }

  /**
   * Accepts data from a device hitting the API and processes it.
   * @param $device
   * @param $data
   * @return array|void
   */
  public function processData($data)
  {

    $this->Sensors = TableRegistry::get('Sensors');

    $sensorData = explode(',', $data['d']);

    $sensorArray = array();
    $zoneArray = array();
    foreach ($sensorData as $dat) {
      $dat = trim($dat, "[]");
      $dataPoint = explode(':', $dat);
      if (!isset($dataPoint[1])) {
        return false;
      }
      $pin = $dataPoint[0];
      $value = $dataPoint[1];
      if (!is_numeric($value)) {
        // We have two data points (humidity and temp!), split them.
        $values = explode('-', $value);
        $airTempTypeId = $this->Sensors->enumValueToKey('sensor_type', 'Air Temperature');
        $sensor = $this->savePinData($data['id'], 'M' . substr($pin, 1, 3), $values[1], $airTempTypeId);
        $value = $values[0];
        if ($sensor) {
          array_merge($zoneArray, $sensor['zones']);
          $sensorArray[] = $sensor['id'];
        }
      }
      $sensor = $this->savePinData($data['id'], $pin, $value, false);
      if ($sensor) {
        array_merge($zoneArray, $sensor['zones']);
        $sensorArray[] = $sensor['id'];
      }
    }
    return ['sensorArray' => $sensorArray, 'id' => $data['id']];
  }

  private function savePinData($deviceId, $pin, $value, $sensor_type)
  {
    $this->Sensors = TableRegistry::get('Sensors');

    if (!$sensor_type) {
      # First find the Sensor
      $sensor = Cache::remember('sensor-api-' . $deviceId . '-' . $pin, function () use ($deviceId, $pin) {
        $params = array(
          'contain' => [
            'Zones'
          ],
          'conditions' => array(
            'device_id' => $deviceId,
            'sensor_pin' => $pin
          ),
          'fields' => array(
            'id',
            'sensor_type_id'
          )
        );
        $query = $this->Sensors->find('all', $params);
        return $query->first();
      });
    } else {
      # If we have a sensor_type, use that to be more specific. (For air temp sensors that share a pin with humidity sensors)
      $sensor = Cache::remember('sensor-api-' . $deviceId . '-' . $pin . '-' . $sensor_type, function () use ($deviceId, $pin, $sensor_type) {
        $params = array(
          'contain' => [
            'Zones'
          ],
          'conditions' => array(
            'device_id' => $deviceId,
            'sensor_pin' => $pin,
            'sensor_type_id' => $sensor_type
          ),
          'fields' => array(
            'id',
            'sensor_type_id'
          )
        );
        $query = $this->Sensors->find('all', $params);
        return $query->first();
      });
    }

    if (!$sensor) {
      return;
    }

    if ($sensor_type == $this->Sensors->enumValueToKey('sensor_type', 'Co2') && $value == 0) {
      return;
    }

    // check sensor type for calibration
    $calibratedValue = $this->calibrate($sensor, $value);

    # Attempt to save the DataPoint to the local RabbitMQ queue to be processed by our data pipeline
    try {
      //                $shell->out(time().'Process');
      $connection = new AMQPStreamConnection('rabbitmq', 5672, 'rabbit', 'rabbit');
      $channel = $connection->channel();

      $channel->queue_declare('data.sensor', false, false, false, false);
      $channel->exchange_declare('data.sensor', 'fanout', false, false, false);
      $channel->queue_bind('data.sensor', 'data.sensor');

      $json = json_encode(array(array(
        'value' => (float) $calibratedValue,
        'source_id' => $sensor['id'],
        // Was $this->DataPoints->enumValueToKey('source_type', 'Sensor')
        // 'data_source_type' => array(
        //     'Sensor',
        //     'Zone',
        //     'Weather'
        // )

        // TODO: Put this magic number somewhere else
        'source_type' => 0,
        'sensor_type' => $sensor['sensor_type_id'],
        'data_type' => $this->Sensors->enumKeyToValue('sensor_data_type', $sensor['sensor_type_id']),
        'device_id' => $deviceId,
        'created' => (string) date("Y-m-d H:i:s"),
        'facility_id' => (float) env('FACILITY_ID')
      )));
      // print_r($json); die();
      $msg = new AMQPMessage($json);
      $channel->basic_publish($msg, 'data.sensor', 'data.sensor');

      $channel->close();
      $connection->close();
    } catch (\Exception $e) {
      // Couldn't connect to AMQP server. Should probably create a notification here
      // but with a flag to only create one, so there aren't a ton of notifications created
      // every time the server dies for some reason.
      // $shell->out($e);
      //   print_r($e);die("?");
    }

    # Attempt to save the DataPoint to the local Influx DB instance.
    try {
      $points = [
        new Point(
          'sensor_data', // name of the measurement
          (float) $calibratedValue, // the measurement value
          [
            'source_type' => 0,
            'sensor_type' => $sensor['sensor_type_id'],
            'data_type' => $this->Sensors->enumKeyToValue('sensor_data_type', $sensor['sensor_type_id']),
            'device_id' => $deviceId,
            'facility_id' => env('FACILITY_ID'),
            'source_id' => $sensor['id'],

          ],
          [], // optional additional fields
          time() // Time precision has to be set to seconds!
        )
      ];
      $database = Client::fromDSN(sprintf('influxdb://%s:%s@%s:%s/%s', env('INFLUX_USER'), env('INFLUX_PASS'), env('INFLUX_HOST'), env('INFLUX_PORT'), 'sensor_data'));
      // we are writing unix timestamps, which have a second precision
      $result = $database->writePoints($points, Database::PRECISION_SECONDS);
    } catch (\Exception $e) {
      # Failed to save to influx. As above should probably create an alert here
      print_r($e);
      die("Couldn't write to influx.");
    }

    Cache::write('sensor-value-' . $sensor['id'], $calibratedValue);
    Cache::write('sensor-time-' . $sensor['id'], date("Y-m-d H:i:s"));

    return $sensor;
  }

  public function calibrate($sensor, $value)
  {
    $calibrated = $value;

    if (null !== ($sensor['calibration'])) {
      // individual sensor override
      if ('multiply' == $sensor['sensor_type']['calibration_operator']) {
        $calibrated = $value * $sensor['calibration'];
      }
    } else if (null !== ($sensor['sensor_type'])) {
      // sensor type calibration override
      if ('multiply' == $sensor['sensor_type']['calibration_operator']) {
        $calibrated = $value * $sensor['sensor_type']['calibration_operand'];
      }
    }

    return $calibrated;
  }

  public function isRebooting($device)
  {
    $recorder = new SystemEventRecorder();
    $cached_device = Cache::read('device-' . $device->id);
    if ($cached_device['status'] == $this->enumValueToKey('status', 'Rebooting')) {
      $recorder->recordEvent('system_events', 'wifi_told_device_to_reboot', 1, ['device_id' => $device->id]);
      return true;
    } else {
      $recorder->recordEvent('system_events', 'wifi_told_device_not_to_reboot', 1, ['device_id' => $device->id]);
      return false;
    }
  }

  public function getSensors($device)
  {
    $this->Sensors = TableRegistry::get('Sensors');
    $query = $this->Sensors->find('all', [
      'conditions' => [
        'device_id' => $device->id,
        'status' => $this->Sensors->enumValueToKey('status', 'Enabled')
      ],
      'fields' => ['sensor_type_id', 'sensor_pin', 'calibration']
    ]);
    $sensors = $query->all();

    $type1sensors = '';
    $type2sensors = '';
    $type4sensors = '';
    $type5sensors = '';
    $type6sensors = '';
    $type7sensors = '';
    $type8sensors = '';
    $type9sensors = '';
    $type11sensors = '';
    $type12sensors = '';
    $type13sensors = '';
    $type14sensors = '';
    foreach ($sensors as $sensor) {
      switch ($sensor['sensor_type_id']) { //FIXME: update to use sensor_type table?
        case 1:
          if (strlen($type1sensors) > 0) {
            $type1sensors .= ',';
          }
          $type1sensors .= '"' . $sensor['sensor_pin'] . '"';
          break;
        case 2:
          if (strlen($type2sensors) > 0) {
            $type2sensors .= ',';
          }
          $type2sensors .= '"' . $sensor['sensor_pin'] . '"';
          break;
        case 4:
          if (strlen($type4sensors) > 0) {
            $type4sensors .= ',';
          }
          $type4sensors .= '"' . $sensor['sensor_pin'] . '"';
          break;
        case 5:
          if (strlen($type5sensors) > 0) {
            $type5sensors .= ',';
          }
          $type5sensors .= '"' . $sensor['sensor_pin'] . '"';
          break;
        case 6:
          if (strlen($type6sensors) > 0) {
            $type6sensors .= ',';
          }
          $type6sensors .= '"' . $sensor['sensor_pin'] . '"';
          break;
        case 7:
          if (strlen($type7sensors) > 0) {
            $type7sensors .= ',';
          }
          $type7sensors .= '"' . $sensor['sensor_pin'] . '"';
          break;
        case 8:
          if (strlen($type8sensors) > 0) {
            $type8sensors .= ',';
          }
          $type8sensors .= '"' . $sensor['sensor_pin'] . '"';
          break;
        case 9:
          if (strlen($type9sensors) > 0) {
            $type9sensors .= ',';
          }
          $type9sensors .= '"' . $sensor['sensor_pin'] . '"';
          break;
        case 11:
          if (strlen($type11sensors) > 0) {
            $type11sensors .= ',';
          }
          $type11sensors .= '"' . $sensor['sensor_pin'] . '"';
          break;
        case 12:
          if (strlen($type12sensors) > 0) {
            $type12sensors .= ',';
          }
          $type12sensors .= '"' . $sensor['sensor_pin'] . '"';
          break;
        case 13:
          if (strlen($type13sensors) > 0) {
            $type13sensors .= ',';
          }
          $type13sensors .= '"' . $sensor['sensor_pin'] . '"';
          break;
        case 14:
          if (strlen($type14sensors) > 0) {
            $type14sensors .= ',';
          }
          $type14sensors .= '"' . $sensor['sensor_pin'] . '"';
          break;
      }
    }

    $return = "\"boot\":1,\"i1\":[" . $type1sensors .
      "],\"i2\":[" . $type2sensors .
      "],\"i4\":[" . $type4sensors .
      "],\"i5\":[" . $type5sensors .
      "],\"i6\":[" . $type6sensors .
      "],\"i7\":[" . $type7sensors .
      "],\"i8\":[" . $type8sensors .
      "],\"i9\":[" . $type9sensors .
      "],\"i11\":[" . $type11sensors .
      "],\"i12\":[" . $type12sensors .
      "],\"i13\":[" . $type13sensors .
      "],\"i14\":[" . $type14sensors .
      "]";
    if ($device['refresh_rate'] > 0) {
      $return .= ",\"refresh\":" . $device['refresh_rate'];
    }
    return $return;
  }

  public function beforeDelete($event, $device, $options)
  {
    $this->Sensors = TableRegistry::get('Sensors');
    $sensors = $this->Sensors->find('all', ['conditions' => ['device_id' => $device->id]]);
    foreach ($sensors as $sensor) {
      $this->Sensors->delete($sensor);
    }
    $this->Outputs = TableRegistry::get('Outputs');
    $outputs = $this->Outputs->find('all', ['conditions' => ['device_id' => $device->id]]);
    foreach ($outputs as $output) {
      $this->Outputs->delete($output);
    }
    return true;
  }

  public function beforeSave($event,  $entity)
  {
    if ($entity->isNew() && !isset($entity->doCreateSensors)) { // create sensors for new device.
      $entity->doCreateSensors = true;
      $entity->dontNotify = true;
    }
  }

  public function afterSave($event,  $entity,  $options)
  {
    if ($entity->api_id == 0) {
      $entity->api_id = $entity->id;
      $this->save($entity);
    }

    if ($entity->doCreateSensors == true && $entity->dontMap == false) {
      //            Log::write("debug" , "creating sensors for entity ===> " .$entity);
      $this->createSensors($entity);
    }
  }

  private function createSensors($entity)
  {
    return false;
    //don't try to create sensors again
    $entity->doCreateSensors = false;

    if (!isset($this->Sensors)) {
      $this->Sensors = TableRegistry::get("Sensors");
    }

    // tempHumLow
    $humLow = $this->Sensors->newEntity([
      'sensor_pin' => 'THH',
      'label' => $entity->label . ' - THH',
      'sensor_type_id' => $this->Sensors->enumValueToKey('sensor_type', 'BME280 Humidity'),
      'status' => 1,
      'floorplan_id' => $entity->floorplan_id,
      'latitude' => $entity->map_item->latitude,
      'longitude' =>  $entity->map_item->longitude,
      'offsetHeight' => 1
    ]);
    $humLow->zones = $entity->zones;
    $humLow->dirty('zones', true);
    $humLow->device = $entity;
    $humLow->dontNotify = true;

    if (!$this->Sensors->save($humLow)) {
      //     $this->log("saved new sensor => ".$tempHumLow->label, 'debug');
      // } else {
      Log::write("debug", "Failed to save Sensor.");
      //Log::write("debug", $humLow->errors());
    }

    $tempLow = $this->Sensors->newEntity([
      'sensor_pin' => 'THT',
      'label' => $entity->label . ' - THT',
      'sensor_type_id' => $this->Sensors->enumValueToKey('sensor_type', 'BME280 Air Temperature'),
      'status' => 1,
      'floorplan_id' => $entity->floorplan_id,
      'latitude' => $entity->map_item->latitude,
      'longitude' =>  $entity->map_item->longitude,
      'offsetHeight' => 1
    ]);
    $tempLow->zones = $entity->zones;
    $tempLow->dirty('zones', true);
    $tempLow->device = $entity;
    $tempLow->dontNotify = true;

    if (!$this->Sensors->save($tempLow)) {
      //     $this->log("saved new sensor => ".$tempHumLow->label, 'debug');
      // } else {
      //Log::write("debug", $tempLow->errors());
      Log::write("debug", "Failed to save Sensor.");
    }


    $pressureLow = $this->Sensors->newEntity([
      'sensor_pin' => 'THP',
      'label' => $entity->label . ' - THP',
      'sensor_type_id' => $this->Sensors->enumValueToKey('sensor_type', 'BME280 Air Pressure'),
      'status' => 1,
      'floorplan_id' => $entity->floorplan_id,
      'latitude' => $entity->map_item->latitude,
      'longitude' =>  $entity->map_item->longitude,
      'offsetHeight' => 1
    ]);
    $pressureLow->zones = $entity->zones;
    $pressureLow->dirty('zones', true);
    $pressureLow->device = $entity;
    $pressureLow->dontNotify = true;

    if (!$this->Sensors->save($pressureLow)) {
      //     $this->log("saved new sensor => ".$tempHumLow->label, 'debug');
      // } else {
      //Log::write("debug", $tempLow->errors());
      Log::write("debug", "Failed to save Sensor.");
    }

    // humHigh == M2
    $humHigh = $this->Sensors->newEntity([
      'label' => $entity->label . ' - CH',
      'sensor_pin' => 'CH',
      'sensor_type_id' => $this->Sensors->enumValueToKey('sensor_type', 'SCD30 Humidity'),
      'status' => 1,
      'floorplan_id' => $entity->floorplan_id,
      'latitude' => $entity->map_item->latitude,
      'longitude' =>  $entity->map_item->longitude,
      'offsetHeight' => 2

    ]);
    $humHigh->zones = $entity->zones;
    $humHigh->dirty('zones', true);
    $humHigh->device = $entity;
    $humHigh->dontNotify = true;

    if (!$this->Sensors->save($humHigh)) {
      //     $this->log("saved new sensor => ".$tempHumHigh->label, 'debug');
      // } else {
      Log::write("debug", $humHigh->errors());
      Log::write("debug", "Failed to save Sensor.");
    }

    $tempHigh = $this->Sensors->newEntity([
      'label' => $entity->label . ' - CT',
      'sensor_pin' => 'CT',
      'sensor_type_id' => $this->Sensors->enumValueToKey('sensor_type', 'SCD30 Air Temperature'),
      'status' => 1,
      'floorplan_id' => $entity->floorplan_id,
      'latitude' => $entity->map_item->latitude,
      'longitude' =>  $entity->map_item->longitude,
      'offsetHeight' => 2
    ]);
    $tempHigh->zones = $entity->zones;
    $tempHigh->dirty('zones', true);
    $tempHigh->device = $entity;
    $tempHigh->dontNotify = true;

    if (!$this->Sensors->save($tempHigh)) {
      Log::write("debug", $tempHigh);
      //     $this->log("saved new sensor => ".$tempHumHigh->label, 'debug');
      // } else {
      //Log::write("debug", $tempHigh->errors());
      Log::write("debug", "Failed to save Sensor.");
    }

    //co2 High
    $co2High = $this->Sensors->newEntity([
      'sensor_pin' => 'CC',
      'label' => $entity->label . ' - CC',
      'sensor_type_id' => $this->Sensors->enumValueToKey('sensor_type', 'SCD30 Co2'),
      'status' => 1,
      'floorplan_id' => $entity->floorplan_id,
      'latitude' => $entity->map_item->latitude,
      'longitude' =>  $entity->map_item->longitude,
      'offsetHeight' => 2
    ]);
    $co2High->zones = $entity->zones;
    $co2High->dirty('zones', true);
    $co2High->device = $entity;
    $co2High->dontNotify = true;

    if (!$this->Sensors->save($co2High)) {
      //     $this->log("saved new sensor => ".$co2High->label, 'debug');
      // } else {
      // Log::write("debug", $co2High->errors());
      Log::write("debug", "Failed to save Sensor.");
    }

    # Par
    $par = $this->Sensors->newEntity([
      'sensor_pin' => 'A0',
      'label' => $entity->label . ' - Par',
      'sensor_type_id' => $this->Sensors->enumValueToKey('sensor_type', 'PAR'),
      'status' => 1,
      'floorplan_id' => $entity->floorplan_id,
      'latitude' => $entity->map_item->latitude,
      'longitude' =>  $entity->map_item->longitude,
      'offsetHeight' => 2
    ]);
    $par->zones = $entity->zones;
    $par->dirty('zones', true);
    $par->device = $entity;
    $par->dontNotify = true;

    if (!$this->Sensors->save($par)) {
      //     $this->log("saved new sensor => ".$co2High->label, 'debug');
      // } else {
      // Log::write("debug", $co2High->errors());
      Log::write("debug", "Failed to save Sensor.");
    }

    # SM1
    $sm1 = $this->Sensors->newEntity([
      'sensor_pin' => 'A1',
      'label' => $entity->label . ' - Soil 1',
      'sensor_type_id' => $this->Sensors->enumValueToKey('sensor_type', 'Soil Moisture'),
      'status' => 1,
      'floorplan_id' => $entity->floorplan_id,
      'latitude' => $entity->map_item->latitude,
      'longitude' =>  $entity->map_item->longitude,
      'offsetHeight' => 2
    ]);
    $sm1->zones = $entity->zones;
    $sm1->dirty('zones', true);
    $sm1->device = $entity;
    $sm1->dontNotify = true;

    if (!$this->Sensors->save($sm1)) {
      //     $this->log("saved new sensor => ".$co2High->label, 'debug');
      // } else {
      // Log::write("debug", $co2High->errors());
      Log::write("debug", "Failed to save Sensor.");
    }
    # SM2
    $sm2 = $this->Sensors->newEntity([
      'sensor_pin' => 'A2',
      'label' => $entity->label . ' - Soil 2',
      'sensor_type_id' => $this->Sensors->enumValueToKey('sensor_type', 'Soil Moisture'),
      'status' => 1,
      'floorplan_id' => $entity->floorplan_id,
      'latitude' => $entity->map_item->latitude,
      'longitude' =>  $entity->map_item->longitude,
      'offsetHeight' => 2
    ]);
    $sm2->zones = $entity->zones;
    $sm2->dirty('zones', true);
    $sm2->device = $entity;
    $sm2->dontNotify = true;

    if (!$this->Sensors->save($sm2)) {
      //     $this->log("saved new sensor => ".$co2High->label, 'debug');
      // } else {
      // Log::write("debug", $co2High->errors());
      Log::write("debug", "Failed to save Sensor.");
    }
  }

  public function enableBurnoutProtection($device)
  {
    $current_time = time();
    $sf = new \SensioLabs\Consul\ServiceFactory(['base_uri' => 'http://' . env('CONSUL_HOST') . ':8500']);
    $kv = $sf->get('kv');
    $kv->put('devices/' . $device->id . '/burnout_protection_time', $current_time);

    $recorder = new SystemEventRecorder();
    $recorder->recordEvent('system_events', 'enable_burnout_protection', 1, ['device_id' => $device->id]);
  }

  public function isBurnoutProtected($device)
  {
    try {
      $sf = new \SensioLabs\Consul\ServiceFactory(['base_uri' => 'http://consul:8500']);
      $kv = $sf->get('kv');
      $burnout_protection_time = $kv->get('devices/' . $device['id'] . '/burnout_protection_time', ['raw' => true])->getBody();
      if (time() - $burnout_protection_time > env('BURNOUT_PROTECTION_DELAY')) {
        # The burnout protection delay has expired, remove the consul record
        return false;
      } else {
        return true;
      }
    } catch (\Exception $e) {
      return false;
    }
  }
}
