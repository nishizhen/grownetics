<?php
use Migrations\AbstractMigration;

class AddDaysToBatchRecipeEntries extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function up()
    {
        $this->table('batch_recipe_entries')
        ->addColumn('days', 'integer', [
            'default' => null,
            'length' => 11,
            'null' => true,
        ])
        ->update();
    }

    public function down()
    {
        $this->table('batch_recipe_entries')
        ->removeColumn('days')
        ->update();
    }
}
