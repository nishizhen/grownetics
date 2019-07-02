<?php
use Migrations\AbstractMigration;

class UpdateBREColumns extends AbstractMigration
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
        $table->changeColumn('recipe_entry_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => true
        ]);
        $table->changeColumn('recipe_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => true
        ]);
        $table->changeColumn('zone_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => true
        ]);
        $table->update();
    }
}
