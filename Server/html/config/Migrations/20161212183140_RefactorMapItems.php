<?php
use Migrations\AbstractMigration;

class RefactorMapItems extends AbstractMigration
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
        foreach(['devices', 'sensors', 'zones'] as $tableName) {
            $table = $this->table($tableName);

            if ($table->hasColumn('latitude')) {
                $table->removeColumn('latitude');
            }
            if ($table->hasColumn('longitude')) {
                $table->removeColumn('longitude');
            }
            if ($table->hasColumn('offsetHeight')) {
                $table->removeColumn('offsetHeight');
            }
            if ($table->hasColumn('floorplan_id')) {
                $table->removeColumn('floorplan_id');
            }
            if ($table->hasColumn('geoJSON')) {
                $table->removeColumn('geoJSON');
            }
            if (!$table->hasColumn('map_item_id')) {
                $table->addColumn('map_item_id', 'integer');
            }

            $table->update();
        }
    }
}
