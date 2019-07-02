<?php

namespace App\Test\TestCase\Integration;

use Cake\TestSuite\IntegrationTestCase;
use Cake\ORM\TableRegistry;
use RuntimeException;

class ZoneBehaviorTest extends IntegrationTestCase {

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
        $Rules = TableRegistry::get('Rules');
        $Devices = TableRegistry::get('Devices');
        $Sensors = TableRegistry::get('Sensors');
        $Outputs = TableRegistry::get('Outputs');
        $RuleConditions = TableRegistry::get('RuleConditions');
        $RuleActions = TableRegistry::get('RuleActions');
        $RuleActionTargets = TableRegistry::get("RuleActionTargets");
        $SensorsZones = TableRegistry::get("SensorsZones");        

        $zone = $Zones->newEntity();
        $Zones->save($zone);

        $air_temp_sensor_type_id = $Sensors->enumValueToKey('sensor_type','Air Temperature');
        $humidity_sensor_type_id = $Sensors->enumValueToKey('sensor_type','Humidity');
        $co2_sensor_type_id = $Sensors->enumValueToKey('sensor_type','Co2');

        $device1 = $Devices->newEntity(array(
            'status' => 1,
            'doCreateSensors' => false,
            'latitude' => 1000.99999,
            'longitude' => 0.0000002,
            'label' => '3D Crop Sensor 2',
            'floorplan_id' => 0
        ));
        $Devices->save($device1);
        $this->deviceId1 = $device1->api_id;

        $device2 = $Devices->newEntity([
            'doCreateSensors' => false,
            'latitude' => 9.0099999,
            'longitude' => 366.0000002,
            'label' => 'Power Panel',
            'floorplan_id' => 0
        ]);

        $Devices->save($device2);
        $this->deviceId2 = $device2->api_id;

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

        $output = $Outputs->newEntity(array(
            'output_type' => $Outputs->enumValueToKey('output_type', 'Relay Output'),
            'output_target' => '1',
            'device_id' => $this->deviceId2,
            'zone_id' => $zone->id,
            'status' => 1,
            'floorplan_id' => 0
        ));
        $Outputs->save($output);
        $this->heaterId = $output->id;

        $rule = $Rules->newEntity(array(
            'status' => $Rules->enumValueToKey('status', 'Enabled'),
            'label' => 'Turn on Heater when below 75',
            'autoreset' => 1,
        ));
        $Rules->save($rule);

        $action = $RuleActions->newEntity(array(
            'type' => $RuleActions->enumValueToKey('type', 'Turn On'),
            'status' => $RuleActions->enumValueToKey('status', 'Enabled'),
            'notification_level' => $RuleActions->enumValueToKey('notification_level', 'Dashboard Alarm'),
            'rule_id' => $rule->id,
            'on_trigger' => 1
        ));
        $RuleActions->save($action);

        $condition = $RuleConditions->newEntity(array(
            'data_source' => $RuleConditions->enumValueToKey('data_source', 'Zone'),
            'data_id' => $zone->id,
            'trigger_threshold' => 75,
            'zone_behavior' => $RuleConditions->enumValueToKey('zone_behavior', 'Single Sensor'),
            'status' => $RuleConditions->enumValueToKey('status', 'Enabled'),
            'reset_threshold' => 76,
            'operator' => $RuleConditions->enumValueToKey('operator', '<'),
            'sensor_type' => $Sensors->enumValueToKey('sensor_type','Air Temperature'),
            'trigger_delay' => 1,
            'rule_id' => $rule->id
        ));
        $RuleConditions->save($condition);
        $this->singleSensorConditionLowId = $condition->id;

