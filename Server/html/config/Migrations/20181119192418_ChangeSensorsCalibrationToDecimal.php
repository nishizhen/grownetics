<?php
use Migrations\AbstractMigration;

class ChangeSensorsCalibrationToDecimal extends AbstractMigration
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
        $table->changeColumn('calibration', 'decimal', [
                'default' => 0,
                'precision' => 12,
                'scale' => 8,
                'null' => true
            ]);
        $table->update();
    }
}
