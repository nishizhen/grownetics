<?php

namespace App\Test\TestCase\Integration;

use Cake\TestSuite\IntegrationTestCase;
use App\Model\Table\DevicesTable;
use Cake\ORM\TableRegistry;
use RuntimeException;

class ZoneChillerRulesTest extends IntegrationTestCase
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
        'app.map_item_types',
        'app.roles',
        'app.users'
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

        $device = $Devices->newEntity([
            'doCreateSensors' => false,
            'latitude' => 1000.99999,
            'longitude' => 0.0000002,
            'label' => '3D Crop Sensor 3',
            'floorplan_id' => 0
        ]);
        $Devices->save($device);
        $this->deviceId = $device->api_id;

        $device = $Devices->newEntity([
            'doCreateSensors' => false,
            'latitude' => 9.0099999,
            'longitude' => 366.0000002,
            'label' => 'Chiller Relay',
            'floorplan_id' => 0,
            'type' => $Devices->enumValueToKey('type','Control')
        ]);
        $Devices->save($device);
        $this->deviceId2 = $device->api_id;

        $zone = $this->Zones->newEntity();
        $this->Zones->save($zone);
        $zone_id = $this->zone_id = $zone->id;

        $sensor = $Sensors->newEntity(array(
            'label' => 'Low - Humidity Sensor',
            'status' => $Sensors->enumValueToKey('status', 'Enabled'),
            'sensor_pin' => 'M1',
            'device_id' => $this->deviceId,
            'sensor_type_id' => $Sensors->enumValueToKey('sensor_type', 'Humidity'),
            'floorplan_id' => 0
        ));
        $sensor1id = $Sensors->save($sensor)->id;
        $SensorsZones->save($SensorsZones->newEntity(['zone_id'=>$zone_id,'sensor_id'=>$sensor1id]));

        $sensor = $Sensors->newEntity(array(
            'label' => 'Low - Air Temperature Sensor',
            'status' => $Sensors->enumValueToKey('status', 'Enabled'),
            'sensor_pin' => 'M1',
            'device_id' => $this->deviceId,
            'sensor_type_id' => $Sensors->enumValueToKey('sensor_type', 'Air Temperature'),
            'floorplan_id' => 0
        ));
        $sensor2id = $Sensors->save($sensor)->id;
        $SensorsZones->save($SensorsZones->newEntity(['zone_id'=>$zone_id,'sensor_id'=>$sensor2id]));

        $sensor = $Sensors->newEntity(array(
            'label' => 'High - Humidity Sensor',
            'sensor_pin' => 'M2',
            'device_id' => $this->deviceId,
            'status' => $Sensors->enumValueToKey('status', 'Enabled'),
            'sensor_type_id' => $Sensors->enumValueToKey('sensor_type', 'Humidity'),
            'floorplan_id' => 0
        ));
        $sensor3id = $Sensors->save($sensor)->id;
        $SensorsZones->save($SensorsZones->newEntity(['zone_id'=>$zone_id,'sensor_id'=>$sensor3id]));

        $sensor = $Sensors->newEntity(array(
            'label' => 'High - Air Temperature Sensor',
            'sensor_pin' => 'M2',
            'device_id' => $this->deviceId,
            'status' => $Sensors->enumValueToKey('status', 'Enabled'),
            'sensor_type_id' => $Sensors->enumValueToKey('sensor_type', 'Air Temperature'),
            'floorplan_id' => 0
        ));
        $sensor4id = $Sensors->save($sensor)->id;
        $SensorsZones->save($SensorsZones->newEntity(['zone_id'=>$zone_id,'sensor_id'=>$sensor4id]));

        $sensor = $Sensors->newEntity(array(
            'label' => 'High - Co2 Sensor',
            'status' => $Sensors->enumValueToKey('status', 'Enabled'),
            'sensor_pin' => 'M3',
            'device_id' => $this->deviceId,
            'zone_id' => $zone_id,
            'sensor_type_id' => $Sensors->enumValueToKey('sensor_type', 'Co2'),
            'floorplan_id' => 0
        ));
        $sensor5id = $Sensors->save($sensor)->id;
        $SensorsZones->save($SensorsZones->newEntity(['zone_id'=>$zone_id,'sensor_id'=>$sensor5id]));

        $output = $Outputs->newEntity(array(
            'output_type' => $Outputs->enumValueToKey('output_type', 'Relay Output'),
            'output_target' => 2, //pin on the Arduino that controls the Relay
            'device_id' => $this->deviceId2,
            'zone_id' => $zone_id,
            'status' => $Outputs->enumValueToKey('status', 'Off')
        ));
        $Outputs->save($output);
        $this->chillerId = $output->id;

        // Rules
        $rule = $Rules->newEntity(array(
            'status' => $Rules->enumValueToKey('status', 'Enabled'),
            'label' => 'Turn on chiller above 82',
            'is_default' => false
        ));
        $Rules->save($rule);
        $this->rule1id = $rule->id;

        $condition = $RuleConditions->newEntity(array(
            'data_source' => $RuleConditions->enumValueToKey('data_source', 'Zone'),
            'data_id' => $zone_id,
            'trigger_threshold' => 82,
            'zone_behavior' => $RuleConditions->enumValueToKey('zone_behavior', 'Average Of Sensors'),
            'status' => $RuleConditions->enumValueToKey('status', 'Enabled'),
            'reset_threshold' => 81,
            'autoreset' => 1,
            'operator' => $RuleConditions->enumValueToKey('operator', '>'),
            'sensor_type' => $Sensors->enumValueToKey('sensor_type', 'Air Temperature'),
            'trigger_delay' => 10,
            'rule_id' => $this->rule1id,
            'is_default' => false
        ));
        $RuleConditions->save($condition);

        $action = $RuleActions->newEntity(array(
            'type' => $RuleActions->enumValueToKey('type', 'Turn On'),
            'status' => $RuleActions->enumValueToKey('status', 'Enabled'),
            'notification_level' => $RuleActions->enumValueToKey('notification_level', 'Logged Only'),
            'rule_id' => $this->rule1id,
            'on_trigger' => 1,
            'is_default' => false
        ));
        $RuleActions->save($action);

        $ruleActionTarget = $this->RuleActionTargets->newEntity(array(
            'rule_action_id' => $action->id,
            'target_type' => $this->RuleActionTargets->enumValueToKey('target_type','Output'),
            'target_id' => $this->chillerId,
            'status' => $this->RuleActionTargets->enumValueToKey('status','Enabled'),
            'is_default' => false
        ));
        $this->RuleActionTargets->save($ruleActionTarget);

    }

    public function testHighTempDataDelay()
    {

        $this->Notifications = TableRegistry::get('Notifications');
        $Rules = TableRegistry::get('Rules');
        // Send higher temp data on all sensors, higher than threshold, but time delay will keep chiller off.
        $data = array('q' => '{"id":' . $this->deviceId . ',"st":1,"d":"[M1:40.2-85.34]"}');
        $this->post(
            '/api/raw',
            array('data' => $data, 'method' => 'get', 'return' => 'vars')
        );
        $this->assertEquals("{}", $this->_response->body());
        $this->Zones->processRules();
        $data = array('q' => '{"id":' . $this->deviceId2 . ',"st":1,"d":"[M2:40.2-82.12,M3:40.2-89.18]"}');
        $this->post(
            '/api/raw',
            array('data' => $data, 'method' => 'get', 'return' => 'vars')
        );
        $this->assertEquals("{}", $this->_response->body());
        $this->Zones->processRules();

        sleep(1);

        // Send higher temp data on all sensors, higher than threshold, but time delay will keep chiller off.
        $data = array('q' => '{"id":' . $this->deviceId . ',"st":1,"d":"[M1:40.2-85.34]"}');
        $this->post(
            '/api/raw',
            array('data' => $data, 'method' => 'get', 'return' => 'vars')
        );
        $this->assertEquals("{}", $this->_response->body());
        $this->Zones->processRules();
        $data = array('q' => '{"id":' . $this->deviceId2 . ',"st":1,"d":"[M2:40.2-83.12,M3:40.2-85.18]"}');
        $this->post(
            '/api/raw',
            array('data' => $data, 'method' => 'get', 'return' => 'vars')
        );
        $this->assertEquals("{}", $this->_response->body());

        $this->Zones->processRules();
        // No notification
        $notifications = $this->Notifications->find('all', array('conditions' => array('rule_id' => $this->rule1id)))->all();
        $this->assertEmpty($notifications);
        $this->Zones->processRules();
        sleep(15);

        // Send higher temp data again, this time, the timed rule should be tripped, chiller should turn on.
        $data = array('q' => '{"id":' . $this->deviceId . ',"st":1,"d":"[M1:40.2-86.54]"}');
        $this->post(
            '/api/raw',
            array('data' => $data, 'method' => 'get', 'return' => 'vars')
        );
        $this->assertEquals("{}", $this->_response->body());
        $this->Zones->processRules();

        $data = array('q' => '{"id":' . $this->deviceId2 . ',"st":1,"d":"[M2:40.2-89.22,M3:40.2-89.08]"}');
        $this->post(
            '/api/raw',
            array('data' => $data, 'method' => 'get', 'return' => 'vars')
        );
        $this->assertEquals("{\"outs\":[\"2\"]}", $this->_response->body());
        $this->Zones->processRules();

        $data = array('q' => '{"id":' . $this->deviceId2 . ',"st":1,"d":"[M2:40.2-82.22,M3:40.2-82.08]"}');
        $this->post(
            '/api/raw',
            array('data' => $data, 'method' => 'get', 'return' => 'vars')
        );
        $this->assertEquals("{\"outs\":[\"2\"]}", $this->_response->body());

        $notifications = $this->Notifications->find('all', array('conditions' => array('rule_id' => $this->rule1id)))->toArray();

        $this->assertEquals($notifications[0]['notification_level'],
            $Rules->enumValueToKey('notification_level', 'Logged Only')
        );

        // Send more high temp data, chiller should stay on.
        $data = array('q' => '{"id":' . $this->deviceId . ',"st":1,"d":"[M1:40.2-82.54]"}');
        $this->post(
            '/api/raw',
            array('data' => $data, 'method' => 'get', 'return' => 'vars')
        );
        $this->assertEquals("{}", $this->_response->body());
        $data = array('q' => '{"id":' . $this->deviceId2 . ',"st":1,"d":"[M2:40.2-82.22,M3:40.2-82.08]"}');
        $this->post(
            '/api/raw',
            array('data' => $data, 'method' => 'get', 'return' => 'vars')
        );
        $this->assertEquals("{\"outs\":[\"2\"]}", $this->_response->body());
    }

}