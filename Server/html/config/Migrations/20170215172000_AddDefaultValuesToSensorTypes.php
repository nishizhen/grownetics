<?php
use Migrations\AbstractMigration;

class AddDefaultValuesToSensorTypes extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $table = $this->table('sensor_types');
        $rows = [
            [
                'id'    => 1,
                'sensor_type'  => '1',
                'label' => 'Waterproof Temperature Sensor',
                'calibration_operator' => 'multiply',
                'calibration_operand' => 1,
                'symbol' => '&degF',
                'display_class' => 'wi wi-raindrops'
            ],
            [
                'id'    => 2,
                'sensor_type'  => '2',
                'label' => 'Humidity Sensor',
                'calibration_operator' => 'multiply',
                'calibration_operand' => 1,
                'symbol' => '&#37',
                'display_class' => 'wi wi-humidity'
            ],
            [
                'id'    => 3,
                'sensor_type'  => '3',
                'label' => 'Air Temperature Sensor',
                'calibration_operator' => 'multiply',
                'calibration_operand' => 1,
                'symbol' => '&degF',
                'display_class' => 'wi wi-thermometer'
            ],
            [
                'id'    => 4,
                'sensor_type'  => '4',
                'label' => 'Co2 Sensor',
                'calibration_operator' => 'multiply',
                'calibration_operand' => 1,
                'symbol' => 'ppm',
                'display_class' => 'wi wi-barometer'
            ],
            [
                'id'    => 5,
                'sensor_type'  => '5',
                'label' => 'pH Sensor',
                'calibration_operator' => 'multiply',
                'calibration_operand' => 1,
                'symbol' => '&degF',
                'display_class' => 'wi wi-raindrop'
            ],
            [
                'id'    => 6,
                'sensor_type'  => '6',
                'label' => 'DO Sensor',
                'calibration_operator' => 'multiply',
                'calibration_operand' => 1,
                'symbol' => '',
                'display_class' => 'wi wi-humidity'
            ],
            [
                'id'    => 7,
                'sensor_type'  => '7',
                'label' => 'EC Sensor',
                'calibration_operator' => 'multiply',
                'calibration_operand' => 1,
                'symbol' => '&mu;S',
                'display_class' => 'wi wi-dust'
            ],
            [
                'id'    => 8,
                'sensor_type'  => '8',
                'label' => 'CT Sensor',
                'calibration_operator' => 'multiply',
                'calibration_operand' => 1,
                'symbol' => '',
                'display_class' => 'wi wi-lightning'
            ],
            [
                'id'    => 9,
                'sensor_type'  => '9',
                'label' => 'Fill Level Sensor',
                'calibration_operator' => 'multiply',
                'calibration_operand' => 1,
                'symbol' => '',
                'display_class' => 'wi wi-flood'
            ],
            [
                'id'    => 10,
                'sensor_type'  => '11',
                'label' => 'PAR Sensor',
                'calibration_operator' => 'multiply',
                'calibration_operand' => 5,
                'symbol' => 'nm',
                'display_class' => 'wi wi-lightning'
            ],
        ];

        $this->insert('sensor_types', $rows);
        $table->update();
    }
}
