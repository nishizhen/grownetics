<?php
use Migrations\AbstractMigration;

class AddingSoftDeleteToAllFloorplanImportedItems extends AbstractMigration
{

    public function up()
    {

        $this->table('appliance_templates')
            ->addColumn('deleted_date', 'datetime', [
                'after' => 'modified',
                'default' => null,
                'length' => null,
                'null' => true,
            ])
            ->update();

        $this->table('appliances')
            ->addColumn('deleted_date', 'datetime', [
                'after' => 'modified',
                'default' => null,
                'length' => null,
                'null' => true,
            ])
            ->update();

        $this->table('appliances_zones')
            ->addColumn('deleted_date', 'datetime', [
                'after' => 'zone_id',
                'default' => null,
                'length' => null,
                'null' => true,
            ])
            ->update();

        $this->table('sensors_zones')
            ->addColumn('deleted_date', 'datetime', [
                'after' => 'zone_id',
                'default' => null,
                'length' => null,
                'null' => true,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('appliance_templates')
            ->removeColumn('deleted_date')
            ->update();

        $this->table('appliances')
            ->removeColumn('deleted_date')
            ->update();

        $this->table('appliances_zones')
            ->removeColumn('deleted_date')
            ->update();

        $this->table('sensors_zones')
            ->removeColumn('deleted_date')
            ->update();
    }
}

