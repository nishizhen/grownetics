<?php
use Migrations\AbstractMigration;

class AllowZoneIdNullInTasks extends AbstractMigration
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
        $table->changeColumn('zone_id', 'integer', [
            'null' => true
        ]);
        $table->update();
    }
}
