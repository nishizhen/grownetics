<?php
namespace App\Model\Table;

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use SoftDelete\Model\Table\SoftDeleteTrait;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;

use InfluxDB\Point;
use InfluxDB\Client;
use InfluxDB\Database;
use App\Lib\DataConverter;
use App\Lib\SystemEventRecorder;


/**
 * Zones Model
 *
 * @property \App\Model\Table\DatapointsTable|\Cake\ORM\Association\HasMany $Datapoints
 * @property \App\Model\Table\OutputsTable|\Cake\ORM\Association\HasMany $Outputs
 * @property \App\Model\Table\SensorsTable|\Cake\ORM\Association\BelongsToMany $Sensors
 *
 * @method \App\Model\Entity\Zone get($primaryKey, $options = [])
 * @method \App\Model\Entity\Zone newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Zone[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Zone|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Zone patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Zone[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Zone findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @property \App\Model\Table\AppliancesTable|\Cake\ORM\Association\BelongsToMany $Appliances
 * @property \App\Model\Table\MapItemsTable|\Cake\ORM\Association\BelongsTo $MapItems
 * @mixin \App\Model\Behavior\EnumBehavior
 * @mixin \App\Model\Behavior\NotifierBehavior
 */
class ZonesTable extends Table
{

    use SoftDeleteTrait;

    public $enums = [
        'status' => [
            'Disabled',
            'Enabled'
        ],
        'plant_zone_types' => [
            'None',
            'Clone',
            'Veg',
            'Bloom',
            'Dry',
            'Cure',
            'Processing',
            'Storage',
            'Shipping',
            'HVAC',
        ],
        'zone_types' => [
            'None',
            'Room',
            'HVAC',
            'Group',
            'Custom'
        ]
    ];

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('zones');
        $this->setDisplayField('label');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Enum');
        $this->addBehavior('Notifier');
        $this->addBehavior('Mappable');
        $this->addBehavior('FeatureFlags.FeatureFlags');
        $this->addBehavior('Organization');

        $this->hasMany('Datapoints', [
            'foreignKey' => 'zone_id'
        ]);
        $this->hasMany('Outputs', [
            'foreignKey' => 'zone_id'
        ]);
        
        $this->belongsToMany('Sensors', [
            'joinTable' => 'sensors_zones'
        ]);

        $this->hasMany('Notes', [
            'foreignKey' => 'zone_id'
        ]);

        $this->belongsToMany('MapItems', [
            'joinTable' => 'map_items_zones'
        ]);

