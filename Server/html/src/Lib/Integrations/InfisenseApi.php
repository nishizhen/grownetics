<?php

namespace App\Lib\Integrations;

use Cake\ORM\TableRegistry;
use Cake\Cache\Cache;
use App\Lib\SystemEventRecorder;
use InfluxDB\Point;
use App\Lib\TimeSeriesWrapper;
use App\Lib\MessageQueueWrapper;

# This class scrapes data from our remote integrations
class InfisenseApi
{

  # Mapping of Infisense Sensor Types to our internal Sensor Types
  public $infisenseSensorTypes = [
    'barometer_temperature' => 22,
    'barometric_pressure' => 23,
    'battery_level' => 24,
    'capacitor_voltage_1' => 25,
    'capacitor_voltage_2' => 26,
    'co2_concentration_lpf' => 27,
    'co2_concentration' => 28,
    'co2_sensor_status' => 29,
    'co2_sensor_temperature' => 30,
    'dielectric_permittivity' => 31,
    'electrical_conductivity' => 32,
    'light_intensity' => 33,
    'photosynthetically_active_radiation' => 34,
    'raw_ir_reading' => 35,
    'raw_ir_reading_lpf' => 36,
    'relative_humidity' => 37,
    'rssi' => 38,
    'soil_temp' => 39,
    'temp' => 40,
    'temperature' => 41,
    'volumetric_water_content' => 42,
  ];

  # This function is called by GrowpulseShell, it pulls the most recent data, and processes it.
  public function poll()
  {
    $this->Devices = TableRegistry::get("Devices");
    $this->Sensors = TableRegistry::get("Sensors");

    # Store recent data in bulk
    $start = new \DateTime('-5 minutes');
    $end = new \DateTime();
    $data = $this->query($start, $end);
    $this->processBulkData($data);

    # Query for the most recent datapoints only
    $latest = $this->latest();
    # Submit the latest data points for rendering on the dashboard map.
    $messageQueueWrapper = new MessageQueueWrapper();
    $messages = [];
    foreach ($latest as $dataPoint) {
      # Get Device ID for Inifisense ID
      $device = $this->Devices->findByApiId($dataPoint[0])->first();
      if (!$device) {
        print_r("No device found for: " . $dataPoint[0] . "\n");
        continue;
      }
      $sensor = $this->getSensorForDataPoint($dataPoint, $device);
      $sensorTypeId = $this->infisenseSensorTypes[$dataPoint[1]];
      $dataType = $this->Sensors->enumKeyToValue('sensor_data_type', $sensorTypeId);
      $json = json_encode(array(array(
        'value' => (float) $dataPoint[4],
        'source_id' => $sensor->id,
        'source_type' => 0,
        'data_type' => $dataType,
        'sensor_type' => $sensorTypeId,
        'device_id' => $device->id,
        'created' => (string) date("Y-m-d H:i:s"),
        'facility_id' => (float) env('FACILITY_ID')
      )));
      array_push($messages, $json);
      Cache::write('sensor-value-' . $sensor->id, (float) $dataPoint[4]);
      Cache::write('sensor-time-' . $sensor->id, date("Y-m-d H:i:s"));
    }
    print_r("Pushing: ");
    print_r($messages);
    $messageQueueWrapper->send($messages, 'data.sensor');
  }

  public function getSensorForDataPoint($dataPoint, $device)
  {
    # Look up sensor type ID
    $sensorTypeId = $this->infisenseSensorTypes[$dataPoint[1]];

    # Get Sensor for sensor type
    $sensor = $this->Sensors->find('all', [
      'conditions' => [
        'device_id' => $device->id,
        'sensor_type_id' => $sensorTypeId
      ]
    ])->first();
    if (!$sensor) {
      $sensor = $this->Sensors->newEntity();
      $sensor->device_id = $device->id;
      $sensor->sensor_type_id = $sensorTypeId;
      $sensor->label = $this->Sensors->enumKeyToValue('sensor_type', $sensorTypeId);
      $sensor->status = 1;
      $sensor->map_item_id = $device->map_item_id;
      $sensor->floorplan_id = 1;
      $sensor->dontMap = true;
      $this->Sensors->save($sensor);
    }
    return $sensor;
  }

  public function query($start, $end)
  {
    return $this->callApi("/data/?start_time=" . urlencode($start->format("Y-m-d H:i:s")) . "&end_time=" . urlencode($end->format("Y-m-d H:i:s")) . "&return_type=json");
  }

  public function latest()
  {
    return $this->callApi("/data/latest?return_type=json");
  }

  public function callApi($url)
  {
    # Query Infisense
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://api.infisense.com" . $url,
      CURLOPT_HTTPHEADER => array(
        "x-api-key: " . env('INFISENSE_API_KEY'),
        "accept: application/json"
      ),
      CURLOPT_RETURNTRANSFER => true
    ));
    print_r("https://api.infisense.com" . $url . "\n\n");
    # Check that we got a valid response, and decode it.
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    $jsonResponse = json_decode($response);
    if (property_exists($jsonResponse, 'message')) {
      print_r($response);
      return false;
    }
    return $jsonResponse->data;
  }

  # Store many points to the Timeseries Database.
  public function processBulkData($data)
  {
    $this->Devices = TableRegistry::get("Devices");
    $this->Sensors = TableRegistry::get("Sensors");

    # Store Infisense Data in InfluxDB
    $points = [];
    foreach ($data as $dataPoint) {
      $device = $this->Devices->findByApiId($dataPoint[0])->first();
      if (!$device) {
        continue;
      }
      $sensor = $this->getSensorForDataPoint($dataPoint, $device);
      $sensorTypeId = $this->infisenseSensorTypes[$dataPoint[1]];
      $dataType = $this->Sensors->enumKeyToValue('sensor_data_type', $sensorTypeId);

      $points[] = new Point(
        'infisense', // name of the measurement
        (float) $dataPoint[4], // the measurement value
        [
          'source_type' => 0,
          'sensor_type' => $this->infisenseSensorTypes[$dataPoint[1]],
          'data_type' => $this->Sensors->enumKeyToValue('sensor_data_type', $this->infisenseSensorTypes[$dataPoint[1]]),
          'facility_id' => env('FACILITY_ID'),
          'source_id' => $sensor->id,
          'device_id' => $device->id,

        ],
        [], // optional additional fields
        strtotime($dataPoint[3])
      );
    }

    # Attempt to save the DataPoint to the local Time Series DB
    $tsw = new TimeSeriesWrapper();
    $tsw->store($points, 'integration_data');
  }
}



