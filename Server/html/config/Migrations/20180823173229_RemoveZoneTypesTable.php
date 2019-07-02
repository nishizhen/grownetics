<?php
use Migrations\AbstractMigration;

class RemoveZoneTypesTable extends AbstractMigration
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
        $table = $this->table('zone_types');
        if ($table) {
            $this->dropTable('zone_types');
        }
    }
}
