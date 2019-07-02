<?php
use Migrations\AbstractMigration;

class UnrestrictZoneIds extends AbstractMigration
{

    public function up()
    {

        $this->table('map_items')
            ->changeColumn('zone_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->update();

        $this->table('zones')
            ->changeColumn('room_zone_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->changeColumn('plant_zone_type_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('map_items')
            ->changeColumn('zone_id', 'integer', [
                'default' => null,
                'length' => 11,
                'null' => false,
            ])
            ->update();

        $this->table('zones')
            ->changeColumn('room_zone_id', 'integer', [
                'default' => null,
                'length' => 11,
                'null' => false,
            ])
            ->changeColumn('plant_zone_type_id', 'integer', [
                'default' => null,
                'length' => 11,
                'null' => false,
            ])
            ->update();
    }
}