        $condition2 = $RuleConditions->newEntity(array(
            'data_source' => $RuleConditions->enumValueToKey('data_source', 'Zone'),
            'data_id' => $zone->id,
            'trigger_threshold' => 75,
            'zone_behavior' => $RuleConditions->enumValueToKey('zone_behavior', 'Single Sensor'),
            'status' => $RuleConditions->enumValueToKey('status', 'Enabled'),
            'reset_threshold' => 76,
            'operator' => $RuleConditions->enumValueToKey('operator', '>'),
            'sensor_type' => $Sensors->enumValueToKey('sensor_type','Air Temperature'),
            'trigger_delay' => 1,
            'rule_id' => $rule->id
        ));
        $RuleConditions->save($condition2);
        $this->singleSensorConditionHighId = $condition2->id;

        $condition3 = $RuleConditions->newEntity(array(
            'data_source' => $RuleConditions->enumValueToKey('data_source', 'Zone'),
            'data_id' => $zone->id,
            'trigger_threshold' => 75,
            'zone_behavior' => $RuleConditions->enumValueToKey('zone_behavior', 'Average Of Sensors'),
            'status' => $RuleConditions->enumValueToKey('status', 'Enabled'),
            'reset_threshold' => 76,
            'operator' => $RuleConditions->enumValueToKey('operator', '>'),
            'sensor_type' => $Sensors->enumValueToKey('sensor_type','Air Temperature'),
            'trigger_delay' => 1,
            'rule_id' => $rule->id
        ));
        $RuleConditions->save($condition3);
        $this->averageSensorsConditionId = $condition3->id;

        $ruleActionTarget = $RuleActionTargets->newEntity(array(
            'rule_action_id' => $action->id,
            'target_type' => $RuleActionTargets->enumValueToKey('target_type','Output'),
            'target_id' => $this->heaterId,
            'status' => $RuleActionTargets->enumValueToKey('status','Enabled')
        ));
        $RuleActionTargets->save($ruleActionTarget);
    }

    public function testZoneBehavior() {
        $this->Zones = TableRegistry::get('Zones');
        $this->DataPoints = TableRegistry::get('DataPoints');
        $this->RuleConditions = TableRegistry::get('RuleConditions');

        $singleSensorConditionLow = $this->RuleConditions->get($this->singleSensorConditionLowId);
        $singleSensorConditionHigh = $this->RuleConditions->get($this->singleSensorConditionHighId);
        $averageSensorsCondition = $this->RuleConditions->get($this->averageSensorsConditionId);

        $data = array('q' => '{"id":' . $this->deviceId1 . ',"st":1,"d":"[M1:10.2-67.34],[M2:40.2-68.18],[M3:200]"}');
        $this->post(
            '/api/raw',
            array('data' => $data, 'method' => 'get', 'return' => 'vars')
        );
        $this->assertEquals("{}", $this->_response->body());

        $dataPoints = $this->DataPoints->getDataPointsForCondition($singleSensorConditionLow);
        $dataPointValue = $this->DataPoints->getValueForRule($dataPoints, $singleSensorConditionLow);
        $this->assertEquals(67.34, $dataPointValue);

        $data = array('q' => '{"id":' . $this->deviceId1 . ',"st":1,"d":"[M1:10.2-77.34],[M2:40.2-78.18],[M3:300]"}');
        $this->post(
            '/api/raw',
            array('data' => $data, 'method' => 'get', 'return' => 'vars')
        );
        $this->assertEquals("{}", $this->_response->body());
        $dataPoints = $this->DataPoints->getDataPointsForCondition($singleSensorConditionHigh);
        $dataPointValue = $this->DataPoints->getValueForRule($dataPoints, $singleSensorConditionHigh);
        $this->assertEquals(78.18, $dataPointValue);

        $data = array('q' => '{"id":' . $this->deviceId1 . ',"st":1,"d":"[M1:10.2-87.34],[M2:40.2-88.18],[M3:400]"}');
        $this->post(
            '/api/raw',
            array('data' => $data, 'method' => 'get', 'return' => 'vars')
        );
        $this->assertEquals("{}", $this->_response->body());
        $dataPoints = $this->DataPoints->getDataPointsForCondition($averageSensorsCondition);
        $dataPointValue = $this->DataPoints->getValueForRule($dataPoints, $averageSensorsCondition);
        $this->assertEquals(87.76, $dataPointValue);
    }

}
