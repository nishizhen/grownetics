<?php

namespace App\Test\TestCase\Integration;

use Cake\TestSuite\IntegrationTestCase;
use App\Model\Table\DevicesTable;
use Cake\ORM\TableRegistry;
use RuntimeException;

class ZoneHeaterRulesTest extends IntegrationTestCase
{

    public $fixtures = array(
        'app.floorplans',
        'app.notifications',
        'app.devices',
        'app.sensors',
        'app.sensors_zones',
        'app.batch_recipe_entries',
        'app.zones',
        'app.rules',
        'app.outputs',
        'app.rule_actions',
        'app.users',
        'app.roles',
        'app.rule_conditions',
        'app.rule_action_targets',
        'app.set_points',
        'app.map_items',
        'app.map_item_types'
    );

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


        $Devices = TableRegistry::get('Devices');
        $Sensors = TableRegistry::get('Sensors');
        $SensorsZones = TableRegistry::get('SensorsZones');
        $Rules = TableRegistry::get('Rules');
        $this->Notifications = TableRegistry::get('Notifications');
        $this->Zones = TableRegistry::get('Zones');
        $Outputs = TableRegistry::get('Outputs');
        $RuleConditions = TableRegistry::get('RuleConditions');
        $RuleActions = TableRegistry::get('RuleActions');
        $this->RuleActionTargets = TableRegistry::get('RuleActionTargets');
        $this->Users = TableRegistry::get('Users');
        $this->Users = TableRegistry::get('Roles');

        $device = $Devices->newEntity([
            'doCreateSensors' => false,
            'latitude' => 1000.99999,
            'longitude' => 0.0000002,
            'label' => '3D Crop Device',
            'floorplan_id' => 0
        ]);

        if ($Devices->save($device)) {
            $this->deviceId = $device->api_id;
        }

        $device = $Devices->newEntity([
            'doCreateSensors' => false,
            'latitude' => 9.0099999,
            'longitude' => 366.0000002,
            'label' => 'Heater Relay Output',
            'floorplan_id' => 0,
            'type' => $Devices->enumValueToKey('type','Control')
        ]);

        if ($Devices->save($device)) {
            $this->deviceId2 = $device->api_id;
        }

        $zone = $this->Zones->newEntity();

        if ($this->Zones->save($zone)) {
            $zone_id = $this->zone_id = $zone->id;
        }

        $air_temp_sensor_type_id = $Sensors->enumValueToKey('sensor_types','Air Temperature');
        $humidity_sensor_type_id = $Sensors->enumValueToKey('sensor_types','Humidity');
        $co2_sensor_type_id = $Sensors->enumValueToKey('sensor_types','Co2');

        $sensor = $Sensors->newEntity(array(
            'label' => 'Low - Humidity',
            'status' => $Sensors->enumValueToKey('status', 'Enabled'),
            'sensor_pin' => 'M1',
            'device_id' => $this->deviceId,
            'sensor_type_id' => $humidity_sensor_type_id,
            'floorplan_id' => 0
        ));
        $sensor1id = $Sensors->save($sensor)->id;
        $SensorsZones->save($SensorsZones->newEntity(['zone_id'=>$zone_id,'sensor_id'=>$sensor1id]));

        $sensor = $Sensors->newEntity(array(
            'label' => 'High - Air Temperature',
            'status' => $Sensors->enumValueToKey('status', 'Enabled'),
            'sensor_pin' => 'M1',
            'device_id' => $this->deviceId,
            'sensor_type_id' => $air_temp_sensor_type_id,
            'floorplan_id' => 0
        ));
        $sensor2id = $Sensors->save($sensor)->id;
        $SensorsZones->save($SensorsZones->newEntity(['zone_id'=>$zone_id,'sensor_id'=>$sensor2id]));

        $sensor = $Sensors->newEntity(array(
            'label' => 'High - Humidity',
            'sensor_pin' => 'M2',
            'device_id' => $this->deviceId,
            'status' => $Sensors->enumValueToKey('status', 'Enabled'),
            'sensor_type_id' => $humidity_sensor_type_id,
            'floorplan_id' => 0
        ));
        $sensor3id = $Sensors->save($sensor)->id;
        $SensorsZones->save($SensorsZones->newEntity(['zone_id'=>$zone_id,'sensor_id'=>$sensor3id]));

