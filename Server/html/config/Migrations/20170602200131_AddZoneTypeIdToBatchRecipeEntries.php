<?php
use Migrations\AbstractMigration;

class AddZoneTypeIdToBatchRecipeEntries extends AbstractMigration
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
        $table = $this->table('batch_recipe_entries');
        if ($table->hasColumn('zone_id')) {
            $table->removeColumn('zone_id');
        }
        $table->addColumn('zone_type_id', 'integer');
        $table->update();
    }
}
