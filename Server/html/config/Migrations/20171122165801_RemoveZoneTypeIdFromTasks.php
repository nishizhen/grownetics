<?php
use Migrations\AbstractMigration;

class RemoveZoneTypeIdFromTasks extends AbstractMigration
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
        $table = $this->table('tasks');
        $table->addColumn('zone_type_id', 'integer', ['default' => null]);
        $table->update();
        $table->removeColumn('zone_type_id');
        $table->update();
    }
}
