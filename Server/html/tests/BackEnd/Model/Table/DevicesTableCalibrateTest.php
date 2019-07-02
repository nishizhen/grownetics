<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\DevicesTableCalibrate;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\DevicesTableCalibrate Test Case
 */
class DevicesTableCalibrateTest extends TestCase
{

    public $fixtures = ['app.devices'];
    /**
     * Test subject
     *
     * @var \App\Model\Table\DevicesTableCalibrate
     */
    public $DevicesTable;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->DevicesTable = TableRegistry::get('Devices');
    }

    public function testCalibrate()
    {
        $calibratedSensor = [
            'id' => 36,
            'sensor_type_id' => 4,
            'zone_id' => 5,
            'calibration' => 5,
            'sensor_type' => [
                'calibration_operator' => 'multiply'
            ]
        ];
        $result = $this->DevicesTable->calibrate($calibratedSensor, 20);

        $this->assertEquals(100, $result);

        $noDefaultCalibrationSensor = [
            'id' => 36,
            'sensor_type_id' => 4,
            'zone_id' => 5,
            'calibration' => NULL,
            'sensor_type' => [
                'calibration_operator' => 'multiply',
                'calibration_operand' => 5
            ]
        ];
        $result2 = $this->DevicesTable->calibrate($noDefaultCalibrationSensor, 30);

        $this->assertEquals(150, $result2);

    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->DevicesTable);

        parent::tearDown();
    }

}
