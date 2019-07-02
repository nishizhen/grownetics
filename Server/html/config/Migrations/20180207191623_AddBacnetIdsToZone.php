<?php
use Migrations\AbstractMigration;

class AddBacnetIdsToZone extends AbstractMigration
{

    public function up()
    {

        $this->table('zones')
            ->addColumn('bacnet_hum_read', 'integer', [
                'after' => 'plant_zone_type_id',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->addColumn('bacnet_hum_set', 'integer', [
                'after' => 'bacnet_hum_read',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->addColumn('bacnet_temp_read', 'integer', [
                'after' => 'bacnet_hum_set',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->addColumn('bacnet_temp_set', 'integer', [
                'after' => 'bacnet_temp_read',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->addColumn('bacnet_timestamp', 'integer', [
                'after' => 'bacnet_temp_set',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('zones')
            ->removeColumn('bacnet_hum_read')
            ->removeColumn('bacnet_hum_set')
            ->removeColumn('bacnet_temp_read')
            ->removeColumn('bacnet_temp_set')
            ->removeColumn('bacnet_timestamp')
            ->update();
    }
}

