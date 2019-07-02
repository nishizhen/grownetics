<?php
namespace App\Test\TestCase\Integration;

use Cake\TestSuite\IntegrationTestCase;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use RuntimeException;

/**
 * App\Model\Table\TimeBasedSetPointRulesTable Test Case
 */
class TimeBasedSetPointRulesTableTest extends IntegrationTestCase
{

    public $fixtures = array(
        'app.floorplans',
        'app.notifications',
        'app.devices',
        'app.sensors',
        'app.sensors_zones',
        'app.zones',
        'app.rules',
        'app.users',
        'app.roles',
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

        $Devices = TableRegistry::get('Devices');
        $Sensors = TableRegistry::get('Sensors');
        $SensorsZones = TableRegistry::get('SensorsZones');
        $Rules = TableRegistry::get('Rules');
        $this->Notifications = TableRegistry::get('Notifications');
        $this->Zones = TableRegistry::get('Zones');
        $Output = TableRegistry::get('outputs');
        $this->RuleConditions = TableRegistry::get('RuleConditions');
        $RuleActions = TableRegistry::get('RuleActions');
        $this->RuleActionTargets = TableRegistry::get('RuleActionTargets');
        $this->SetPoints = TableRegistry::get('SetPoints');
        $this->DataPoints = TableRegistry::get('DataPoints');

        $device = $Devices->newEntity([
            'doCreateSensors' => false,
            'latitude' => 9.0099999,
            'longitude' => 366.0000002,
            'label' => 'Morty',
	        'floorplan_id' => 0,
	        'status' => 1
        ]);

        if ($Devices->save($device)) {
            // The $article entity contains the id now
            $this->deviceId = $device->api_id;
        }

        $zone = $this->Zones->newEntity();

        if ($this->Zones->save($zone)) {
            // The $article entity contains the id now
            $zone_id = $this->zone_id = $zone->id;
        }
       
        $air_temp_sensor_type_id = $Sensors->enumValueToKey('sensor_type','Air Temperature');
        $humidity_sensor_type_id = $Sensors->enumValueToKey('sensor_type','Humidity');

        $sensor = $Sensors->newEntity(array(
            'sensor_pin' => 'M1',
            'device_id' => $this->deviceId,
            'status' => '1',
            'sensor_type_id' => $humidity_sensor_type_id
        ));
        $sensor1id = $Sensors->save($sensor)->id;
        $SensorsZones->save($SensorsZones->newEntity(['zone_id'=>$zone_id,'sensor_id'=>$sensor1id]));

        $sensor = $Sensors->newEntity(array(
            'sensor_pin' => 'M1',
            'device_id' => $this->deviceId,
            'status' => '1',
            'sensor_type_id' => $air_temp_sensor_type_id
        ));
        $sensor2id = $Sensors->save($sensor)->id;
        $SensorsZones->save($SensorsZones->newEntity(['zone_id'=>$zone_id,'sensor_id'=>$sensor2id]));

        $rule = $Rules->newEntity(array(
            'status' => $Rules->enumValueToKey('status', 'Enabled'),
            'label' => 'Set point in 15 seconds',
            'is_default' => false
        ));
        $Rules->save($rule);
        $this->ruleId = $rule->id;

        $action = $RuleActions->newEntity(array(
            'type' => $RuleActions->enumValueToKey('type', 'Set Point'),
            'status' => $RuleActions->enumValueToKey('status', 'Enabled'),
            'notification_level' => $RuleActions->enumValueToKey('notification_level', 'Logged Only'),
            'rule_id' => $this->ruleId,
            'on_trigger' => 1,
            'is_default' => false
        ));
        $RuleActions->save($action);

        $relay_output = $Output->newEntity(array(
            'status' => 1,
            'output_type' => $Output->enumValueToKey('output_type', 'Relay Output'),
            'output_target' => 1,
            'device_id' => $this->deviceId
        ));
        $Output->save($relay_output);
        $relay_output_id = $relay_output->id;

        $condition = $this->RuleConditions->newEntity(array(
            'data_source' => $this->RuleConditions->enumValueToKey('data_source', 'Time'),
            'trigger_threshold' => time() - strtotime('today') + 15,
            'status' => $this->RuleConditions->enumValueToKey('status', 'Enabled'),
            'autoreset' => 1,
            'operator' => 0,
            'sensor_type' => $air_temp_sensor_type_id,
            'rule_id' => $this->ruleId,
            'is_default' => false
        ));
        $this->RuleConditions->save($condition);
        $this->condition_id = $condition->id;

        $setPoint = $this->SetPoints->newEntity(array(
            'value' => 69,
            'status' => $this->SetPoints->enumValueToKey('status', 'Enabled'),
            'target_id' => $relay_output_id,
            'target_type' => $this->SetPoints->enumValueToKey('target_type','Zone'),
            'sensor_type' => $air_temp_sensor_type_id
        ));
        $this->SetPoints->save($setPoint);
        $this->setPoint = $setPoint;

        $ruleActionTarget = $this->RuleActionTargets->newEntity(array(
            'rule_action_id' => $action->id,
            'target_type' => $this->RuleActionTargets->enumValueToKey('target_type','Set Point'),
            'target_id' => $setPoint->id,
            'status' => $this->RuleActionTargets->enumValueToKey('status','Enabled'),
            'output_value' => 80,
            'is_default' => false
        ));
        $this->RuleActionTargets->save($ruleActionTarget);
        $this->ratId = $ruleActionTarget->id;

    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->TimeBasedSetPointRules);

        parent::tearDown();
    }

    /**
     * Test initial setup
     *
     * @return void
     */
    public function testInitialization()
    {
        $this->Notifications = TableRegistry::get('Notifications');
        $this->RuleActionTargets = TableRegistry::get('RuleActionTargets');
        $Rules = TableRegistry::get('Rules');
        $Zones = TableRegistry::get('Zones');

        $data = array('q' => '{"id":' . $this->deviceId . ',"st":1,"d":"[M1:40.2-85.34]"}');
        $this->post(
            '/api/raw',
            array('data' => $data, 'method' => 'get', 'return' => 'vars')
        );
	    $this->assertEquals("{}", $this->_response->body());
        $rule_target = $Rules->get($this->ruleId);
        $this->assertEquals($rule_target['status'], $Rules->enumValueToKey('status','Enabled'));
        sleep(22);
        $Rules->processTimedRules();
        $Zones->processRules();

        $rule_target = $Rules->get($this->ruleId);
        $this->assertEquals($rule_target['status'], $Rules->enumValueToKey('status','Triggered'));
    }
}
