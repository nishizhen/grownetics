<?php
use Migrations\AbstractMigration;

class AddDefaultSetPointIdToSetPoints extends AbstractMigration
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
        $table = $this->table('set_points');
        $table->addColumn('default_setpoint_id', 'integer', [
            'default' => 0,
            'length' => 11
        ]);
        $table->addColumn('alert_level', 'integer', [
            'default' => 0,
            'length' => 11
        ]);
        $table->update();
    }
}
