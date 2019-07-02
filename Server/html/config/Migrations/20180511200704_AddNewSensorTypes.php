<?php
use Migrations\AbstractMigration;

class AddNewSensorTypes extends AbstractMigration
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
                'sensor_type' => '12',
                'label' => 'Atlas Scientific RTD',
                'calibration_operator' => 'multiply',
                'calibration_operand' => '1',
                'created' => '0000-00-00 00:00:00',
                'deleted' => '0000-00-00 00:00:00',
                'symbol' => '&#8457;',
                'display_class' => 'wi wi-thermometer',
                'metric_symbol' => '&#8451;',
            ],
            [
                'sensor_type' => '13',
                'label' => 'Soil Moisture',
                'calibration_operator' => 'multiply',
                'calibration_operand' => '1',
                'created' => '0000-00-00 00:00:00',
                'deleted' => '0000-00-00 00:00:00',
                'symbol' => '&#37;',
                'display_class' => 'wi wi-humidity',
                'metric_symbol' => '',
            ],
        ];

        $this->insert('sensor_types', $rows);
        $table->update();
    }
}
