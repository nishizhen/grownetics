<?php

namespace App\Test\TestCase\Integration;

use Cake\TestSuite\IntegrationTestCase;
use App\Model\Table\DevicesTable;
use Cake\ORM\TableRegistry;
use RuntimeException;

class MultipleRuleConditionsTest extends IntegrationTestCase {

    public $fixtures = [
        'app.rules',
        'app.rule_conditions',
        'app.rule_actions',
        'app.rule_action_targets',
        'app.devices',
        'app.sensors',
        'app.zones',
        'app.outputs',
        'app.map_items',
        'app.map_item_types',
        'app.notifications',
        'app.sensors_zones'
    ];

    public function setUp()
    {
        parent::setUp();
        $Outputs = TableRegistry::get('Outputs');
        $Devices = TableRegistry::get('Devices');
        $Sensors = TableRegistry::get('Sensors');
        $SensorsZones = TableRegistry::get('SensorsZones');
        $Zones = TableRegistry::get('Zones');
        $Rules = TableRegistry::get('Rules');
        $RuleConditions = TableRegistry::get('RuleConditions');
        $RuleActions = TableRegistry::get('RuleActions');
        $RuleActionTargets = TableRegistry::get('RuleActionTargets');

        $power_panel_device = $Devices->newEntity([
            'label' => 'Test - Power Panel 1 Device',
            'floorplan_id' => 0,
            'doCreateSensors' => false
        ]);
        $Devices->save($power_panel_device);

        $crop_device = $Devices->newEntity([
            'label' => '3D Crop Device',
            'floorplan_id' => 0,
            'doCreateSensors' => false
        ]);
        $Devices->save($crop_device);
        $this->crop_device_id = $crop_device->id;

        $zone = $Zones->newEntity([
            'label' => 'Test - Flower Zone',
            'status' => $Zones->enumValueToKey('status', 'Enabled'),
            'zone_type_id' => $Zones->enumValueToKey('zone_types', 'Room'),
            'plant_zone_type_id' => $Zones->enumValueToKey('plant_zone_types', 'Bloom'),
            'floorplan_id' => 0
        ]);
        $Zones->save($zone);

        $sensor1 = $Sensors->newEntity(array(
            'label' => 'Low - Humidity Sensor',
            'status' => $Sensors->enumValueToKey('status', 'Enabled'),
            'sensor_pin' => 'M1',
            'device_id' => $crop_device->id,
            'sensor_type_id' => $Sensors->enumValueToKey('sensor_type', 'Humidity'),
            'floorplan_id' => 0
        ));
        $Sensors->save($sensor1);
        $SensorsZones->save($SensorsZones->newEntity(['zone_id'=>$zone->id,'sensor_id'=>$sensor1->id]));

        $sensor2 = $Sensors->newEntity(array(
            'label' => 'Low - Temperature Sensor',
            'status' => $Sensors->enumValueToKey('status', 'Enabled'),
            'sensor_pin' => 'M1',
            'device_id' => $crop_device->id,
            'sensor_type_id' => $Sensors->enumValueToKey('sensor_type', 'Air Temperature'),
            'floorplan_id' => 0
        ));
        $Sensors->save($sensor2);
        $SensorsZones->save($SensorsZones->newEntity(['zone_id'=>$zone->id,'sensor_id'=>$sensor2->id]));

        $sensor3 = $Sensors->newEntity(array(
            'label' => 'High - Humidity Sensor',
            'status' => $Sensors->enumValueToKey('status', 'Enabled'),
            'sensor_pin' => 'M2',
            'device_id' => $crop_device->id,
            'sensor_type_id' => $Sensors->enumValueToKey('sensor_type', 'Humidity'),
            'floorplan_id' => 0
        ));
        $Sensors->save($sensor3);
        $SensorsZones->save($SensorsZones->newEntity(['zone_id'=>$zone->id,'sensor_id'=>$sensor3->id]));

        $sensor4 = $Sensors->newEntity(array(
            'label' => 'High - Temperature Sensor',
            'status' => $Sensors->enumValueToKey('status', 'Enabled'),
            'sensor_pin' => 'M2',
            'device_id' => $crop_device->id,
            'sensor_type_id' => $Sensors->enumValueToKey('sensor_type', 'Air Temperature'),
            'floorplan_id' => 0
        ));
        $Sensors->save($sensor4);
        $SensorsZones->save($SensorsZones->newEntity(['zone_id'=>$zone->id,'sensor_id'=>$sensor4->id]));

        $co2_sensor = $Sensors->newEntity(array(
            'label' => 'High - Co2 Sensor',
            'status' => $Sensors->enumValueToKey('status', 'Enabled'),
            'sensor_pin' => 'M3',
            'device_id' => $crop_device->id,
            'sensor_type_id' => $Sensors->enumValueToKey('sensor_type', 'Co2'),
            'floorplan_id' => 0
        ));
        $Sensors->save($co2_sensor);
        $SensorsZones->save($SensorsZones->newEntity(['zone_id'=>$zone->id,'sensor_id'=>$co2_sensor->id]));

        $output = $Outputs->newEntity([
            'status' => $Outputs->enumValueToKey('status', 'Off'),
            'label' => 'Test - Co2 output',
            'output_target' => 1, //Pin on the Arduino that controls the Relay
            'output_type' => $Outputs->enumValueToKey('output_type', 'Relay Output'),
            'device_id' => $power_panel_device->id,
            'zone_id' => $zone->id
        ]);
        $Outputs->save($output);
        $this->output_id = $output->id;
        
        $rule = $Rules->newEntity([
            'label' => 'Test - Turn Light ON',
            'status' => $Rules->enumValueToKey('status', 'Enabled'),
            'autoreset' =>  true,
            'is_default' => false
        ]);
        $Rules->save($rule);

        $time_rule_condition = $RuleConditions->newEntity([
            'label' => 'Test - Turn ON Co2 in 5 seconds and OFF in 20 seconds',
            'data_source' => $RuleConditions->enumValueToKey('data_source', 'Time'),
            'sensor_type' => NULL,
            'data_id' => NULL,
            'operator' => $RuleConditions->enumValueToKey('operator', '>'),
            'trigger_threshold' => time() - strtotime('today') + 5,
            'reset_threshold' => time() - strtotime('today') + 20,
            'status' => $RuleConditions->enumValueToKey('status', 'Enabled'),
            'zone_behavior' => NULL,
            'trigger_delay' => NULL,
            'pending_time' => NULL,
            'rule_id' => $rule->id,
            'is_default' => false
        ]);
        $RuleConditions->save($time_rule_condition);
        $this->time_rule_condition_id = $time_rule_condition->id;

        $co2_range_rule_condition = $RuleConditions->newEntity([
            'label' => 'Test - Keep Co2 between 1150-1200ppm',
            'data_source' => $RuleConditions->enumValueToKey('data_source', 'Zone'),
            'sensor_type' => $Sensors->enumValueToKey('sensor_type', 'Co2'),
            'data_id' => $zone->id,
            'operator' => $RuleConditions->enumValueToKey('operator', '<'),
            'trigger_threshold' => 1150,
            'reset_threshold' => 1200,
            'status' => $RuleConditions->enumValueToKey('status', 'Enabled'),
            'zone_behavior' => $RuleConditions->enumValueToKey('zone_behavior', 'Average Of Sensors'),
            'trigger_delay' => NULL,
            'pending_time' => NULL,
            'rule_id' => $rule->id,
            'is_default' => false
        ]);
        $RuleConditions->save($co2_range_rule_condition);
        $this->co2_range_rule_condition_id = $co2_range_rule_condition->id;

        $rule_action = $RuleActions->newEntity([
            'type' => $RuleActions->enumValueToKey('type', 'Turn On'),
            'notification_level' => $RuleActions->enumValueToKey('notification_level', 'Logged Only'),
            'notification_user_role' => NULL,
            'rule_id' => $rule->id,
            'on_trigger' => true,
            'is_default' => false
        ]);
        $RuleActions->save($rule_action);

        $rule_action_target = $RuleActionTargets->newEntity([
            'rule_action_id' => $rule_action->id,
            'target_type' => $RuleActionTargets->enumValueToKey('target_type', 'Output'),
            'target_id' => $output->id,
            'status' => $RuleActionTargets->enumValueToKey('status', 'Enabled'),
            'output_value' => NULL,
            'output_object' => NULL,
            'output_property' => NULL,
            'is_default' => false
        ]);
        $RuleActionTargets->save($rule_action_target);
    }

