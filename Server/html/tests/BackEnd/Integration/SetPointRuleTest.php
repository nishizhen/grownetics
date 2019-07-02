<?php

namespace App\Test\TestCase\Integration;

use Cake\TestSuite\IntegrationTestCase;
use App\Model\Table\DevicesTable;
use Cake\ORM\TableRegistry;
use RuntimeException;

class SetPointRuleTest extends IntegrationTestCase
{

    public $fixtures = array(
        'app.floorplans',
        'app.notifications',
        'app.devices',
        'app.sensors',
        'app.sensors_zones',
        'app.users',
        'app.roles',
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

    public function setUp()
    {
        parent::setUp();

        $Devices = TableRegistry::get('Devices');
        $Sensors = TableRegistry::get('Sensors');
        $SensorsZones = TableRegistry::get('SensorsZones');
        $Rules = TableRegistry::get('Rules');
        $this->Notifications = TableRegistry::get('Notifications');
        $this->Zones = TableRegistry::get('Zones');
        $RuleConditions = TableRegistry::get('RuleConditions');
        $RuleActions = TableRegistry::get('RuleActions');
        $this->RuleActionTargets = TableRegistry::get('RuleActionTargets');
        $this->SetPoints = TableRegistry::get('SetPoints');
        $this->DataPoints = TableRegistry::get('DataPoints');

        $device = $Devices->newEntity([
            'doCreateSensors' => false,
            'latitude' => 9.0099999,
            'longitude' => 366.0000002,
            'label' => 'Morty',
            'floorplan_id' => 0
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

        // Rules
        # Yes, you would never do anything like this rule in real life, but it's a test.
        $rule = $Rules->newEntity(array(
            'status' => $Rules->enumValueToKey('status', 'Enabled'),
            'label' => 'Set temp to 69 when temp reads above 82'
        ));
        $Rules->save($rule);
        $this->rule1id = $rule->id;

        $action = $RuleActions->newEntity(array(
            'type' => $RuleActions->enumValueToKey('type', 'Set Point'),
            'status' => $RuleActions->enumValueToKey('status', 'Enabled'),
            'notification_level' => $RuleActions->enumValueToKey('notification_level', 'Logged Only'),
            'rule_id' => $this->rule1id,
            'on_trigger' => 1,
            'output_value' => 80

        ));
        $RuleActions->save($action);

        $condition = $RuleConditions->newEntity(array(
            'data_source' => $RuleConditions->enumValueToKey('data_source', 'Zone'),
            'data_id' => $zone_id,
            'trigger_threshold' => 82,
            'zone_behavior' => $RuleConditions->enumValueToKey('zone_behavior', 'Single Sensor'),
            'status' => $RuleConditions->enumValueToKey('status', 'Enabled'),
            'reset_threshold' => 81,
            'autoreset' => 1,
            'operator' => $RuleConditions->enumValueToKey('operator', '>'),
            'sensor_type' => $air_temp_sensor_type_id,
            'trigger_delay' => 2,
            'rule_id' => $this->rule1id
        ));
        $RuleConditions->save($condition);

        $setPoint = $this->SetPoints->newEntity(array(
            'value' => 69,
            'target_id' => $zone_id,
            'target_type' => $this->SetPoints->enumValueToKey('target_type','Zone'),
            'sensor_type' => $air_temp_sensor_type_id
        ));
        $this->SetPoints->save($setPoint);
        $this->setPoint = $setPoint;

        $ruleActionTarget = $this->RuleActionTargets->newEntity(array(
            'rule_action_id' => $action->id,
            'target_type' => $this->RuleActionTargets->enumValueToKey('target_type','Set Point'),
            'target_id' => $setPoint->id,
            'status' => $this->RuleActionTargets->enumValueToKey('status','Enabled')
        ));
        $this->RuleActionTargets->save($ruleActionTarget);
        $this->ratId = $ruleActionTarget->id;
    }

    public function testSetPointRule()
    {
        $this->Notifications = TableRegistry::get('Notifications');
        $this->RuleActionTargets = TableRegistry::get('RuleActionTargets');

        $data = array('q' => '{"id":' . $this->deviceId . ',"st":1,"d":"[M1:40.2-85.34]"}');
        $this->post(
            '/api/raw',
            array('data' => $data, 'method' => 'get', 'return' => 'vars')
        );
        $this->assertEquals("{}", $this->_response->body());
        $this->Zones->processRules();
        sleep(3);

        $data = array('q' => '{"id":' . $this->deviceId . ',"st":1,"d":"[M1:40.2-88.34]"}');
        $this->post(
            '/api/raw',
            array('data' => $data, 'method' => 'get', 'return' => 'vars')
        );
        $this->assertEquals("{}", $this->_response->body());
        $this->Zones->processRules();
        

        $target = $this->RuleActionTargets->get($this->ratId);
        //Rule should be triggered and SetPoint RAT should change from Enabled to Set
        $this->assertEquals($target['status'], $this->RuleActionTargets->enumValueToKey('status','Set'));
    }
}