        $this->belongsToMany('Appliances', [
            'joinTable' => 'appliances_zones'
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

//        $validator
//            ->requirePresence('label', 'create')
//            ->notEmpty('label');
//
//        $validator
//            ->integer('status')
//            ->requirePresence('status', 'create')
//            ->notEmpty('status');

        $validator
            ->integer('room_zone_id')
            ->allowEmpty('room_zone_id');

        $validator
            ->boolean('deleted')
            ->allowEmpty('deleted');

        return $validator;
    }

    public function processRules()
    {
        $this->RuleConditions = TableRegistry::get('RuleConditions');
        $this->Rules = TableRegistry::get('Rules');

        # Toggles conditions as needed
        $ruleConditionIds = $this->RuleConditions->processConditions(['conditions' => [
            'data_source IN' => [
                $this->RuleConditions->enumValueToKey('data_source', 'Zone'),
                $this->RuleConditions->enumValueToKey('data_source', 'Zone Type'),
                $this->RuleConditions->enumValueToKey('data_source', 'Time')
            ],
            'status IN' => [
                $this->RuleConditions->enumValueToKey('status','Enabled'),
                $this->RuleConditions->enumValueToKey('status','Triggered')
            ],
            'is_default' => false
        ]]);
        $this->Rules->processRules($ruleConditionIds);
    }

    # Determine the average value for each zone, save it into Influx
    public function processData()
    {
        $this->Sensors = TableRegistry::get('Sensors');
        $this->SetPoints = TableRegistry::get('SetPoints');

        $airTempSensorTypeId = $this->Sensors->enumValueToKey('sensor_type','Air Temperature');
        $humiditySensorTypeId = $this->Sensors->enumValueToKey('sensor_type','Humidity');
        $vpdSensorTypeId = $this->Sensors->enumValueToKey('sensor_type','Vapor Pressure Deficit');

        $query = $this->find('all')->select([
            'Zones.id',
            'Zones.plant_zone_type_id',
        ]);
        $query->cache('zone_ids');

        foreach ($query as $zone) {
            $sensorsByType = $this->getSensorsByType($zone);

            if ($sensorsByType) {
                $points = [];
                $airTempAverage = 0;
                $humAverage = 0;
                foreach ($sensorsByType as $type => $sensors) {
                    $total = 0;
                    $sensorCount = 0;
                    foreach ($sensors as $sensor) {
                        $value = Cache::read('sensor-value-' . $sensor);
                        if ($value) {
                            $total = $total + $value;
                            $sensorCount++;
                        }
                    }

                    if ($total) {
                        $value = round($total / $sensorCount, 2);
                        if ($type == $airTempSensorTypeId) {
                            $airTempAverage = $value;
                        }
                        else if ($type == $humiditySensorTypeId) {
                            $humAverage = $value;
                        }
                        if ($airTempAverage && $humAverage) {
                            $converter = new DataConverter();
                            $vaporPressureDeficit = $converter->convertToVaporPressureDeficit($humAverage, $airTempAverage);
                            array_push($points,
                                new Point(
                                    'sensor_data', // name of the measurement
                                    (float) $vaporPressureDeficit, // the measurement value
                                    [
                                        'source_type' => 1,
                                        'type' => $vpdSensorTypeId,
                                        'facility_id' => env('FACILITY_ID'),
                                        'source_id' => $zone['id'],
                                    ],
                                    [], // optional additional fields
                                    time() // Time precision has to be set to seconds!
                                )
                            );
                        } 
                        array_push($points,

                                new Point(
                                    'sensor_data', // name of the measurement
                                    (float) $value, // the measurement value
                                    [
                                        'source_type' => 1,
                                        'type' => $type,
                                        'facility_id' => env('FACILITY_ID'),
                                        'source_id' => $zone['id'],
                                    ],
                                    [], // optional additional fields
                                    time() // Time precision has to be set to seconds!
                                )
                        );

                        $active_batches = $zone->getActiveBatches();

                        foreach($active_batches as $batch) {
                            array_push($points,

                                new Point(
                                    'sensor_data', // name of the measurement
                                    (float)$value, // the measurement value
                                    [
                                        'source_type' => $this->DataPoints->enumValueToKey('source_type', 'Harvest Batch'),
                                        'type' => $type,
                                        'facility_id' => env('FACILITY_ID'),
                                        'source_id' => $batch['id'],
                                    ],
                                    [], // optional additional fields
                                    time() // Time precision has to be set to seconds!
                                )
                            );
                        }

                        Cache::write('zone-value-' . $type . '-' . $zone['id'], $value);
                    }
                } #/ Foreach 
                try {
                    $database = Client::fromDSN(sprintf('influxdb://root:root@%s:%s/%s', env('INFLUX_HOST'), env('INFLUX_PORT'), 'sensor_data'));
                    $database->writePoints($points, Database::PRECISION_SECONDS);
                } catch (\Exception $e) {
                    # Failed to save to influx. As above should probably create an alert here
                }
            }
        }
    }

    # Update our BACnet device if needed, send alerts if appropriate
    public function updateBacnetPoints($shell=null)
    {
        $recorder = new SystemEventRecorder();
        $recorder->recordEvent('system_events', 'update_bacnet_points_tick', 1);

        $this->Sensors = TableRegistry::get('Sensors');
        $this->SetPoints = TableRegistry::get('SetPoints');
        $this->Notifications = TableRegistry::get('Notifications');
        $this->RuleActions = TableRegistry::get('RuleActions');

        $query = $this->find('all')->select([
            'Zones.id',
            'Zones.bacnet_hum_read',
            'Zones.bacnet_hum_set',
            'Zones.bacnet_temp_read',
            'Zones.bacnet_temp_set',
            'Zones.bacnet_timestamp',
            'Zones.plant_zone_type_id',
        ]);
        // $query->cache('zone_ids');

        $set_point_alerts_enabled = $this->getFeatureFlagValue("set_point_alerts_enabled");

        $tempSensorTypeId = $this->Sensors->enumValueToKey('sensor_type','Air Temperature');
        $humiditySensorTypeId = $this->Sensors->enumValueToKey('sensor_type', 'Humidity');

        foreach ($query as $zone) {
            $shell->out('Process zone: '.$zone['id']);
            $sensorsByType = $this->getSensorsByType($zone);

            if ($sensorsByType) {
                foreach ($sensorsByType as $type => $sensors) {
                    $shell->out('Process sensor type: '.$type);
                    $value = Cache::read('zone-value-' . $type . '-' . $zone['id']);
                    $shell->out('Got value: '.$value);
                    $shell->out('BACnet Humidity ID: '.$zone['bacnet_hum_read']);
                    # If we have a valid read value, and BACnet object IDs, then go ahead and update the BACnet network
                    if ($value > 0 && $zone['bacnet_hum_read']) {
                        $shell->out("========= UPDATE BACNET! ===========".$type."-".$tempSensorTypeId);
                        switch($type) {
                            case $tempSensorTypeId:
                                $shell->out('Update Temp Point');
                                $fahrenheit = ($value * 9 / 5) + 32;
                                $url = env('BACNET_URL') . $zone['bacnet_temp_read'] . '/Value/' . $fahrenheit . trim(env('BACNET_AUTH'), "'");
                                $ch = curl_init();
                                curl_setopt($ch, CURLOPT_URL, $url);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, $url);
                                $result = curl_exec($ch);
                                $shell->out($result);
                                if (!$result) {
                                    $recorder->recordEvent(
                                        'system_events',
                                        'bacnet_update_failed',
                                        $value,
                                        [
                                            'zone_id' => $zone['id'],
                                            'bacnet_point_id' => $zone['bacnet_temp_read'],
                                            'sensor_type_id' => $type,
                                            'update_type' => 'zone_value',
                                        ]
                                    );
                                } else {
                                    $recorder->recordEvent(
                                        'system_events',
                                        'bacnet_update',
                                        $value,
                                        [
                                            'zone_id' => $zone['id'],
                                            'bacnet_point_id' => $zone['bacnet_temp_read'],
                                            'sensor_type_id' => $type,
                                            'update_type' => 'zone_value',
                                        ]
                                    );
                                }

                                # We only update the timestamp here because if we have temp, we know we have humidity
                                # And it saves us one BACnet gateway request per zone, which is a fair bit
                                $url = env('BACNET_URL') . $zone['bacnet_timestamp'] . '/Value/' . time() . trim(env('BACNET_AUTH'), "'");
                                $ch = curl_init();
                                curl_setopt($ch, CURLOPT_URL, $url);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, $url);
                                $result = curl_exec($ch);
                                $shell->out($result);
                                if (!$result) {
                                    $recorder->recordEvent(
                                        'system_events',
                                        'bacnet_update_failed',
                                        1,
                                        [
                                            'zone_id' => $zone['id'],
                                            'bacnet_point_id' => $zone['bacnet_temp_read'],
                                            'sensor_type_id' => $type,
                                            'update_type' => 'timestamp',
                                        ]
                                    );
                                } else {
                                    $recorder->recordEvent(
                                        'system_events',
                                        'bacnet_update',
                                        1,
                                        [
                                            'zone_id' => $zone['id'],
                                            'bacnet_point_id' => $zone['bacnet_timestamp'],
                                            'sensor_type_id' => $type,
                                            'update_type' => 'timestamp',
                                        ]
                                    );
                                }

                                $setPoint = $this->SetPoints->getSetPointForTarget($this->SetPoints->enumValueToKey('target_type', 'Zone'), $zone, $tempSensorTypeId);
                                if ($setPoint) {
                                    $shell->out("Got Setpoint ID: ".$setPoint->id);
                                    $setPointFahrenheit = ($setPoint->value * 9 / 5) + 32;
                                    $url = env('BACNET_URL') . $zone['bacnet_temp_set'] . '/Value/' . $setPointFahrenheit . trim(env('BACNET_AUTH'), "'");
                                    $ch = curl_init();
                                    curl_setopt($ch, CURLOPT_URL, $url);
                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, $url);
                                    $result = curl_exec($ch);
                                    $shell->out($result);
                                    if (!$result) {
                                        $recorder->recordEvent(
                                            'system_events',
                                            'bacnet_update_failed',
                                            $setPoint->value,
                                            [
                                                'zone_id' => $zone['id'],
                                                'bacnet_point_id' => $zone['bacnet_temp_set'],
                                                'sensor_type_id' => $type,
                                                'update_type' => 'set_point',
                                            ]
                                        );
                                    } else {
                                        $recorder->recordEvent(
                                            'system_events',
                                            'bacnet_update',
                                            $setPoint->value,
                                            [
                                                'zone_id' => $zone['id'],
                                                'bacnet_point_id' => $zone['bacnet_temp_set'],
                                                'sensor_type_id' => $type,
                                                'update_type' => 'set_point',
                                            ]
                                        );
                                    }
                                } else {
                                    $shell->out("No setpoint found");
                                }
                                break;
                            case $humiditySensorTypeId:
                                $shell->out('Update Hum Point');
                                $url = env('BACNET_URL') . $zone['bacnet_hum_read'] . '/Value/' . $value . trim(env('BACNET_AUTH'),"'");
                                $ch = curl_init();
                                curl_setopt($ch, CURLOPT_URL, $url);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, $url);
                                $result = curl_exec($ch);
                                $shell->out($result);
                                if (!$result) {
                                    $recorder->recordEvent(
                                        'system_events',
                                        'bacnet_update_failed',
                                        $value,
                                        [
                                            'zone_id' => $zone['id'],
                                            'bacnet_point_id' => $zone['bacnet_hum_read'],
                                            'sensor_type_id' => $type,
                                            'update_type' => 'zone_value',
                                        ]
                                    );
                                } else {
                                    $recorder->recordEvent(
                                        'system_events',
                                        'bacnet_update',
                                        $value,
                                        [
                                            'zone_id' => $zone['id'],
                                            'bacnet_point_id' => $zone['bacnet_hum_read'],
                                            'sensor_type_id' => $type,
                                            'update_type' => 'zone_value',
                                        ]
                                    );
                                }

                                $setPoint = $this->SetPoints->getSetPointForTarget($this->SetPoints->enumValueToKey('target_type','Zone'), $zone, $humiditySensorTypeId);
                                if ($setPoint) {
                                    $url = env('BACNET_URL') . $zone['bacnet_hum_set'] . '/Value/' . $setPoint->value . trim(env('BACNET_AUTH'),"'");
                                    $ch = curl_init();
                                    curl_setopt($ch, CURLOPT_URL, $url);
                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, $url);
                                    $result = curl_exec($ch);
                                    $shell->out($result);
                                    if (!$result) {
                                        $recorder->recordEvent(
                                            'system_events',
                                            'bacnet_update_failed',
                                            $setPoint->value,
                                            [
                                                'zone_id' => $zone['id'],
                                                'bacnet_point_id' => $zone['bacnet_hum_set'],
                                                'sensor_type_id' => $type,
                                                'update_type' => 'set_point',
                                            ]
                                        );
                                    } else {
                                        $recorder->recordEvent(
                                            'system_events',
                                            'bacnet_update',
                                            $setPoint->value,
                                            [
                                                'zone_id' => $zone['id'],
                                                'bacnet_point_id' => $zone['bacnet_hum_set'],
                                                'sensor_type_id' => $type,
                                                'update_type' => 'set_point',
                                            ]
                                        );
                                    }

                                }
                                break;
                        } # / Sensor Type Switch
                        if (isset($setPoint) && $set_point_alerts_enabled) {
                            $shell->out('Check Set Point Value: '.$setPoint->value);
                            $shell->out('Alarm Tolerance: '.env('SET_POINT_ALARM_TOLERANCE_PERCENTAGE'));
                            # This says if the set point is more than the SET_POINT_ALARM_TOLERANCE_PERCENTAGE out of range, send an alert
                            if ($setPoint && abs((($setPoint->value/$value)*100)-1) > env('SET_POINT_ALARM_TOLERANCE_PERCENTAGE')) {
                                $shell->out('===== Send Set Point Alarm!!! ======');
                                $notificationData = $this->Notifications->newEntity(array(
                                    'status' => 0,
                                    'message' => "Set point " . $setPoint->label . " is out of range. Set point: " . $setPoint->value . " Current Value: " . $value,
                                    'notification_level' => $this->RuleActions->enumValueToKey('notification_level', 'Text Message')
                                ));
                                $this->Notifications->save($notificationData);
                                print_r($notificationData); die("??");
                            }
                        }
                    } # / If we have a valid value and bacnet points
                } #/ Foreach
            }
            else {
                $shell->out('No sensors by type');
            }
        }
    }

    private function getSensorsByType($zone) {
        # This caching is disabled, as we would need to invalidate it when a sensor is edited (disabled/enabled).
        # TODO: Reenable this caching.
        // if (($sensorsByType = Cache::read('sensors-by-type-zone-'.$zone['id'])) === false) {
            # We don't have it in cache, so load the sensors.
            $zone = $this->find('all',['conditions'=>['id'=>$zone['id']]])->contain('Sensors', function ($q) {
                return $q->where(['Sensors.status' => $this->Sensors->enumValueToKey('status','Enabled')]);
            })->first();
            if ($zone) {
                $zone = $zone->toArray();
            }

            $sensorsByType = [];
            if (isset($zone['sensors'])) {
                foreach ($zone['sensors'] as $sensor) {
                    if (!isset($sensorsByType[$sensor['sensor_type_id']]) || !is_array($sensorsByType[$sensor['sensor_type_id']])) {
                        $sensorsByType[$sensor['sensor_type_id']] = [];
                    }
                    array_push($sensorsByType[$sensor['sensor_type_id']], $sensor['id']);
                }
            }
            // Cache::write('sensors-by-type-zone-'.$zone['id'],$sensorsByType);
        // }

        return $sensorsByType;
    }

    public function beforeMarshal($event, $data) {
        if (isset($data['room_zone_id']) && is_string($data['room_zone_id'])) {
            $Zones = TableRegistry::get("Zones");
            $room_zone_text = preg_replace('/_/', ' ', $data['room_zone_id']);

            $zoneEntity = $Zones->find()->where(['label' => $room_zone_text])->first();
            if (isset($zoneEntity)) {
                $data['room_zone_id'] = $zoneEntity->id;
            }
        }
        if (isset($data['plant_zone_type']) && is_string($data['plant_zone_type'])) {
            $pzt = $this->enumValueToKey('plant_zone_types', $data['plant_zone_type']);

            if (isset($pzt)) {
                $data['plant_zone_type_id'] = $pzt;
            }  
        }
    }

    public function beforeSave( $event, $entity)
    {

        if ($entity->isNew() && is_string($entity->zone_type)) {
            $entity->status = 1;
            $entity->zone_type_id = $this->enumValueToKey('zone_types', $entity->zone_type);

            if (isset($entity->room_zone_id)) {
                $Zones = TableRegistry::get("Zones");
                $roomZoneEntity = $Zones->find()->where(['label' => $entity->room_zone_id])->first();
                if (isset($roomZoneEntity)) {
                    $entity->room_zone_id = $roomZoneEntity->id;
                }
            } else {
                $entity->room_zone_id = 0;
            }

            Log::write("debug", $entity);

            $entity->dontNotify = true;

            $mapEntity = $this->MapItems->newEntity([
                'label' => $entity->label
            ]);

            $mapEntity->floorplan_id = $entity->floorplan_id;

            $this->MapItemTypes = TableRegistry::get("MapItemTypes");
            $mapItemType = $this->MapItemTypes->find()->where(['label' => 'Zone' ])->first();

            if (!isset($mapItemType)) {
                $mapItemType = $this->MapItemTypes->newEntity([
                    'label' => 'Zone',
                    'opacity' => 1
                ]);
                $this->MapItemTypes->save($mapItemType);
            }

            //$mapEntity->map_item_type = $mapItemType;

            $mapEntity->type = "Zone";
            $mapEntity->zones = [ $entity ];
            $mapEntity->dirty("zones");

            if (isset($entity->latitude) && isset($entity->longitude)) {
                $mapEntity->latitude = $entity->latitude;
                $mapEntity->longitude = $entity->longitude;
            }

            if (isset($entity->offsetHeight)) {
                $mapEntity->offsetHeight = $entity->offsetHeight;
            } else {
                $mapEntity->offsetHeight = 0;
            }

            if (isset($entity->geoJSON)) {
                $mapEntity->geoJSON = $entity->geoJSON;
            }

            if (!$this->MapItems->save($mapEntity)) {
                Log::write("debug", "Failed to save map item for zone: ".$entity);
                Log::write("debug", $mapEntity->errors());
            } else {
//                Log::write("debug", $entity);
                $entity->map_items = [ $mapEntity ];
                $entity->dirty("map_items");
                $entity->map_item_id = $mapEntity->id;
            }
        }
    }
}

