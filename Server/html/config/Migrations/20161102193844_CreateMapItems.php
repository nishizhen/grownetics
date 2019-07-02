<?php
use Migrations\AbstractMigration;

class CreateMapItems extends AbstractMigration
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
        $table->addColumn('latitude', 'decimal', [
            'default' => null,
            'null' => false,
            'precision' => 19,
            'scale' => 16
        ]);
        $table->addColumn('longitude', 'decimal', [
            'default' => null,
            'null' => false,
            'precision' => 19,
            'scale' => 16
        ]);
        $table->addColumn('geoJSON', 'text', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('offsetHeight', 'decimal', [
            'default' => null,
            'null' => false,
            'precision' => 12,
            'scale' => 4
        ]);
        $table->addColumn('label', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
        ]);
        $table->addColumn('floorplan_id', 'integer');
        $table->addColumn('map_item_type_id', 'integer');
        $table->addColumn('created', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('modified', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('deleted', 'boolean', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('deleted_date', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->create();
    }
}