        $sensor = $Sensors->newEntity(array(
            'label' => 'High - Air Temperature',
            'sensor_pin' => 'M2',
            'device_id' => $this->deviceId,
            'status' => $Sensors->enumValueToKey('status', 'Enabled'),
            'sensor_type_id' => $air_temp_sensor_type_id,
            'floorplan_id' => 0
        ));
        $sensor4id = $Sensors->save($sensor)->id;
        $SensorsZones->save($SensorsZones->newEntity(['zone_id'=>$zone_id,'sensor_id'=>$sensor4id]));

        $sensor = $Sensors->newEntity(array(
            'label' => 'High - Co2',
            'status' => $Sensors->enumValueToKey('status', 'Enabled'),
            'sensor_pin' => 'M3',
            'device_id' => $this->deviceId,
            'zone_id' => $zone_id,
            'sensor_type_id' => $co2_sensor_type_id,
            'floorplan_id' => 0
        ));
        $sensor5id = $Sensors->save($sensor)->id;
        $SensorsZones->save($SensorsZones->newEntity(['zone_id'=>$zone_id,'sensor_id'=>$sensor5id]));

        $output = $Outputs->newEntity(array(
            'output_type' => $Outputs->enumValueToKey('output_type', 'Relay Output'),
            'output_target' => '1',
            'device_id' => $this->deviceId2,
            'zone_id' => $zone_id,
            'status' => $Outputs->enumValueToKey('status', 'Off'),
            'floorplan_id' => 0
        ));
        $Outputs->save($output);
        $this->heaterId = $output->id;

        // Rules
        $rule = $Rules->newEntity(array(
            'status' => $Rules->enumValueToKey('status', 'Enabled'),
            'label' => 'Turn on the heater when below 78',
            'autoreset' => 1,
        ));
        $Rules->save($rule);
        $this->rule7id = $rule->id;

        $action7 = $RuleActions->newEntity(array(
            'type' => $RuleActions->enumValueToKey('type', 'Turn On'),
            'status' => $RuleActions->enumValueToKey('status', 'Enabled'),
            'notification_level' => $RuleActions->enumValueToKey('notification_level', 'Dashboard Alarm'),
            'rule_id' => $this->rule7id,
            'on_trigger' => 1
        ));
        $RuleActions->save($action7);

        $condition7 = $RuleConditions->newEntity(array(
            'data_source' => $RuleConditions->enumValueToKey('data_source', 'Zone'),
            'data_id' => $zone_id,
            'trigger_threshold' => 78,
            'zone_behavior' => $RuleConditions->enumValueToKey('zone_behavior', 'Average Of Sensors'),
            'status' => $RuleConditions->enumValueToKey('status', 'Enabled'),
            'reset_threshold' => 79,
            'operator' => $RuleConditions->enumValueToKey('operator', '<'),
            'sensor_type' => $Sensors->enumValueToKey('sensor_type','Air Temperature'),
            'trigger_delay' => 10,
            'rule_id' => $this->rule7id,
            'averaging_method' => $RuleConditions->enumValueToKey('averaging_method', 'Average'),
            'is_default' => 0
        ));
        $RuleConditions->save($condition7);

