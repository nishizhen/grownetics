<?php
use Migrations\AbstractMigration;

class AddBatchRecipeToHarvestBatches extends AbstractMigration
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
        $table = $this->table('harvestbatches');
        $table->addColumn('recipe_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->update();

        $table = $this->table('batch_recipe_entries');
        $table->addColumn('batch_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->update();
    }
}
