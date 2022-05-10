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
    'CO2_ppm' => 43,
    'raw_vwc' => 44,
    'raw_soil_temperature' => 49,
    'soil_temperature' => 39,
    'eos_alert' => 45,
    'GWC' => 46,
    'lux' => 47,
    'raw_soil_moisture' => 48,
    'soil_pH' => 50,
    'leaf_temperature' => 51,
    'leaf_moisture' => 52,
    'soil_conductivity' => 32,
    'soil_moisture' => 53,
  ];

  # This function is called by GrowpulseShell, it pulls the most recent data, and processes it.
  public function poll($shell)
  {
    $this->Devices = TableRegistry::get("Devices");
    $this->Sensors = TableRegistry::get("Sensors");

    # Store recent data in bulk
    $start = new \DateTime('-5 minutes');
    $end = new \DateTime();
    $data = $this->query($start, $end);
    $this->processBulkData($data, $shell);

    # Query for the most recent datapoints only
    $latest = $this->latest();
    # Submit the latest data points for rendering on the dashboard map.
    $messageQueueWrapper = new MessageQueueWrapper();
    $messages = [];
    foreach ($latest as $dataPoint) {
      # Get Device ID for Inifisense ID
      $device = $this->Devices->findByApiId($dataPoint[0])->first();
      if (!$device) {
        $shell->out("No device found for: " . $dataPoint[0] . "\n");
        continue;
      }
      $shell->out("Got device " . $device->id . " for api_id " . $dataPoint[0]);

      $this->Devices->updateDeviceInfo($device, ['id' => $device->id]);

      $sensor = $this->getSensorForDataPoint($dataPoint, $device, $shell);
      if (!$sensor) {
        $shell->out("No sensor found!!");
        continue;
      }
      $sensorTypeId = $this->infisenseSensorTypes[$dataPoint[1]];
      $dataType = $this->Sensors->enumKeyToValue('sensor_data_type', $sensorTypeId);
      $calibratedValue = $this->Devices->calibrate($sensor, $dataPoint[4]);

      $json = json_encode(array(array(
        'value' => (float) $calibratedValue,
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

  public function getSensorForDataPoint($dataPoint, $device, $shell)
  {
    $this->MapItems = TableRegistry::get("map_items");
    # Look up sensor type ID
    if (!isset($this->infisenseSensorTypes[$dataPoint[1]])) {
      return false;
    }
    $sensorTypeId = $this->infisenseSensorTypes[$dataPoint[1]];

    # Get Sensor for sensor type
    $sensor = $this->Sensors->find('all', [
      'conditions' => [
        'device_id' => $device->id,
        'sensor_type_id' => $sensorTypeId
      ]
    ])->first();
    $shell->out("Found sensor.");
    // $shell->out($sensor);
    if (!$sensor) {
      $shell->out("No sensor, create one");
      $sensor = $this->Sensors->newEntity();
      $sensor->device_id = $device->id;
      $sensor->sensor_type_id = $sensorTypeId;
      $sensor->label = 'Device ' . $device->id . ' - ' . $this->Sensors->enumKeyToValue('sensor_type', $sensorTypeId);
      $sensor->status = 1;
      $sensor->map_item_id = $device->map_item_id;
      $sensor->floorplan_id = 1;
      $sensor->dontMap = true;
      $this->Sensors->save($sensor);
      $shell->out($sensor);
    }
    return $sensor;
  }

  public function query($start, $end)
  {
    // return json_decode('{"index": [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86], "columns": ["device_id", "point_type", "units", "tmst", "value"], "data": [["70b3d57ba0000632", "co2_sensor_temperature", "celsius", "2020-12-02 20:31:55", 27.78], ["70b3d57ba0000632", "co2_concentration_lpf", "ppm", "2020-12-02 20:31:55", 614.0], ["70b3d57ba0000632", "co2_concentration", "ppm", "2020-12-02 20:31:55", 619.0], ["70b3d57ba0000632", "barometric_pressure", "Pa", "2020-12-02 20:31:55", 99636.0], ["70b3d57ba0000632", "battery_level", "volts", "2020-12-02 20:31:55", 2.911], ["70b3d57ba0000632", "relative_humidity", "%", "2020-12-02 20:31:55", 24.3478], ["70b3d57ba0000632", "temperature", "celsius", "2020-12-02 20:31:55", 27.7082], ["70b3d57ba0000632", "capacitor_voltage_1", "volts", "2020-12-02 20:31:55", 3.04], ["70b3d57ba0000632", "barometer_temperature", "celsius", "2020-12-02 20:31:55", 27.48], ["70b3d57ba0000632", "raw_ir_reading_lpf", "NA", "2020-12-02 20:31:55", 37783.0], ["70b3d57ba0000632", "raw_ir_reading", "NA", "2020-12-02 20:31:55", 37758.0], ["70b3d57ba0000632", "co2_sensor_status", "NA", "2020-12-02 20:31:55", 0.0], ["70b3d57ba0000632", "capacitor_voltage_2", "volts", "2020-12-02 20:31:55", 3.006], ["70b3d57ba0000632", "rssi", "dBm", "2020-12-02 20:31:55", -32.0], ["70b3d57ba00014e8", "battery_level", "volts", "2020-12-02 20:32:28", 3.059], ["70b3d57ba00014e8", "photosynthetically_active_radiation", "uMol/m2s", "2020-12-02 20:32:28", 10.9863], ["70b3d57ba00014e8", "rssi", "dBm", "2020-12-02 20:32:28", -51.0], ["0025ca0a0000ca54", "temp", "celsius", "2020-12-02 20:32:33", 19.9], ["0025ca0a0000ca54", "battery_level", "%", "2020-12-02 20:32:33", 60.0], ["0025ca0a0000ca54", "rssi", "dBm", "2020-12-02 20:32:33", -79.0], ["0025ca0a0000ca54", "relative_humidity", "%", "2020-12-02 20:32:33", 50.0], ["70b3d57ba000064c", "co2_concentration", "ppm", "2020-12-02 20:32:58", 789.0], ["70b3d57ba000064c", "barometric_pressure", "Pa", "2020-12-02 20:32:58", 90260.0], ["70b3d57ba000064c", "barometer_temperature", "celsius", "2020-12-02 20:32:58", 28.16], ["70b3d57ba000064c", "relative_humidity", "%", "2020-12-02 20:32:58", 33.2456], ["70b3d57ba000064c", "temperature", "celsius", "2020-12-02 20:32:58", 28.0594], ["70b3d57ba000064c", "rssi", "dBm", "2020-12-02 20:32:58", -36.0], ["70b3d57ba000064c", "capacitor_voltage_1", "volts", "2020-12-02 20:32:58", 3.023], ["70b3d57ba000064c", "battery_level", "volts", "2020-12-02 20:32:58", 3.027], ["70b3d57ba000064c", "co2_sensor_temperature", "celsius", "2020-12-02 20:32:58", 28.14], ["70b3d57ba000064c", "co2_concentration_lpf", "ppm", "2020-12-02 20:32:58", 767.0], ["70b3d57ba000064c", "capacitor_voltage_2", "volts", "2020-12-02 20:32:58", 2.99], ["70b3d57ba000064c", "raw_ir_reading", "NA", "2020-12-02 20:32:58", 36592.0], ["70b3d57ba000064c", "raw_ir_reading_lpf", "NA", "2020-12-02 20:32:58", 36648.0], ["70b3d57ba000064c", "co2_sensor_status", "NA", "2020-12-02 20:32:58", 0.0], ["647fda0000004890", "light_intensity", "a value between 0 and 64, inclusive", "2020-12-02 20:33:17", 1.0], ["647fda0000004890", "rssi", "dBm", "2020-12-02 20:33:17", -43.0], ["647fda0000004890", "temp", "celsius", "2020-12-02 20:33:17", 27.9], ["647fda0000004890", "relative_humidity", "%", "2020-12-02 20:33:17", 24.5], ["647fda0000004890", "battery_level", "volts", "2020-12-02 20:33:17", 3.09], ["0025ca0a0000ca40", "rssi", "dBm", "2020-12-02 20:33:37", -59.0], ["0025ca0a0000ca40", "relative_humidity", "%", "2020-12-02 20:33:37", 42.0], ["0025ca0a0000ca40", "temp", "celsius", "2020-12-02 20:33:37", 23.2], ["0025ca0a0000ca40", "battery_level", "%", "2020-12-02 20:33:37", 60.0], ["70b3d57ba00014e7", "rssi", "dBm", "2020-12-02 20:33:59", -36.0], ["70b3d57ba00014e7", "battery_level", "volts", "2020-12-02 20:33:59", 3.002], ["70b3d57ba00014e7", "photosynthetically_active_radiation", "uMol/m2s", "2020-12-02 20:33:59", 0.0], ["70b3d57ba000064b", "barometric_pressure", "Pa", "2020-12-02 20:34:18", 90222.0], ["70b3d57ba000064b", "rssi", "dBm", "2020-12-02 20:34:18", -51.0], ["70b3d57ba000064b", "barometer_temperature", "celsius", "2020-12-02 20:34:18", 19.17], ["70b3d57ba000064b", "relative_humidity", "%", "2020-12-02 20:34:18", 30.1271], ["70b3d57ba000064b", "temperature", "celsius", "2020-12-02 20:34:18", 19.2702], ["70b3d57ba000064b", "raw_ir_reading", "NA", "2020-12-02 20:34:18", 38716.0], ["70b3d57ba000064b", "battery_level", "volts", "2020-12-02 20:34:18", 3.018], ["70b3d57ba000064b", "capacitor_voltage_2", "volts", "2020-12-02 20:34:18", 2.987], ["70b3d57ba000064b", "capacitor_voltage_1", "volts", "2020-12-02 20:34:18", 3.022], ["70b3d57ba000064b", "co2_sensor_temperature", "celsius", "2020-12-02 20:34:18", 19.66], ["70b3d57ba000064b", "co2_concentration_lpf", "ppm", "2020-12-02 20:34:18", 464.0], ["70b3d57ba000064b", "co2_concentration", "ppm", "2020-12-02 20:34:18", 472.0], ["70b3d57ba000064b", "co2_sensor_status", "NA", "2020-12-02 20:34:18", 0.0], ["70b3d57ba000064b", "raw_ir_reading_lpf", "NA", "2020-12-02 20:34:18", 38746.0], ["0025ca0a0000ca41", "rssi", "dBm", "2020-12-02 20:34:47", -68.0], ["0025ca0a0000ca41", "temp", "celsius", "2020-12-02 20:34:47", 18.7], ["0025ca0a0000ca41", "relative_humidity", "%", "2020-12-02 20:34:47", 47.0], ["0025ca0a0000ca41", "battery_level", "%", "2020-12-02 20:34:47", 60.0], ["0025ca0a0000ca3c", "battery_level", "%", "2020-12-02 20:34:54", 40.0], ["0025ca0a0000ca3c", "temp", "celsius", "2020-12-02 20:34:54", 26.7], ["0025ca0a0000ca3c", "rssi", "dBm", "2020-12-02 20:34:54", -44.0], ["0025ca0a0000ca3c", "relative_humidity", "%", "2020-12-02 20:34:54", 37.5], ["70b3d57ba000064d", "barometric_pressure", "Pa", "2020-12-02 20:35:19", 90268.0], ["70b3d57ba000064d", "co2_concentration", "ppm", "2020-12-02 20:35:19", 683.0], ["70b3d57ba000064d", "co2_concentration_lpf", "ppm", "2020-12-02 20:35:19", 689.0], ["70b3d57ba000064d", "co2_sensor_temperature", "celsius", "2020-12-02 20:35:19", 27.1], ["70b3d57ba000064d", "capacitor_voltage_1", "volts", "2020-12-02 20:35:19", 3.042], ["70b3d57ba000064d", "co2_sensor_status", "NA", "2020-12-02 20:35:19", 0.0], ["70b3d57ba000064d", "capacitor_voltage_2", "volts", "2020-12-02 20:35:19", 3.009], ["70b3d57ba000064d", "rssi", "dBm", "2020-12-02 20:35:19", -51.0], ["70b3d57ba000064d", "raw_ir_reading_lpf", "NA", "2020-12-02 20:35:19", 37512.0], ["70b3d57ba000064d", "battery_level", "volts", "2020-12-02 20:35:19", 3.069], ["70b3d57ba000064d", "temperature", "celsius", "2020-12-02 20:35:19", 27.1049], ["70b3d57ba000064d", "relative_humidity", "%", "2020-12-02 20:35:19", 31.5347], ["70b3d57ba000064d", "barometer_temperature", "celsius", "2020-12-02 20:35:19", 27.17], ["70b3d57ba000064d", "raw_ir_reading", "NA", "2020-12-02 20:35:19", 37538.0], ["0025ca0a0000ca80", "rssi", "dBm", "2020-12-02 20:36:13", -47.0], ["0025ca0a0000ca80", "relative_humidity", "%", "2020-12-02 20:36:13", 32.5], ["0025ca0a0000ca80", "battery_level", "%", "2020-12-02 20:36:13", 60.0], ["0025ca0a0000ca80", "temp", "celsius", "2020-12-02 20:36:13", 29.6]]}')
    //   ->data;
    return $this->callApi("/data/?start_time=" . urlencode($start->format("Y-m-d H:i:s")) . "&end_time=" . urlencode($end->format("Y-m-d H:i:s")) . "&return_type=json");
  }

  public function latest()
  {
    // return json_decode('{"index": [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 100, 101, 102, 103, 104], "columns": ["device_id", "point_type", "units", "tmst", "value"], "data": [["70b3d57ba00014ae", "soil_temp", "celsius", "2020-12-02 20:27:02", 28.0], ["70b3d57ba00014ae", "volumetric_water_content", "m3/m3", "2020-12-02 20:27:02", 0.0102228], ["70b3d57ba00014ae", "rssi", "dBm", "2020-12-02 20:27:02", -38.0], ["70b3d57ba00014ae", "electrical_conductivity", "muS/cm", "2020-12-02 20:27:02", 0.0], ["70b3d57ba00014ae", "battery_level", "volts", "2020-12-02 20:27:02", 2.91], ["70b3d57ba00014ae", "dielectric_permittivity", "NA", "2020-12-02 20:27:02", 1.29382], ["70b3d57ba00014ea", "battery_level", "volts", "2020-12-02 20:28:03", 3.029], ["70b3d57ba00014ea", "volumetric_water_content", "m3/m3", "2020-12-02 20:28:03", 0.00529651], ["70b3d57ba00014ea", "dielectric_permittivity", "NA", "2020-12-02 20:28:03", 1.13153], ["70b3d57ba00014ea", "soil_temp", "celsius", "2020-12-02 20:28:03", 24.3], ["70b3d57ba00014ea", "electrical_conductivity", "muS/cm", "2020-12-02 20:28:03", 1.0], ["70b3d57ba00014ea", "rssi", "dBm", "2020-12-02 20:28:03", -30.0], ["70b3d57ba00014e6", "photosynthetically_active_radiation", "uMol/m2s", "2020-12-02 20:29:13", 0.0], ["70b3d57ba00014e6", "battery_level", "volts", "2020-12-02 20:29:13", 3.005], ["70b3d57ba00014e6", "rssi", "dBm", "2020-12-02 20:29:13", -41.0], ["70b3d57ba00014e9", "battery_level", "volts", "2020-12-02 20:30:31", 3.093], ["70b3d57ba00014e9", "photosynthetically_active_radiation", "uMol/m2s", "2020-12-02 20:30:31", 3.20435], ["70b3d57ba00014e9", "rssi", "dBm", "2020-12-02 20:30:31", -51.0], ["70b3d57ba0000632", "temperature", "celsius", "2020-12-02 20:31:55", 27.7082], ["70b3d57ba0000632", "rssi", "dBm", "2020-12-02 20:31:55", -32.0], ["70b3d57ba0000632", "relative_humidity", "%", "2020-12-02 20:31:55", 24.3478], ["70b3d57ba0000632", "raw_ir_reading_lpf", "NA", "2020-12-02 20:31:55", 37783.0], ["70b3d57ba0000632", "raw_ir_reading", "NA", "2020-12-02 20:31:55", 37758.0], ["70b3d57ba0000632", "co2_sensor_temperature", "celsius", "2020-12-02 20:31:55", 27.78], ["70b3d57ba0000632", "co2_concentration_lpf", "ppm", "2020-12-02 20:31:55", 614.0], ["70b3d57ba0000632", "co2_concentration", "ppm", "2020-12-02 20:31:55", 619.0], ["70b3d57ba0000632", "capacitor_voltage_2", "volts", "2020-12-02 20:31:55", 3.006], ["70b3d57ba0000632", "capacitor_voltage_1", "volts", "2020-12-02 20:31:55", 3.04], ["70b3d57ba0000632", "co2_sensor_status", "NA", "2020-12-02 20:31:55", 0.0], ["70b3d57ba0000632", "barometric_pressure", "Pa", "2020-12-02 20:31:55", 99636.0], ["70b3d57ba0000632", "barometer_temperature", "celsius", "2020-12-02 20:31:55", 27.48], ["70b3d57ba0000632", "battery_level", "volts", "2020-12-02 20:31:55", 2.911], ["70b3d57ba00014e8", "rssi", "dBm", "2020-12-02 20:32:28", -51.0], ["70b3d57ba00014e8", "photosynthetically_active_radiation", "uMol/m2s", "2020-12-02 20:32:28", 10.9863], ["70b3d57ba00014e8", "battery_level", "volts", "2020-12-02 20:32:28", 3.059], ["0025ca0a0000ca54", "relative_humidity", "%", "2020-12-02 20:32:33", 50.0], ["0025ca0a0000ca54", "rssi", "dBm", "2020-12-02 20:32:33", -79.0], ["0025ca0a0000ca54", "temp", "celsius", "2020-12-02 20:32:33", 19.9], ["0025ca0a0000ca54", "battery_level", "%", "2020-12-02 20:32:33", 60.0], ["70b3d57ba000064c", "rssi", "dBm", "2020-12-02 20:32:58", -36.0], ["70b3d57ba000064c", "raw_ir_reading", "NA", "2020-12-02 20:32:58", 36592.0], ["70b3d57ba000064c", "co2_sensor_temperature", "celsius", "2020-12-02 20:32:58", 28.14], ["70b3d57ba000064c", "co2_sensor_status", "NA", "2020-12-02 20:32:58", 0.0], ["70b3d57ba000064c", "co2_concentration_lpf", "ppm", "2020-12-02 20:32:58", 767.0], ["70b3d57ba000064c", "co2_concentration", "ppm", "2020-12-02 20:32:58", 789.0], ["70b3d57ba000064c", "capacitor_voltage_2", "volts", "2020-12-02 20:32:58", 2.99], ["70b3d57ba000064c", "capacitor_voltage_1", "volts", "2020-12-02 20:32:58", 3.023], ["70b3d57ba000064c", "battery_level", "volts", "2020-12-02 20:32:58", 3.027], ["70b3d57ba000064c", "barometric_pressure", "Pa", "2020-12-02 20:32:58", 90260.0], ["70b3d57ba000064c", "barometer_temperature", "celsius", "2020-12-02 20:32:58", 28.16], ["70b3d57ba000064c", "relative_humidity", "%", "2020-12-02 20:32:58", 33.2456], ["70b3d57ba000064c", "raw_ir_reading_lpf", "NA", "2020-12-02 20:32:58", 36648.0], ["70b3d57ba000064c", "temperature", "celsius", "2020-12-02 20:32:58", 28.0594], ["647fda0000004890", "temp", "celsius", "2020-12-02 20:33:17", 27.9], ["647fda0000004890", "rssi", "dBm", "2020-12-02 20:33:17", -43.0], ["647fda0000004890", "relative_humidity", "%", "2020-12-02 20:33:17", 24.5], ["647fda0000004890", "light_intensity", "a value between 0 and 64, inclusive", "2020-12-02 20:33:17", 1.0], ["647fda0000004890", "battery_level", "volts", "2020-12-02 20:33:17", 3.09], ["0025ca0a0000ca40", "battery_level", "%", "2020-12-02 20:33:37", 60.0], ["0025ca0a0000ca40", "relative_humidity", "%", "2020-12-02 20:33:37", 42.0], ["0025ca0a0000ca40", "rssi", "dBm", "2020-12-02 20:33:37", -59.0], ["0025ca0a0000ca40", "temp", "celsius", "2020-12-02 20:33:37", 23.2], ["70b3d57ba00014e7", "rssi", "dBm", "2020-12-02 20:33:59", -36.0], ["70b3d57ba00014e7", "battery_level", "volts", "2020-12-02 20:33:59", 3.002], ["70b3d57ba00014e7", "photosynthetically_active_radiation", "uMol/m2s", "2020-12-02 20:33:59", 0.0], ["70b3d57ba000064b", "temperature", "celsius", "2020-12-02 20:34:18", 19.2702], ["70b3d57ba000064b", "relative_humidity", "%", "2020-12-02 20:34:18", 30.1271], ["70b3d57ba000064b", "barometer_temperature", "celsius", "2020-12-02 20:34:18", 19.17], ["70b3d57ba000064b", "barometric_pressure", "Pa", "2020-12-02 20:34:18", 90222.0], ["70b3d57ba000064b", "battery_level", "volts", "2020-12-02 20:34:18", 3.018], ["70b3d57ba000064b", "capacitor_voltage_1", "volts", "2020-12-02 20:34:18", 3.022], ["70b3d57ba000064b", "capacitor_voltage_2", "volts", "2020-12-02 20:34:18", 2.987], ["70b3d57ba000064b", "co2_concentration", "ppm", "2020-12-02 20:34:18", 472.0], ["70b3d57ba000064b", "rssi", "dBm", "2020-12-02 20:34:18", -51.0], ["70b3d57ba000064b", "co2_sensor_status", "NA", "2020-12-02 20:34:18", 0.0], ["70b3d57ba000064b", "co2_sensor_temperature", "celsius", "2020-12-02 20:34:18", 19.66], ["70b3d57ba000064b", "raw_ir_reading", "NA", "2020-12-02 20:34:18", 38716.0], ["70b3d57ba000064b", "raw_ir_reading_lpf", "NA", "2020-12-02 20:34:18", 38746.0], ["70b3d57ba000064b", "co2_concentration_lpf", "ppm", "2020-12-02 20:34:18", 464.0], ["0025ca0a0000ca41", "battery_level", "%", "2020-12-02 20:34:47", 60.0], ["0025ca0a0000ca41", "relative_humidity", "%", "2020-12-02 20:34:47", 47.0], ["0025ca0a0000ca41", "rssi", "dBm", "2020-12-02 20:34:47", -68.0], ["0025ca0a0000ca41", "temp", "celsius", "2020-12-02 20:34:47", 18.7], ["0025ca0a0000ca3c", "battery_level", "%", "2020-12-02 20:34:54", 40.0], ["0025ca0a0000ca3c", "relative_humidity", "%", "2020-12-02 20:34:54", 37.5], ["0025ca0a0000ca3c", "rssi", "dBm", "2020-12-02 20:34:54", -44.0], ["0025ca0a0000ca3c", "temp", "celsius", "2020-12-02 20:34:54", 26.7], ["70b3d57ba000064d", "relative_humidity", "%", "2020-12-02 20:35:19", 31.5347], ["70b3d57ba000064d", "barometric_pressure", "Pa", "2020-12-02 20:35:19", 90268.0], ["70b3d57ba000064d", "rssi", "dBm", "2020-12-02 20:35:19", -51.0], ["70b3d57ba000064d", "capacitor_voltage_1", "volts", "2020-12-02 20:35:19", 3.042], ["70b3d57ba000064d", "capacitor_voltage_2", "volts", "2020-12-02 20:35:19", 3.009], ["70b3d57ba000064d", "co2_concentration", "ppm", "2020-12-02 20:35:19", 683.0], ["70b3d57ba000064d", "co2_concentration_lpf", "ppm", "2020-12-02 20:35:19", 689.0], ["70b3d57ba000064d", "co2_sensor_status", "NA", "2020-12-02 20:35:19", 0.0], ["70b3d57ba000064d", "co2_sensor_temperature", "celsius", "2020-12-02 20:35:19", 27.1], ["70b3d57ba000064d", "raw_ir_reading", "NA", "2020-12-02 20:35:19", 37538.0], ["70b3d57ba000064d", "raw_ir_reading_lpf", "NA", "2020-12-02 20:35:19", 37512.0], ["70b3d57ba000064d", "temperature", "celsius", "2020-12-02 20:35:19", 27.1049], ["70b3d57ba000064d", "battery_level", "volts", "2020-12-02 20:35:19", 3.069], ["70b3d57ba000064d", "barometer_temperature", "celsius", "2020-12-02 20:35:19", 27.17], ["0025ca0a0000ca80", "temp", "celsius", "2020-12-02 20:36:13", 29.6], ["0025ca0a0000ca80", "rssi", "dBm", "2020-12-02 20:36:13", -47.0], ["0025ca0a0000ca80", "relative_humidity", "%", "2020-12-02 20:36:13", 32.5], ["0025ca0a0000ca80", "battery_level", "%", "2020-12-02 20:36:13", 60.0]]}')
    //   ->data;
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
    print_r($response);
    if (property_exists($jsonResponse, 'message')) {
      print_r($response);
      return false;
    }
    return $jsonResponse->data;
  }

  # Store many points to the Timeseries Database.
  public function processBulkData($data, $shell)
  {
    $this->Devices = TableRegistry::get("Devices");
    $this->Sensors = TableRegistry::get("Sensors");

    # Store Infisense Data in InfluxDB
    $points = [];
    foreach ($data as $dataPoint) {
      $api_id = $dataPoint[0];
      $device = $this->Devices->findByApiId($api_id)->first();

      if (!$device) {
        continue;
      }
      $shell->out("Returned api_id: " . $device->api_id); //die();
      $shell->out("194 Found device " . $device->id . " for api_id " . $dataPoint[0]);
      $sensor = $this->getSensorForDataPoint($dataPoint, $device, $shell);
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
