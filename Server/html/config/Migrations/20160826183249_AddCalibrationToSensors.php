<?php
use Migrations\AbstractMigration;

class AddCalibrationToSensors extends AbstractMigration
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
        $table = $this->table('sensors');
        $table->changeColumn('calibration', 'decimal');
        $table->renameColumn('sensor_type', 'sensor_type_id');
        $table->update();
    }
}
