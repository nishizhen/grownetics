<?php

namespace App\Test\TestCase\Integration;

use Cake\TestSuite\IntegrationTestCase;
use Cake\ORM\TableRegistry;
use RuntimeException;

class DefaultZoneRulesTest extends IntegrationTestCase {

    public $fixtures = [
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
        'app.appliance_templates',
        'app.appliances',
        'app.map_items',
        'app.map_item_types',
        'app.appliance_types',
        'app.set_points',
        'app.harvest_batches',
        'app.roles',
        'app.users'
    ];

    public function tearDown()
    {
        restore_error_handler();
    }

    public function setUp()
    {
        parent::setUp();
        set_error_handler(function ($errno, $errstr, $errfile, $errline) {
            debug_backtrace();
            throw new RuntimeException($errstr . " on line " . $errline . " in file " . $errfile);
        });

        $Zones = TableRegistry::get('Zones');
        $Devices = TableRegistry::get('Devices');
        $Sensors = TableRegistry::get('Sensors');
        $SensorsZones = TableRegistry::get("SensorsZones");

        $zone = $Zones->newEntity([
            'label' => 'Clone 1 Test',
            'plant_zone_type_id' => $Zones->enumValueToKey('plant_zone_types', 'Clone'),
            'dontMap' => true
        ]);
        $Zones->save($zone);
        $this->zone_id = $zone->id;

        $air_temp_sensor_type_id = $Sensors->enumValueToKey('sensor_type','Air Temperature');
        $humidity_sensor_type_id = $Sensors->enumValueToKey('sensor_type','Humidity');
        $co2_sensor_type_id =  $Sensors->enumValueToKey('sensor_type','Co2');

        $device1 = $Devices->newEntity(array(
            'status' => 1,
            'doCreateSensors' => false,
            'latitude' => 1000.99999,
            'longitude' => 0.0000002,
            'label' => '3D Crop Sensor 1',
            'floorplan_id' => 0
        ));
        $Devices->save($device1);
        $this->deviceId1 = $device1->api_id;

        $sensor1 = $Sensors->newEntity(array(
            'label' => 'Low - Humidity Sensor',
            'status' => 1,
            'sensor_pin' => 'M1',
            'device_id' => $device1->id,
            'sensor_type_id' => $humidity_sensor_type_id,
            'floorplan_id' => 0
        ));
        $Sensors->save($sensor1);
        $SensorsZones->save($SensorsZones->newEntity(['zone_id'=>$zone->id,'sensor_id'=>$sensor1->id]));

        $sensor2 = $Sensors->newEntity(array(
            'label' => 'Low - Temperature Sensor',
            'status' => 1,
            'sensor_pin' => 'M1',
            'device_id' => $device1->id,
            'sensor_type_id' => $air_temp_sensor_type_id,
            'floorplan_id' => 0
        ));
        $Sensors->save($sensor2);
        $SensorsZones->save($SensorsZones->newEntity(['zone_id'=>$zone->id,'sensor_id'=>$sensor2->id]));

        $sensor3 = $Sensors->newEntity(array(
            'label' => 'High - Humidity Sensor',
            'status' => 1,
            'sensor_pin' => 'M2',
            'device_id' => $device1->id,
            'sensor_type_id' => $humidity_sensor_type_id,
            'floorplan_id' => 0
        ));
        $Sensors->save($sensor3);
        $SensorsZones->save($SensorsZones->newEntity(['zone_id'=>$zone->id,'sensor_id'=>$sensor3->id]));

        $sensor4 = $Sensors->newEntity(array(
            'label' => 'High - Temperature Sensor',
            'status' => 1,
            'sensor_pin' => 'M2',
            'device_id' => $device1->id,
            'sensor_type_id' => $air_temp_sensor_type_id,
            'floorplan_id' => 0
        ));
        $Sensors->save($sensor4);
        $SensorsZones->save($SensorsZones->newEntity(['zone_id'=>$zone->id,'sensor_id'=>$sensor4->id]));

        $sensor5 = $Sensors->newEntity(array(
            'label' => 'High - Co2 Sensor',
            'status' => 1,
            'sensor_pin' => 'M3',
            'device_id' => $device1->id,
            'sensor_type_id' => $co2_sensor_type_id,
            'floorplan_id' => 0
        ));
        $Sensors->save($sensor5);
        $SensorsZones->save($SensorsZones->newEntity(['zone_id'=>$zone->id,'sensor_id'=>$sensor5->id]));

    }

