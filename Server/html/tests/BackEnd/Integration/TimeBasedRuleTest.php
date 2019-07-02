<?php

namespace App\Test\TestCase\Integration;

use Cake\TestSuite\IntegrationTestCase;
use App\Model\Table\DevicesTable;
use Cake\ORM\TableRegistry;
use RuntimeException;

class TimeBasedRuleTest extends IntegrationTestCase {

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
        'app.notifications'
    ];

    public function setUp()
    {
        parent::setUp();
        $Outputs = TableRegistry::get('Outputs');
        $Devices = TableRegistry::get('Devices');
        $Zones = TableRegistry::get('Zones');
        $Rules = TableRegistry::get('Rules');
        $RuleConditions = TableRegistry::get('RuleConditions');
        $RuleActions = TableRegistry::get('RuleActions');
        $RuleActionTargets = TableRegistry::get('RuleActionTargets');

        $device = $Devices->newEntity([
            'label' => 'Test - Power Panel 1 Device',
            'floorplan_id' => 0,
            'doCreateSensors' => false
        ]);
        $Devices->save($device);

        $zone = $Zones->newEntity([
            'label' => 'Test - Flower Zone',
            'status' => $Zones->enumValueToKey('status', 'Enabled'),
            'zone_type_id' => $Zones->enumValueToKey('zone_types', 'Room'),
            'plant_zone_type_id' => $Zones->enumValueToKey('plant_zone_types', 'Bloom'),
            'floorplan_id' => 0
        ]);
        $Zones->save($zone);

        $output = $Outputs->newEntity([
            'status' => $Outputs->enumValueToKey('status', 'Off'),
            'label' => 'Test - Light output',
            'output_target' => 1, //Pin on the Arduino that controls the Relay
            'output_type' => $Outputs->enumValueToKey('output_type', 'Relay Output'),
            'device_id' => $device->id,
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

        $rule_condition = $RuleConditions->newEntity([
            'label' => 'Test - Light ON in 5 seconds from now and OFF in 20 seconds',
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
        $RuleConditions->save($rule_condition);

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

    public function testTimeBasedRules() {
        $Outputs = TableRegistry::get('Outputs');
        $Rules = TableRegistry::get('Rules');
        $Zones = TableRegistry::get('Zones');
        
        sleep(2);
        $Rules->processTimedRules();
        $Zones->processRules();
        // Not triggered yet
        $output = $Outputs->get($this->output_id);
        $this->assertEquals($Outputs->enumValueToKey('status', 'Off'), $output['status']);

        sleep(5);
        $Rules->processTimedRules();
        $Zones->processRules();
        // Triggered!
        $output = $Outputs->get($this->output_id);
        $this->assertEquals($Outputs->enumValueToKey('status', 'On'), $output['status']);

        sleep(17);
        $Rules->processTimedRules();
        $Zones->processRules();
        // Done being triggered until tomorrow
        $output = $Outputs->get($this->output_id);
        $this->assertEquals($Outputs->enumValueToKey('status', 'Off'), $output['status']);
    }
}