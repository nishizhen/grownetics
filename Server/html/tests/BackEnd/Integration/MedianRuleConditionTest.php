<?php

namespace App\Test\TestCase\Integration;

use Cake\TestSuite\IntegrationTestCase;
use App\Model\Table\DevicesTable;
use Cake\ORM\TableRegistry;
use RuntimeException;

class MedianRuleConditionTest extends IntegrationTestCase
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
        $Outputs = TableRegistry::get('Outputs');
        $Devices = TableRegistry::get('Devices');
        $Sensors = TableRegistry::get('Sensors');
        $SensorsZones = TableRegistry::get('SensorsZones');
        $Zones = TableRegistry::get('Zones');
        $Rules = TableRegistry::get('Rules');
        $RuleConditions = TableRegistry::get('RuleConditions');
        $RuleActions = TableRegistry::get('RuleActions');
        $RuleActionTargets = TableRegistry::get('RuleActionTargets');

        $crop_device = $Devices->newEntity([
            'label' => '3D Crop Device 1',
            'floorplan_id' => 0,
            'doCreateSensors' => false
        ]);
        $Devices->save($crop_device);
        $this->crop_device_id = $crop_device->id;

        $crop_device2 = $Devices->newEntity([
            'label' => '3D Crop Device 2',
            'floorplan_id' => 0,
            'doCreateSensors' => false
        ]);
        $Devices->save($crop_device2);
        $this->crop_device_id2 = $crop_device2->id;

        $crop_device3 = $Devices->newEntity([
            'label' => '3D Crop Device 3',
            'floorplan_id' => 0,
            'doCreateSensors' => false
        ]);
        $Devices->save($crop_device3);
        $this->crop_device_id3 = $crop_device3->id;

        $zone = $Zones->newEntity([
            'label' => 'Flower Zone',
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
            'sensor_type_id' => $Sensors->enumValueToKey('sensor_types', 'Humidity'),
            'floorplan_id' => 0
        ));
        $Sensors->save($sensor1);
        $SensorsZones->save($SensorsZones->newEntity(['zone_id'=>$zone->id,'sensor_id'=>$sensor1->id]));

        $sensor2 = $Sensors->newEntity(array(
            'label' => 'Low - Temperature Sensor',
            'status' => $Sensors->enumValueToKey('status', 'Enabled'),
            'sensor_pin' => 'M1',
            'device_id' => $crop_device->id,
            'sensor_type_id' => $Sensors->enumValueToKey('sensor_types', 'Air Temperature'),
            'floorplan_id' => 0
        ));
        $Sensors->save($sensor2);
        $SensorsZones->save($SensorsZones->newEntity(['zone_id'=>$zone->id,'sensor_id'=>$sensor2->id]));

        $sensor3 = $Sensors->newEntity(array(
            'label' => 'High - Humidity Sensor',
            'status' => $Sensors->enumValueToKey('status', 'Enabled'),
            'sensor_pin' => 'M2',
            'device_id' => $crop_device->id,
            'sensor_type_id' => $Sensors->enumValueToKey('sensor_types', 'Humidity'),
            'floorplan_id' => 0
        ));
        $Sensors->save($sensor3);
        $SensorsZones->save($SensorsZones->newEntity(['zone_id'=>$zone->id,'sensor_id'=>$sensor3->id]));

        $sensor4 = $Sensors->newEntity(array(
            'label' => 'High - Temperature Sensor',
            'status' => $Sensors->enumValueToKey('status', 'Enabled'),
            'sensor_pin' => 'M2',
            'device_id' => $crop_device->id,
            'sensor_type_id' => $Sensors->enumValueToKey('sensor_types', 'Air Temperature'),
            'floorplan_id' => 0
        ));
        $Sensors->save($sensor4);
        $SensorsZones->save($SensorsZones->newEntity(['zone_id'=>$zone->id,'sensor_id'=>$sensor4->id]));

        $sensor5 = $Sensors->newEntity(array(
            'label' => 'High - Co2 Sensor',
            'status' => $Sensors->enumValueToKey('status', 'Enabled'),
            'sensor_pin' => 'M3',
            'device_id' => $crop_device->id,
            'sensor_type_id' => $Sensors->enumValueToKey('sensor_types', 'Co2'),
            'floorplan_id' => 0
        ));
        $Sensors->save($sensor5);
        $SensorsZones->save($SensorsZones->newEntity(['zone_id'=>$zone->id,'sensor_id'=>$sensor5->id]));

        $sensor6 = $Sensors->newEntity(array(
            'label' => 'Low - Humidity Sensor',
            'status' => $Sensors->enumValueToKey('status', 'Enabled'),
            'sensor_pin' => 'M1',
            'device_id' => $crop_device2->id,
            'sensor_type_id' => $Sensors->enumValueToKey('sensor_types', 'Humidity'),
            'floorplan_id' => 0
        ));
        $Sensors->save($sensor6);
        $SensorsZones->save($SensorsZones->newEntity(['zone_id'=>$zone->id,'sensor_id'=>$sensor6->id]));

        $sensor7 = $Sensors->newEntity(array(
            'label' => 'Low - Temperature Sensor',
            'status' => $Sensors->enumValueToKey('status', 'Enabled'),
            'sensor_pin' => 'M1',
            'device_id' => $crop_device2->id,
            'sensor_type_id' => $Sensors->enumValueToKey('sensor_types', 'Air Temperature'),
            'floorplan_id' => 0
        ));
        $Sensors->save($sensor7);
        $SensorsZones->save($SensorsZones->newEntity(['zone_id'=>$zone->id,'sensor_id'=>$sensor7->id]));

        $sensor8 = $Sensors->newEntity(array(
            'label' => 'High - Humidity Sensor',
            'status' => $Sensors->enumValueToKey('status', 'Enabled'),
            'sensor_pin' => 'M2',
            'device_id' => $crop_device2->id,
            'sensor_type_id' => $Sensors->enumValueToKey('sensor_types', 'Humidity'),
            'floorplan_id' => 0
        ));
        $Sensors->save($sensor8);
        $SensorsZones->save($SensorsZones->newEntity(['zone_id'=>$zone->id,'sensor_id'=>$sensor8->id]));

        $sensor9 = $Sensors->newEntity(array(
            'label' => 'High - Temperature Sensor',
            'status' => $Sensors->enumValueToKey('status', 'Enabled'),
            'sensor_pin' => 'M2',
            'device_id' => $crop_device2->id,
            'sensor_type_id' => $Sensors->enumValueToKey('sensor_types', 'Air Temperature'),
            'floorplan_id' => 0
        ));
        $Sensors->save($sensor9);
        $SensorsZones->save($SensorsZones->newEntity(['zone_id'=>$zone->id,'sensor_id'=>$sensor9->id]));

        $sensor10 = $Sensors->newEntity(array(
            'label' => 'High - Co2 Sensor',
            'status' => $Sensors->enumValueToKey('status', 'Enabled'),
            'sensor_pin' => 'M3',
            'device_id' => $crop_device2->id,
            'sensor_type_id' => $Sensors->enumValueToKey('sensor_types', 'Co2'),
            'floorplan_id' => 0
        ));
        $Sensors->save($sensor10);
        $SensorsZones->save($SensorsZones->newEntity(['zone_id'=>$zone->id,'sensor_id'=>$sensor10->id]));

        $sensor11 = $Sensors->newEntity(array(
            'label' => 'Low - Humidity Sensor',
            'status' => $Sensors->enumValueToKey('status', 'Enabled'),
            'sensor_pin' => 'M1',
            'device_id' => $crop_device3->id,
            'sensor_type_id' => $Sensors->enumValueToKey('sensor_types', 'Humidity'),
            'floorplan_id' => 0
        ));
        $Sensors->save($sensor11);
        $SensorsZones->save($SensorsZones->newEntity(['zone_id'=>$zone->id,'sensor_id'=>$sensor11->id]));

        $sensor12 = $Sensors->newEntity(array(
            'label' => 'Low - Temperature Sensor',
            'status' => $Sensors->enumValueToKey('status', 'Enabled'),
            'sensor_pin' => 'M1',
            'device_id' => $crop_device3->id,
            'sensor_type_id' => $Sensors->enumValueToKey('sensor_types', 'Air Temperature'),
            'floorplan_id' => 0
        ));
        $Sensors->save($sensor12);
        $SensorsZones->save($SensorsZones->newEntity(['zone_id'=>$zone->id,'sensor_id'=>$sensor12->id]));

        $sensor13 = $Sensors->newEntity(array(
            'label' => 'High - Humidity Sensor',
            'status' => $Sensors->enumValueToKey('status', 'Enabled'),
            'sensor_pin' => 'M2',
            'device_id' => $crop_device3->id,
            'sensor_type_id' => $Sensors->enumValueToKey('sensor_types', 'Humidity'),
            'floorplan_id' => 0
        ));
        $Sensors->save($sensor13);
        $SensorsZones->save($SensorsZones->newEntity(['zone_id'=>$zone->id,'sensor_id'=>$sensor13->id]));

        $sensor14 = $Sensors->newEntity(array(
            'label' => 'High - Temperature Sensor',
            'status' => $Sensors->enumValueToKey('status', 'Enabled'),
            'sensor_pin' => 'M2',
            'device_id' => $crop_device3->id,
            'sensor_type_id' => $Sensors->enumValueToKey('sensor_types', 'Air Temperature'),
            'floorplan_id' => 0
        ));
        $Sensors->save($sensor14);
        $SensorsZones->save($SensorsZones->newEntity(['zone_id'=>$zone->id,'sensor_id'=>$sensor14->id]));

        $sensor15 = $Sensors->newEntity(array(
            'label' => 'High - Co2 Sensor',
            'status' => $Sensors->enumValueToKey('status', 'Enabled'),
            'sensor_pin' => 'M3',
            'device_id' => $crop_device3->id,
            'sensor_type_id' => $Sensors->enumValueToKey('sensor_types', 'Co2'),
            'floorplan_id' => 0
        ));
        $Sensors->save($sensor15);
        $SensorsZones->save($SensorsZones->newEntity(['zone_id'=>$zone->id,'sensor_id'=>$sensor15->id]));

        $rule = $Rules->newEntity([
            'label' => 'Test - Rule 1',
            'status' => $Rules->enumValueToKey('status', 'Enabled'),
            'autoreset' =>  true,
            'is_default' => false
        ]);
        $Rules->save($rule);

        $average_rule_condition = $RuleConditions->newEntity([
            'label' => 'Dashboard Alarm - The Average Humidity in Flower is over 70%',
            'data_source' => $RuleConditions->enumValueToKey('data_source', 'Zone'),
            'sensor_type' => $Sensors->enumValueToKey('sensor_type', 'Humidity'),
            'data_id' => $zone->id,
            'operator' => $RuleConditions->enumValueToKey('operator', '>'),
            'trigger_threshold' => 70,
            'reset_threshold' => 69,
            'status' => $RuleConditions->enumValueToKey('status', 'Enabled'),
            'zone_behavior' => $RuleConditions->enumValueToKey('zone_behavior', 'Average Of Sensors'),
            'trigger_delay' => NULL,
            'pending_time' => NULL,
            'rule_id' => $rule->id,
            'averaging_method' => $RuleConditions->enumValueToKey('averaging_method', 'Average'),
            'is_default' => false
        ]);
        $RuleConditions->save($average_rule_condition);
        $this->average_rule_condition_id = $average_rule_condition->id;

        $rule2 = $Rules->newEntity([
            'label' => 'Test - Rule 2',
            'status' => $Rules->enumValueToKey('status', 'Enabled'),
            'autoreset' =>  true,
            'is_default' => false
        ]);
        $Rules->save($rule2);

        $median_rule_condition = $RuleConditions->newEntity([
            'label' => 'Dashboard Alarm - The Median Humidity in Flower is over 70%',
            'data_source' => $RuleConditions->enumValueToKey('data_source', 'Zone'),
            'sensor_type' => $Sensors->enumValueToKey('sensor_type', 'Humidity'),
            'data_id' => $zone->id,
            'operator' => $RuleConditions->enumValueToKey('operator', '>'),
            'trigger_threshold' => 70,
            'reset_threshold' => 69,
            'status' => $RuleConditions->enumValueToKey('status', 'Enabled'),
            'zone_behavior' => $RuleConditions->enumValueToKey('zone_behavior', 'Average Of Sensors'),
            'trigger_delay' => NULL,
            'pending_time' => NULL,
            'rule_id' => $rule2->id,
            'averaging_method' => $RuleConditions->enumValueToKey('averaging_method', 'Median'),
            'is_default' => false
        ]);
        $RuleConditions->save($median_rule_condition);
        $this->median_rule_condition_id = $median_rule_condition->id;

        $rule_action = $RuleActions->newEntity([
            'type' => $RuleActions->enumValueToKey('type', 'Notification Only'),
            'notification_level' => $RuleActions->enumValueToKey('notification_level', 'Dashboard Alarm'),
            'notification_user_role' => NULL,
            'rule_id' => $rule->id,
            'on_trigger' => true,
            'is_default' => false
        ]);
        $RuleActions->save($rule_action);

        $rule_action2 = $RuleActions->newEntity([
            'type' => $RuleActions->enumValueToKey('type', 'Notification Only'),
            'notification_level' => $RuleActions->enumValueToKey('notification_level', 'Dashboard Alarm'),
            'notification_user_role' => NULL,
            'rule_id' => $rule2->id,
            'on_trigger' => true,
            'is_default' => false
        ]);
        $RuleActions->save($rule_action2);

    }

    public function testFaultySensor()
    {
        $DataPoints = TableRegistry::get('DataPoints');
        $RuleConditions = TableRegistry::get('RuleConditions');
        $Zones = TableRegistry::get('Zones');
        $Notifications = TableRegistry::get('Notifications');

        // send normal data
        $data = array('q' => '{"id":' . $this->crop_device_id . ',"st":1,"d":"[M1:64.0-24.0],[M2:60.0-23.0],[M3:1110]"}');
        $this->post(
            '/api/raw',
            array('data' => $data, 'method' => 'get', 'return' => 'vars')
        );
        $this->assertEquals("{}", $this->_response->body());

        // send normal data
        $data = array('q' => '{"id":' . $this->crop_device_id2 . ',"st":1,"d":"[M1:66.0-24.0],[M2:62.0-23.0],[M3:1110]"}');
        $this->post(
            '/api/raw',
            array('data' => $data, 'method' => 'get', 'return' => 'vars')
        );
        $this->assertEquals("{}", $this->_response->body());

        // send crazy high data simulating faulty sensor
        $data = array('q' => '{"id":' . $this->crop_device_id3 . ',"st":1,"d":"[M1:140.0-24.0],[M2:140.0-23.0],[M3:1110]"}');
        $this->post(
            '/api/raw',
            array('data' => $data, 'method' => 'get', 'return' => 'vars')
        );
        $this->assertEquals("{}", $this->_response->body());
        $Zones->processRules();

        // average triggered, median not yet
        $average_rule_condition = $RuleConditions->get($this->average_rule_condition_id);
        $this->assertEquals(
            $RuleConditions->enumValueToKey('status', 'Triggered'), 
            $average_rule_condition['status']
        );

        $median_rule_condition = $RuleConditions->get($this->median_rule_condition_id);
        $this->assertEquals(
            $RuleConditions->enumValueToKey('status', 'Enabled'), 
            $median_rule_condition['status']
        );

        // send normal data lower than median
        $data = array('q' => '{"id":' . $this->crop_device_id . ',"st":1,"d":"[M1:60.0-24.0],[M2:55.0-23.0],[M3:1110]"}');
        $this->post(
            '/api/raw',
            array('data' => $data, 'method' => 'get', 'return' => 'vars')
        );
        $this->assertEquals("{}", $this->_response->body());

        // send normal data but median will be > 70
        $data = array('q' => '{"id":' . $this->crop_device_id2 . ',"st":1,"d":"[M1:72.0-24.0],[M2:75.0-23.0],[M3:1110]"}');
        $this->post(
            '/api/raw',
            array('data' => $data, 'method' => 'get', 'return' => 'vars')
        );
        $this->assertEquals("{}", $this->_response->body());

        // send normal data higher than median
        $data = array('q' => '{"id":' . $this->crop_device_id3 . ',"st":1,"d":"[M1:80.0-24.0],[M2:78.0-23.0],[M3:1110]"}');
        $this->post(
            '/api/raw',
            array('data' => $data, 'method' => 'get', 'return' => 'vars')
        );
        $this->assertEquals("{}", $this->_response->body());
        $Zones->processRules();

        $median_rule_condition = $RuleConditions->get($this->median_rule_condition_id);
        $this->assertEquals($RuleConditions->enumValueToKey('status', 'Triggered'), $median_rule_condition['status']);
    }
}
