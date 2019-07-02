<?php
use Migrations\AbstractMigration;

class AddVPDToSensorTypes extends AbstractMigration
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
        $row = [
            [
                'sensor_type' => '12',
                'label' => 'Vapor Pressure Deficit Sensor',
                'calibration_operator' => 'multiply',
                'calibration_operand' => '1',
                'created' => '0000-00-00 00:00:00',
                'deleted' => '0000-00-00 00:00:00',
                'symbol' => 'mb',
                'display_class' => 'wi wi-raindrops',
                'metric_symbol' => ''
            ]
        ];
        $table->insert($row)->update();
    }
}
