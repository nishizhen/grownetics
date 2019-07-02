<?php
use Migrations\AbstractMigration;

class RenameZoneTypeIdInRecipeEntries extends AbstractMigration
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
        $table = $this->table('recipe_entries');
        if ($table->hasColumn('zone_type_id')) {
            $table->removeColumn('zone_type_id');
        }
        $table->addColumn('plant_zone_type_id', 'integer', ['default' => null]);
        $table->update();

        $table = $this->table('batch_recipe_entries');
        if ($table->hasColumn('zone_type_id')) {
            $table->removeColumn('zone_type_id');
        }
        $table->addColumn('zone_id', 'integer', ['default' => null]);
        $table->update();
        
        $table = $this->table('tasks');
        if ($table->hasColumn('zone_type_id')) {
            $table->removeColumn('zone_type_id');
        }
        $table->update();
    }
}
