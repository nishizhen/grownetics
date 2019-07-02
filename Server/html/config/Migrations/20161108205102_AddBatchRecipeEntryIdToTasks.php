<?php
use Migrations\AbstractMigration;

class AddBatchRecipeEntryIdToTasks extends AbstractMigration
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
        $table->addColumn('batch_recipe_entry_id', 'integer', [
            'default' => null,
            'null' => false,
            'limit' => 11,
        ]);
        $table->update();
    }
}
