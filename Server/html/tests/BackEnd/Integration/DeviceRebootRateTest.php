<?php
namespace App\Test\TestCase\Integration;

use Cake\TestSuite\IntegrationTestCase;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use RuntimeException;
use Cake\Cache\Cache;

/**
 * App\Model\Table\DeviceRebootRate Test Case
 */
class DeviceRebootRateTest extends IntegrationTestCase
{

    public $fixtures = array(
        'app.floorplans',
        'app.notifications',
        'app.devices',
        'app.sensors',
        'app.sensors_zones',
        'app.zones',
        'app.rules',
        'app.outputs',
        'app.rule_actions',
        'app.rule_conditions',
        'app.rule_action_targets',
        'app.set_points',
        'app.map_items',
        'app.map_item_types'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        set_error_handler(function ($errno, $errstr, $errfile, $errline) {
            debug_backtrace();
            throw new RuntimeException($errstr . " on line " . $errline . " in file " . $errfile);
        });
        $Devices = TableRegistry::get('Devices');
        $SensorsZones = TableRegistry::get("SensorsZones");
        $Sensors = TableRegistry::get('Sensors');
        $Zones = TableRegistry::get('Zones');

        $zone = $Zones->newEntity();
        $Zones->save($zone);

        $air_temp_sensor_type_id = $Sensors->enumValueToKey('sensor_type','Air Temperature');
        $humidity_sensor_type_id = $Sensors->enumValueToKey('sensor_type','Humidity');
        $co2_sensor_type_id =  $Sensors->enumValueToKey('sensor_type','Co2');


        $device = $Devices->newEntity([
            'doCreateSensors' => false,
            'latitude' => 1000.99999,
            'longitude' => 0.0000002,
            'label' => 'Rick',
            'floorplan_id' => 0,
            'reboot_rate' => 10,
            'status' => 1,
            'type' => 0
        ]);

        if ($Devices->save($device)) {
            $this->deviceId = $device->api_id;
        }

        $sensor1 = $Sensors->newEntity(array(
            'label' => 'Low - Humidity Sensor',
            'status' => 1,
            'sensor_pin' => 'M1',
            'device_id' => $device->id,
            'sensor_type_id' => $humidity_sensor_type_id,
            'floorplan_id' => 0
        ));
        $Sensors->save($sensor1);
        $SensorsZones->save($SensorsZones->newEntity(['zone_id'=>$zone->id,'sensor_id'=>$sensor1->id]));

        $sensor2 = $Sensors->newEntity(array(
            'label' => 'Low - Temperature Sensor',
            'status' => 1,
            'sensor_pin' => 'M1',
            'device_id' => $device->id,
            'sensor_type_id' => $air_temp_sensor_type_id,
            'floorplan_id' => 0
        ));
        $Sensors->save($sensor2);
        $SensorsZones->save($SensorsZones->newEntity(['zone_id'=>$zone->id,'sensor_id'=>$sensor2->id]));

        $sensor3 = $Sensors->newEntity(array(
            'label' => 'High - Humidity Sensor',
            'status' => 1,
            'sensor_pin' => 'M2',
            'device_id' => $device->id,
            'sensor_type_id' => $humidity_sensor_type_id,
            'floorplan_id' => 0
        ));
        $Sensors->save($sensor3);
        $SensorsZones->save($SensorsZones->newEntity(['zone_id'=>$zone->id,'sensor_id'=>$sensor3->id]));

        $sensor4 = $Sensors->newEntity(array(
            'label' => 'High - Temperature Sensor',
            'status' => 1,
            'sensor_pin' => 'M2',
            'device_id' => $device->id,
            'sensor_type_id' => $air_temp_sensor_type_id,
            'floorplan_id' => 0
        ));
        $Sensors->save($sensor4);
        $SensorsZones->save($SensorsZones->newEntity(['zone_id'=>$zone->id,'sensor_id'=>$sensor4->id]));

        $sensor5 = $Sensors->newEntity(array(
            'label' => 'High - Co2 Sensor',
            'status' => 1,
            'sensor_pin' => 'M3',
            'device_id' => $device->id,
            'sensor_type_id' => $co2_sensor_type_id,
            'floorplan_id' => 0
        ));
        $Sensors->save($sensor5);
        $SensorsZones->save($SensorsZones->newEntity(['zone_id'=>$zone->id,'sensor_id'=>$sensor5->id]));
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        restore_error_handler();
    }

    public function testRebootRate() {
        $Devices = TableRegistry::get('Devices');
        $shell = null;
        //Send data - Status = Active
        $data = array('q' => '{"id":' . $this->deviceId . ',"st":1,"d":"[M1:10.2-67.34],[M2:40.2-68.18],[M3:200]"}');
        $this->post(
            '/api/raw',
            array('data' => $data, 'method' => 'get', 'return' => 'vars')
        );
        $this->assertEquals("{}", $this->_response->body());
        $Devices->updateStatuses($shell);
        $this->assertEquals($Devices->enumValueToKey('status', 'Active'), Cache::read('device-'.$this->deviceId)['status']);

        $cachedDevice = Cache::read('device-'.$this->deviceId);
        $cachedDevice['last_message'] = date("Y-m-d H:i:s", time() - 700);
        Cache::write('device-' . $this->deviceId, $cachedDevice);
        
        $Devices->updateStatuses($shell);
        $this->assertEquals($Devices->enumValueToKey('status', 'Offline'), Cache::read('device-'.$this->deviceId)['status']);
        $data = array('q' => '{"id":' . $this->deviceId . ',"st":1,"d":"[M1:10.2-67.34],[M2:40.2-68.18],[M3:200]"}');
        $this->post(
            '/api/raw',
            array('data' => $data, 'method' => 'get', 'return' => 'vars')
        );
        $this->assertEquals("{}", $this->_response->body());
        //Get data - Status back to Active
        $Devices->updateStatuses($shell);
        $this->assertEquals($Devices->enumValueToKey('status', 'Active'), Cache::read('device-'.$this->deviceId)['status']);
    }

}
