<?php
use Migrations\AbstractMigration;

class AddTaskRelatedFieldsToRecipeEntries extends AbstractMigration
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
        $table = $this->table('recipe_entries')
            ->addColumn('parent_recipe_entry_id', 'integer', [
                'default' => null,
                'length' => 11,
                'null' => true
            ]);
        $table->addColumn('task_type_id', 'integer', [
            'default' => null,
            'length' => 11,
            'null' => true
        ]);
        $table->addColumn('task_label', 'string', [
           'default' => null,
           'length' => 255,
            'null' => true
        ]);
        $table->update();
    }
}
