<?php

namespace App\Test\TestCase\Integration;

use Cake\TestSuite\IntegrationTestCase;
use App\Model\Table\DevicesTable;
use Cake\ORM\TableRegistry;
use RuntimeException;

class DefaultSetPointsTest extends IntegrationTestCase
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

    public function setUp()
    {
        parent::setUp();
        $SetPoints = TableRegistry::get('SetPoints');
        $Zones = TableRegistry::get('Zones');
        $Sensors = TableRegistry::get('Sensors');

        $zone = $Zones->newEntity([
            'label' => 'Test - Clone Zone',
            'status' => $Zones->enumValueToKey('status', 'Enabled'),
            'zone_type_id' => $Zones->enumValueToKey('zone_types', 'Room'),
            'plant_zone_type_id' => $Zones->enumValueToKey('plant_zone_types', 'Clone'),
            'floorplan_id' => 0
        ]);
        $Zones->save($zone);
        $this->zone = $zone;

        $defaultSetPoint = $SetPoints->newEntity([
            'label' => 'Default Clone Humidity Setpoint',
            'status' => $SetPoints->enumValueToKey('status', 'Enabled'),
            'value' => 70,
            'target_type' => $SetPoints->enumValueToKey('target_type', 'Zone Type'),
            'target_id' => $Zones->enumValueToKey('plant_zone_types', 'Clone'),
            'data_type' => $Sensors->enumValueToKey('sensor_types', 'Humidity'),
            'default_setpoint_id' => 0
        ]);
        $SetPoints->save($defaultSetPoint);
        $this->default_value = $defaultSetPoint->value;
        $this->default_setpoint_id = $defaultSetPoint->id; 

        $zoneSetPoint = $SetPoints->newEntity([
            'label' => 'Test - Clone Zone Humidity Setpoint',
            'status' => $SetPoints->enumValueToKey('status', 'Set'),
            'value' => NULL,
            'target_type' => $SetPoints->enumValueToKey('target_type', 'Zone'),
            'target_id' => $zone->id,
            'data_type' => $Sensors->enumValueToKey('sensor_types', 'Humidity'),
            'default_setpoint_id' => $defaultSetPoint->id
        ]);
        $SetPoints->save($zoneSetPoint);
        $this->zone_setpoint_id = $zoneSetPoint->id;
    }

    public function testDefaultSetPoint()
    {
        $SetPoints = TableRegistry::get('SetPoints');
        $Sensors = TableRegistry::get('Sensors');
        $setPoint = $SetPoints->getSetPointForTarget(
            $SetPoints->enumValueToKey('target_type', 'Zone'), 
            $this->zone, 
            $Sensors->enumValueToKey('sensor_types', 'Humidity')
        );
        $this->assertEquals($this->default_value, $setPoint->value);
        $this->assertEquals($this->default_setpoint_id, $setPoint->default_setpoint_id);
        
        //override Zone Setpoint
        $zoneSetPoint = $SetPoints->get($this->zone_setpoint_id);
        $zoneSetPoint->default_setpoint_id = 0;
        $zoneSetPoint->value = 80;
        $SetPoints->save($zoneSetPoint);

        $setPoint = $SetPoints->getSetPointForTarget(
            $SetPoints->enumValueToKey('target_type', 'Zone'), 
            $this->zone, 
            $Sensors->enumValueToKey('sensor_types', 'Humidity')
        );
        $this->assertEquals(80, $setPoint->value); 
        $this->assertEquals(0, $setPoint->default_setpoint_id);


        //revert to default
        $SetPoints->revertToDefaultSetPoint($setPoint, $show_metric = false); 
        $SetPoints->save($setPoint);

        $setPoint = $SetPoints->getSetPointForTarget(
            $SetPoints->enumValueToKey('target_type', 'Zone'), 
            $this->zone, 
            $Sensors->enumValueToKey('sensor_types', 'Humidity')
        ); 
        $this->assertEquals($this->default_value, $setPoint->value);
        $this->assertEquals($this->default_setpoint_id, $setPoint->default_setpoint_id);

    }
}
