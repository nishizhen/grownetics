<?php
use Migrations\AbstractMigration;

class AddTaskIdToBatchRecipeEntries extends AbstractMigration
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
        $table = $this->table('batch_recipe_entries')
            ->addColumn('task_id', 'integer', [
                'default' => null,
                'length' => 11,
                'null' => true
            ]);
        $table->update();
    }
}
