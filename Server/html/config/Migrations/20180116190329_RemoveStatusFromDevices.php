<?php
use Migrations\AbstractMigration;

class RemoveStatusFromDevices extends AbstractMigration
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
        $table = $this->table('devices');
        if ($table->hasColumn('status')) {
            $table->removeColumn('status');
        }
        $table->update();
    }
}
