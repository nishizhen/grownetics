<?php
use Migrations\AbstractMigration;

class CreateMapItemsZones extends AbstractMigration
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
        $table = $this->table('map_items_zones');
        $table->addColumn('map_item_id', 'integer');
        $table->addColumn('zone_id', 'integer');
        $table->create();
    }
}
