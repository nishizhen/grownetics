<?php
namespace App\Test\TestCase\Integration;

use Cake\TestSuite\IntegrationTestCase;
use Cake\ORM\TableRegistry;
use RuntimeException;

/**
 * App\Controller\FailoverSchedule Test Case
 */
class FailoverScheduleTest extends IntegrationTestCase
{
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
        'app.roles',
        'app.users'
    ];

    public function setUp() {
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

        $device = $Devices->newEntity([
            'label' => 'test device',
            'floorplan_id' => 0,
            'doCreateSensors' => false
        ]);
        $Devices->save($device);
        $this->device_id = $device->id;

        $zone = $Zones->newEntity([
            'label' => 'test zone',
            'status' => $Zones->enumValueToKey('status', 'Enabled'),
            'floorplan_id' => 0
        ]);
        $Zones->save($zone);

        $output = $Outputs->newEntity([
            'status' => $Outputs->enumValueToKey('status', 'On'),
            'label' => 'test output',
            'output_target' => 5, //pin for the Arduino
            'output_type' => $Outputs->enumValueToKey('output_type', 'Relay Output'),
            'hardware_type' => $Outputs->enumValueToKey('hardware_type', 'Light'),
            'device_id' => $device->id,
            'zone_id' => $zone->id
        ]);
        $Outputs->save($output);
        $this->output_1_target_id = $output->output_target;
        $output2 = $Outputs->newEntity([
            'status' => $Outputs->enumValueToKey('status', 'On'),
            'label' => 'test output 2',
            'output_target' => 10, //pin for the Arduino
            'output_type' => $Outputs->enumValueToKey('output_type', 'Relay Output'),
            'hardware_type' => $Outputs->enumValueToKey('hardware_type', 'Light'),
            'device_id' => $device->id,
            'zone_id' => $zone->id
        ]);
        $Outputs->save($output2);
        $this->output_2_target_id = $output2->output_target;
        $output3 = $Outputs->newEntity([
            'status' => $Outputs->enumValueToKey('status', 'On'),
            'label' => 'test output 3',
            'output_target' => 15, //pin for the Arduino
            'output_type' => $Outputs->enumValueToKey('output_type', 'Relay Output'),
            'hardware_type' => $Outputs->enumValueToKey('hardware_type', 'Light'),
            'device_id' => $device->id,
            'zone_id' => $zone->id
        ]);
        $Outputs->save($output3);
        $this->output_3_target_id = $output3->output_target;
        $output4 = $Outputs->newEntity([
            'status' => $Outputs->enumValueToKey('status', 'On'),
            'label' => 'test output 3',
            'output_target' => 1, //pin for the Arduino
            'output_type' => $Outputs->enumValueToKey('output_type', 'Relay Output'),
            'hardware_type' => $Outputs->enumValueToKey('hardware_type', 'Generic'),
            'device_id' => $device->id,
            'zone_id' => $zone->id
        ]);
        $Outputs->save($output4);
        $this->output_4_target_id = $output4->output_target;
        $rule = $Rules->newEntity([
            'label' => 'test rule 1',
            'status' => $Rules->enumValueToKey('status', 'Enabled'),
            'autoreset' => true
        ]);
        $Rules->save($rule);
        $rule_condition = $RuleConditions->newEntity([
            'label' => 'test rule condition',
            'data_source' => $RuleConditions->enumValueToKey('data_source', 'Time'),
            'operator' => $RuleConditions->enumValueToKey('operator', '>'),
            'trigger_threshold' => 60,
            'reset_threshold' => 50400,
            'status' => $RuleConditions->enumValueToKey('status', 'Enabled'),
            'zone_behavior' => $RuleConditions->enumValueToKey('zone_behavior', 'Single Sensor'),
            'rule_id' => $rule->id
        ]);
        $RuleConditions->save($rule_condition);

        $this->rule_1_time_on = strtotime('today') + $rule_condition->reset_threshold;
        $this->rule_1_time_off = strtotime('today') + $rule_condition->trigger_threshold;

        $rule_action = $RuleActions->newEntity([
            'type' => $RuleActions->enumValueToKey('type', 'Turn Off'),
            'notification_level' => $RuleActions->enumValueToKey('notification_level', 'Dashboard Alarm'),
            'rule_id' => $rule->id,
            'on_trigger' => true,
        ]);
        $RuleActions->save($rule_action);

        // RULE 2
        $rule2 = $Rules->newEntity([
            'label' => 'test rule 2',
            'status' => $Rules->enumValueToKey('status', 'Enabled'),
            'autoreset' => true
        ]);
        $Rules->save($rule2);
        $rule_condition = $RuleConditions->newEntity([
            'label' => 'test rule condition 2',
            'data_source' => $RuleConditions->enumValueToKey('data_source', 'Time'),
            'operator' => $RuleConditions->enumValueToKey('operator', '>'),
            'trigger_threshold' => 3600,
            'reset_threshold' => 48500,
            'status' => $RuleConditions->enumValueToKey('status', 'Enabled'),
            'zone_behavior' => $RuleConditions->enumValueToKey('zone_behavior', 'Single Sensor'),
            'rule_id' => $rule2->id
        ]);
        $RuleConditions->save($rule_condition);

        $this->rule_2_time_on = strtotime('today') + $rule_condition->reset_threshold;
        $this->rule_2_time_off = strtotime('today') + $rule_condition->trigger_threshold;

        $rule_action2 = $RuleActions->newEntity([
            'type' => $RuleActions->enumValueToKey('type', 'Turn Off'),
            'notification_level' => $RuleActions->enumValueToKey('notification_level', 'Dashboard Alarm'),
            'rule_id' => $rule2->id,
            'on_trigger' => true,
        ]);
        $RuleActions->save($rule_action2);
        // End of Rule 2

        $rule_action_target = $RuleActionTargets->newEntity([
            'rule_action_id' => $rule_action->id,
            'target_type' => $RuleActionTargets->enumValueToKey('target_type', 'Output'),
            'target_id' => $output->id,
            'status' => $RuleActionTargets->enumValueToKey('status', 'Enabled')
        ]);
        $RuleActionTargets->save($rule_action_target);

        $rule_action_target = $RuleActionTargets->newEntity([
            'rule_action_id' => $rule_action->id,
            'target_type' => $RuleActionTargets->enumValueToKey('target_type', 'Output'),
            'target_id' => $output2->id,
            'status' => $RuleActionTargets->enumValueToKey('status', 'Enabled')
        ]);
        $RuleActionTargets->save($rule_action_target);

        $rule_action_target = $RuleActionTargets->newEntity([
            'rule_action_id' => $rule_action2->id,
            'target_type' => $RuleActionTargets->enumValueToKey('target_type', 'Output'),
            'target_id' => $output3->id,
            'status' => $RuleActionTargets->enumValueToKey('status', 'Enabled')
        ]);
        $RuleActionTargets->save($rule_action_target);
    }

    public function testFailoverResponse() {


        $data = array('q' => '{"id":' . $this->device_id . ',"st":1,"d":"[M1:70.0-24.0],[M2:60.0-23.0],[M3:1110]"}');
        $this->post(
            '/api/tlc',
            array('data' => $data, 'method' => 'get', 'return' => 'vars')
        );

        $res = json_decode($this->_response->body());
        unset($res->current_time);
        $this->assertEquals('{"light_outs":["'.$this->output_1_target_id.'","'.$this->output_2_target_id.'","'.$this->output_3_target_id.'"],"generic_outs":["'.$this->output_4_target_id.'"],"failover_schedule":[{"light_outputs":["'.$this->output_1_target_id.'","'.$this->output_2_target_id.'"],"action":"off","timestamp":'.$this->rule_1_time_off.'},{"light_outputs":["'.$this->output_3_target_id.'"],"action":"off","timestamp":'.$this->rule_2_time_off.'},{"light_outputs":["'.$this->output_1_target_id.'","'.$this->output_2_target_id.'"],"action":"on","timestamp":'.$this->rule_1_time_on.'},{"light_outputs":["'.$this->output_3_target_id.'"],"action":"on","timestamp":'.$this->rule_2_time_on.'}]}', json_encode($res));
    }
}
