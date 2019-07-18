<?php

namespace App\Test\TestCase\Integration;

use Cake\TestSuite\IntegrationTestCase;
use App\Model\Table\DevicesTable;
use Cake\ORM\TableRegistry;

class BurnoutProtectionTest extends IntegrationTestCase {

    public $fixtures = [
        'app.floorplans',
        'app.notifications',
        'app.devices',
        'app.sensors',
        'app.sensors_zones',
        'app.zones',
        'app.rules',
        'app.outputs',
        'app.rule_conditions',
        'app.rule_actions',
        'app.rule_action_targets',
        'app.map_items',
        'app.map_item_types',
        'app.users_roles'
    ];

    public function setUp() {
        parent::setUp();

        $Rules = TableRegistry::get('Rules');
        $Devices = TableRegistry::get('Devices');
        $Sensors = TableRegistry::get('Sensors');
        $this->Outputs = TableRegistry::get('Outputs');
        $RuleConditions = TableRegistry::get('RuleConditions');
        $RuleActions = TableRegistry::get('RuleActions');
        $RuleActionTargets = TableRegistry::get("RuleActionTargets");

        $device = $Devices->newEntity(array(
            'label'=>'Power',
            'status'=>1,
            'doCreateSensors' => false,
            'latitude' => 1000.99999,
            'longitude' => 0.0000002,
            'floorplan_id' => 0,
            'type' => $Devices->enumValueToKey('type','Control')
        ));
        $Devices->save($device);

        $this->device_id = $device->api_id;
        $output = $this->Outputs->newEntity(array(
            'status'=>$this->Outputs->enumValueToKey('status','On'),
            'output_type'=>$this->Outputs->enumValueToKey('output_type','Relay Output'),
            'output_target'=>2,
            'device_id'=>$this->device_id
        ));
        $this->Outputs->save($output);

        $this->relay_output_id = $output->id;

        $sensor = $Sensors->newEntity(array(
            'status'=>1,
            'sensor_type_id'=> $Sensors->enumValueToKey('sensor_type','CT'),
            'sensor_pin'=>'A1',
            'device_id'=>$this->device_id
        ));
        $Sensors->save($sensor);
        $this->ct_sensor_id = $sensor->id;

        $rule = $Rules->newEntity(array(
            'status'            =>  $Rules->enumValueToKey('status','Enabled'),
        ));
        $Rules->save($rule);
        $ruleId = $rule->id;

        $action = $RuleActions->newEntity(array(
            'type'       =>  $RuleActions->enumValueToKey('type','Turn Off'),
            'status'            =>  $RuleActions->enumValueToKey('status','Enabled'),
            'notification_level'=>  $RuleActions->enumValueToKey('notification_level','Dashboard Notification'),
            'rule_id'           => $ruleId,
            'on_trigger'        => 1
        ));
        $RuleActions->save($action);

        $ruleActionTarget = $RuleActionTargets->newEntity(array(
            'rule_action_id' => $action->id,
            'target_type' => $RuleActionTargets->enumValueToKey('target_type','Output'),
            'target_id' => $this->relay_output_id,
            'status' => $RuleActionTargets->enumValueToKey('status','Enabled')
        ));
        $RuleActionTargets->save($ruleActionTarget);

        $condition = $RuleConditions->newEntity(array(
            'data_source'       =>  $RuleConditions->enumValueToKey('data_source','Data Point'),
            'data_id'           =>  $this->ct_sensor_id,
            'trigger_threshold' =>  10,
            'zone_behavior'		=>  $RuleConditions->enumValueToKey('zone_behavior', 'Single Sensor'),
            'status'		=>  $RuleConditions->enumValueToKey('status', 'Enabled'),
            'reset_threshold'   =>  7,
            'autoreset'         =>  0,
            'operator'=>  0,
            'sensor_type'=>  $Sensors->enumValueToKey('sensor_type','CT'),
            'rule_id' => $ruleId
        ));
        $RuleConditions->save($condition);

    }

    public function testBurnoutProtection()
    {
        $data = array('q'=>'{"id":'.$this->device_id.',"st":1,"d":"[A1:6]"}');
        $result = $this->post(
            '/api/raw',
            array('data' => $data, 'method' => 'get', 'return' => 'vars')
        );

        # The device is not booting, we should get outputs back
        $this->assertResponseOk();
        $this->assertEquals("{\"outs\":[\"2\"]}",$this->_response->body());

        $data = array('q'=>'{"id":'.$this->device_id.',"st":0,"b":0}');
        $result = $this->post(
            '/api/raw',
            array('data' => $data, 'method' => 'get', 'return' => 'vars')
        );

        # The device just booted, we should not get outputs back
        $this->assertResponseOk();
        $this->assertEquals('{"boot":1,"i1":[],"i2":[],"i4":[],"i5":[],"i6":[],"i7":[],"i8":["A1"],"i9":[],"i11":[],"i12":[],"i13":[],"i14":[]}',$this->_response->body());

        $data = array('q'=>'{"id":'.$this->device_id.',"st":1,"d":"[A1:6]"}');
        $result = $this->post(
            '/api/raw',
            array('data' => $data, 'method' => 'get', 'return' => 'vars')
        );

        # The device just booted, we should not get outputs back
        $this->assertResponseOk();
        $this->assertEquals("{}",$this->_response->body());

        # Set device burnout_protection_time in consul back 11 minutes
        $current_time = time() - 660;
        $sf = new \SensioLabs\Consul\ServiceFactory(['base_uri' => 'http://consul:8500']);
        $kv = $sf->get('kv');
        $kv->put('devices/'.$this->device_id.'/burnout_protection_time', $current_time);

        $data = array('q'=>'{"id":'.$this->device_id.',"st":1,"d":"[A1:6]"}');
        $result = $this->post(
            '/api/raw',
            array('data' => $data, 'method' => 'get', 'return' => 'vars')
        );

        # The device is not booting, we should get outputs back
        $this->assertResponseOk();
        $this->assertEquals("{\"outs\":[\"2\"]}",$this->_response->body());
    }

}