    public function testZoneAlert() {

        $this->Notifications = TableRegistry::get('Notifications');
        $this->RuleActions = TableRegistry::get('RuleActions');
        $this->RuleConditions = TableRegistry::get('RuleConditions');
        $this->Rules = TableRegistry::get('Rules');
        $this->Zones = TableRegistry::get('Zones');
        $this->Sensors = TableRegistry::get('Sensors');

        #Generate Rule sets for each Zone for each Data type based on Grownetics Default Rules. E.g.
        # Zone_type = Veg
        # Zone: Veg_Room_1 Hum Alarm Lo/Hi, Temp Alarm Lo/Hi, Co2 Alarm Lo/Hi, Temp Emergency Shutoff Lights
        # 7 Rules for each Zone
        $this->Rules->generateFromDefaultRules();

        $this->sensor_type_id = $this->Sensors->enumValueToKey('sensor_type','Air Temperature');

        //Below threshold
        $data = array('q' => '{"id":' . $this->deviceId1 . ',"st":1,"d":"[M1:10.2-83.34],[M2:40.2-82.18],[M3:400]"}');
        $this->post(
            '/api/raw',
            array('data' => $data, 'method' => 'get', 'return' => 'vars')
        );
        $this->assertEquals("{}", $this->_response->body());
        $this->Rules->processTimedRules();
        $this->Zones->processData();
        $this->Zones->processRules();

        //Below threshold
        $data = array('q' => '{"id":' . $this->deviceId1 . ',"st":1,"d":"[M1:10.2-84.34],[M2:40.2-82.18],[M3:400]"}');
        $this->post(
            '/api/raw',
            array('data' => $data, 'method' => 'get', 'return' => 'vars')
        );
        $this->assertEquals("{}", $this->_response->body());
        $this->Rules->processTimedRules();
        $this->Zones->processData();
        $this->Zones->processRules();

        //Triggered
        $data = array('q' => '{"id":' . $this->deviceId1 . ',"st":1,"d":"[M1:10.2-87.34],[M2:40.2-88.18],[M3:400]"}');
        $this->post(
            '/api/raw',
            array('data' => $data, 'method' => 'get', 'return' => 'vars')
        );
        $this->assertEquals("{}", $this->_response->body());
        $this->Rules->processTimedRules();
        $this->Zones->processData();
        $this->Zones->processRules();
        $notification = $this->Notifications->find('all', ['conditions' => ['notification_level' => 3]])->first();
        $this->assertContains('Air Temperature reading from Zone Clone 1 Test has exceeded the rule threshold of 185',
           $notification->message
        );
        
        //Below threshold
        $data = array('q' => '{"id":' . $this->deviceId1 . ',"st":1,"d":"[M1:10.2-82.34],[M2:40.2-81.18],[M3:400]"}');
        $this->post(
            '/api/raw',
            array('data' => $data, 'method' => 'get', 'return' => 'vars')
        );
        $this->assertEquals("{}", $this->_response->body());
        $this->Rules->processTimedRules();
        $this->Zones->processData();
        $this->Zones->processRules();

        //Overwrite rule_condition
        $rule_condition = $this->RuleConditions->find('all', ['conditions' => ['data_id' => $this->zone_id, 'sensor_type' => $this->sensor_type_id, 'data_source' => $this->RuleConditions->enumValueToKey('data_source', 'Zone'), 'is_default' => false, 'operator' => 0]])->first();
        $rule_condition->trigger_threshold = 80;
        $rule_condition->reset_threshold = 79;
        $rule_condition->pending_time = 1;
        $this->RuleConditions->save($rule_condition);

        $rule_action = $this->RuleActions->find('all', ['conditions' => ['rule_id' => $rule_condition->rule_id]])->first();
        $rule_action->notification_level = 4;
        $this->RuleActions->save($rule_action);

        //Below new threshold
        $data = array('q' => '{"id":' . $this->deviceId1 . ',"st":1,"d":"[M1:10.2-75.34],[M2:40.2-76.18],[M3:400]"}');
        $this->post(
            '/api/raw',
            array('data' => $data, 'method' => 'get', 'return' => 'vars')
        );
        $this->assertEquals("{}", $this->_response->body());
        $this->Rules->processTimedRules();
        $this->Zones->processData();
        $this->Zones->processRules();

        //Below new threshold
        $data = array('q' => '{"id":' . $this->deviceId1 . ',"st":1,"d":"[M1:10.2-77.34],[M2:40.2-78.18],[M3:400]"}');
        $this->post(
            '/api/raw',
            array('data' => $data, 'method' => 'get', 'return' => 'vars')
        );
        $this->assertEquals("{}", $this->_response->body());
        $this->Rules->processTimedRules();
        $this->Zones->processData();
        $this->Zones->processRules();

        //Triggered
        $data = array('q' => '{"id":' . $this->deviceId1 . ',"st":1,"d":"[M1:10.2-81.34],[M2:40.2-83.18],[M3:400]"}');
        $this->post(
            '/api/raw',
            array('data' => $data, 'method' => 'get', 'return' => 'vars')
        );
        $this->assertEquals("{}", $this->_response->body());
        $this->Rules->processTimedRules();
        $this->Zones->processData();
        $this->Zones->processRules();

        //Triggered
        $data = array('q' => '{"id":' . $this->deviceId1 . ',"st":1,"d":"[M1:10.2-87.34],[M2:40.2-84.18],[M3:400]"}');
        $this->post(
            '/api/raw',
            array('data' => $data, 'method' => 'get', 'return' => 'vars')
        );
        $this->assertEquals("{}", $this->_response->body());
        $this->Rules->processTimedRules();
        $this->Zones->processData();
        $this->Zones->processRules();
        $notification2 = $this->Notifications->find('all', ['conditions' => ['notification_level' => 4]])->first();
        $this->assertContains('Air Temperature reading from Zone Clone 1 Test has exceeded the rule threshold of 176',
           $notification2->message
        );
        
    }

}