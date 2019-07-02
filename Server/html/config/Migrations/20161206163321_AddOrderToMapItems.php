<?php
use Migrations\AbstractMigration;

class AddOrderToMapItems extends AbstractMigration
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
        $table = $this->table('map_items');
        $table->addColumn('ordinal', 'integer', [
            'default' => null
            //'limit' => 11,
            //'null' => false,
        ]);
        $table->update();
    }
}