    public function testMultipleConditions() {
        $Outputs = TableRegistry::get('Outputs');
        $Rules = TableRegistry::get('Rules');
        $Zones = TableRegistry::get('Zones');

        sleep(2);

        $data = array('q' => '{"id":' . $this->crop_device_id . ',"st":1,"d":"[M1:70.0-24.0],[M2:60.0-23.0],[M3:1110]"}');
        $this->post(
            '/api/raw',
            array('data' => $data, 'method' => 'get', 'return' => 'vars')
        );
        $this->assertEquals("{}", $this->_response->body());
        $Rules->processTimedRules();
        $Zones->processRules();

        // Not triggered yet
        $output = $Outputs->get($this->output_id);
        $this->assertEquals($Outputs->enumValueToKey('status', 'Off'), $output['status']);

        sleep(5);

        $data = array('q' => '{"id":' . $this->crop_device_id . ',"st":1,"d":"[M1:70.0-24.0],[M2:60.0-23.0],[M3:1110]"}');
        $this->post(
            '/api/raw',
            array('data' => $data, 'method' => 'get', 'return' => 'vars')
        );
        $this->assertEquals("{}", $this->_response->body());

        $Rules->processTimedRules();
        $Zones->processRules();

        // Triggered!
        $output = $Outputs->get($this->output_id);
        $this->assertEquals($Outputs->enumValueToKey('status', 'On'), $output['status']);

        sleep(17);

        $data = array('q' => '{"id":' . $this->crop_device_id . ',"st":1,"d":"[M1:70.0-24.0],[M2:60.0-23.0],[M3:1110]"}');
        $this->post(
            '/api/raw',
            array('data' => $data, 'method' => 'get', 'return' => 'vars')
        );
        $this->assertEquals("{}", $this->_response->body());
        $Rules->processTimedRules();
        $Zones->processRules();

        // Done being triggered until tomorrow
        $output = $Outputs->get($this->output_id);
        $this->assertEquals($Outputs->enumValueToKey('status', 'Off'), $output['status']);
    }
}