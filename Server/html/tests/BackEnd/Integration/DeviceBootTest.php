<?php

namespace App\Test\TestCase\Integration;

use Cake\TestSuite\IntegrationTestCase;
use App\Model\Table\DevicesTable;
use Cake\ORM\TableRegistry;

class DeviceBootTest extends IntegrationTestCase {

    public $fixtures = [
        'app.floorplans',
        'app.notifications',
        'app.devices',
        'app.sensors',
        'app.zones',
        'app.rules',
        'app.outputs',
        'app.map_items',
        'app.map_item_types'
    ];

    public function setUp()
    {
        parent::setUp();
        $this->Devices = TableRegistry::get('Devices');
        $device = $this->Devices->newEntity([
            'doCreateSensors' => false,
            'latitude' => 1000.99999,
            'longitude' => 0.0000002,
            'label' => 'Rick',
            'floorplan_id' => 0
        ]);

        if ($this->Devices->save($device)) {
            $this->deviceId = $device->api_id;
        }
    }

    public function testBootingPostData() {
        $data = array('q'=>'{"id":'.$this->deviceId.',"b":0}');

        $result = $this->post(
            '/api/raw',
            array('data' => $data)
        );
        $this->assertResponseOk();
        // debug(json_decode($this->_response->body()));
        $this->assertContains('i1',$this->_response->body());
        $this->assertContains('i2',$this->_response->body());
        $this->assertContains('i9',$this->_response->body());
    }
}