        $ruleActionTarget7 = $this->RuleActionTargets->newEntity(array(
            'rule_action_id' => $action7->id,
            'target_type' => $this->RuleActionTargets->enumValueToKey('target_type','Output'),
            'target_id' => $this->heaterId,
            'status' => $this->RuleActionTargets->enumValueToKey('status','Enabled')
        ));
        $this->RuleActionTargets->save($ruleActionTarget7);

    }

    public function testZoneHeaterRules()
    {

        $this->Notifications = TableRegistry::get('Notifications');
        $this->Outputs = TableRegistry::get('Outputs');
        $Rules = TableRegistry::get('Rules');
        $output = $this->Outputs->get($this->heaterId);
        $rule = $Rules->get($this->rule7id);
        // Have three sensors sending normal temp data

        // Send normal data, get normal response back.
        $data = array('q' => '{"id":' . $this->deviceId . ',"st":1,"d":"[M1:40.2-26.85]"}');
        $this->post(
            '/api/raw',
            array('data' => $data, 'method' => 'get', 'return' => 'vars')
        );
        $this->assertEquals("{}", $this->_response->body());
        $this->Zones->processRules();
        $data = array('q' => '{"id":' . $this->deviceId2 . ',"st":1,"d":"[M2:40.2-26.73,M3:40.2-26.76]"}');
        $this->post(
            '/api/raw',
            array('data' => $data, 'method' => 'get', 'return' => 'vars')
        );
        $this->assertEquals("{}", $this->_response->body());
        $this->Zones->processRules();

        // Send lower temp data on one sensor, lower than the threshold, but the average in the zone is still higher
        $data = array('q' => '{"id":' . $this->deviceId . ',"st":1,"d":"[M1:40.2-24.63]"}');
        $this->post(
            '/api/raw',
            array('data' => $data, 'method' => 'get', 'return' => 'vars')
        );
        $this->assertEquals("{}", $this->_response->body());
        $this->Zones->processRules();
        $data = array('q' => '{"id":' . $this->deviceId2 . ',"st":1,"d":"[M2:40.2-27.73,M3:40.2-27.76]"}');
        $this->post(
            '/api/raw',
            array('data' => $data, 'method' => 'get', 'return' => 'vars')
        );
        $this->assertEquals("{}", $this->_response->body());
        $this->Zones->processRules();

        // Send lower temp data on all sensors, lower than threshold, but time delay will keep heater off.
        $data = array('q' => '{"id":' . $this->deviceId2 . ',"st":1,"d":"[M2:40.2-24.06,M3:40.2-24.65]"}');
        $this->post(
            '/api/raw',
            array('data' => $data, 'method' => 'get', 'return' => 'vars')
        );
        $this->assertEquals("{}", $this->_response->body());
        $this->Zones->processRules();
        
        $data = array('q' => '{"id":' . $this->deviceId . ',"st":1,"d":"[M1:40.2-22.84]"}');
        $this->post(
            '/api/raw',
            array('data' => $data, 'method' => 'get', 'return' => 'vars')
        );
        $this->assertEquals("{}", $this->_response->body());
        $this->Zones->processRules();

        sleep(11);

        // Send lower temp data again, this time, the timed rule should be tripped, heater should be on.
        $data = array('q' => '{"id":' . $this->deviceId . ',"st":1,"d":"[M1:40.2-24.65]"}');
        $this->post(
            '/api/raw',
            array('data' => $data, 'method' => 'get', 'return' => 'vars')
        );
        $this->assertEquals("{}", $this->_response->body());
        $this->Zones->processRules();

        $data = array('q' => '{"id":' . $this->deviceId2 . ',"st":1,"d":"[M2:40.2-21.23,M3:40.2-21.83]"}');
        $this->post(
            '/api/raw',
            array('data' => $data, 'method' => 'get', 'return' => 'vars')
        );
        

        $this->assertEquals("{\"outs\":[\"1\"]}", $this->_response->body());
        $this->Zones->processRules();

        // Send more low temp data, heater should stay on.
        $data = array('q' => '{"id":' . $this->deviceId . ',"st":1,"d":"[M1:40.2-24.01]"}');
        $this->post(
            '/api/raw',
            array('data' => $data, 'method' => 'get', 'return' => 'vars')
        );
        $this->assertEquals("{}", $this->_response->body());
        $this->Zones->processRules();
        $data = array('q' => '{"id":' . $this->deviceId2 . ',"st":1,"d":"[M2:40.2-23.25,M3:40.2-23.45]"}');
        $this->post(
            '/api/raw',
            array('data' => $data, 'method' => 'get', 'return' => 'vars')
        );
        $this->assertEquals("{\"outs\":[\"1\"]}", $this->_response->body());
        $this->Zones->processRules();

        // Temp returns to normal, heater goes off
        $data = array('q' => '{"id":' . $this->deviceId . ',"st":1,"d":"[M1:40.2-26.85]"}');
        $this->post(
            '/api/raw',
            array('data' => $data, 'method' => 'get', 'return' => 'vars')
        );
        $this->assertEquals("{}", $this->_response->body());
        $this->Zones->processRules();
        $data = array('q' => '{"id":' . $this->deviceId2 . ',"st":1,"d":"[M2:40.2-26.45,M3:40.2-26.35]"}');
        $this->post(
            '/api/raw',
            array('data' => $data, 'method' => 'get', 'return' => 'vars')
        );
        $this->assertEquals("{\"outs\":[\"1\"]}", $this->_response->body());
        $this->Zones->processRules();
        $data = array('q' => '{"id":' . $this->deviceId2 . ',"st":1,"d":"[M2:40.2-26.85,M3:40.2-26.55]"}');
        $this->post(
            '/api/raw',
            array('data' => $data, 'method' => 'get', 'return' => 'vars')
        );
        $this->assertEquals("{\"outs\":[\"1\"]}", $this->_response->body());
    }
}
