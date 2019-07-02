<?php
use Migrations\AbstractMigration;

class AddPlantZoneTypeIdToZones extends AbstractMigration
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
        $table = $this->table('zones');
        $table->addColumn('plant_zone_type_id', 'integer', ['default' => null]);
        $table->update();

        $this->execute('DELETE FROM zone_types');
         $rows = [
            [
                'id'    => 1,
                'label'  => 'Room'
            ],
            [
                'id'    => 2,
                'label'  => 'HVAC'
            ],
            [
                'id'    => 3,
                'label'  => 'Group'
            ],
            [
                'id'    => 4,
                'label'  => 'Custom'
            ]
        ];
        $this->insert('zone_types', $rows);
        $rows = [
            [
                'id'    => 1,
                'label'  => 'Clone Humidity',
                'status' => 2,
                'value' => 70,
                'target_type' => 1,
                'target_id' => 1,
                'data_type' => 2
            ],
            [
                'id'    => 2,
                'label'  => 'Clone Air Temperature',
                'status' => 2,
                'value' => 24,
                'target_type' => 1,
                'target_id' => 1,
                'data_type' => 3
            ],
            [
                'id'    => 3,
                'label'  => 'Clone Co2',
                'status' => 2,
                'value' => 100,
                'target_type' => 1,
                'target_id' => 1,
                'data_type' => 4
            ],
            [
                'id'    => 4,
                'label'  => 'Veg Humidity',
                'status' => 2,
                'value' => 85,
                'target_type' => 1,
                'target_id' => 2,
                'data_type' => 2
            ],
            [
                'id'    => 5,
                'label'  => 'Veg Air Temperature',
                'status' => 2,
                'value' => 24,
                'target_type' => 1,
                'target_id' => 2,
                'data_type' => 3
            ],
            [
                'id'    => 6,
                'label'  => 'Veg Co2',
                'status' => 2,
                'value' => 500,
                'target_type' => 1,
                'target_id' => 2,
                'data_type' => 4
            ],
            [
                'id'    => 7,
                'label'  => 'Bloom Humidity',
                'status' => 2,
                'value' => 50,
                'target_type' => 1,
                'target_id' => 3,
                'data_type' => 2
            ],
            [
                'id'    => 8,
                'label'  => 'Bloom Air Temperature',
                'status' => 2,
                'value' => 22,
                'target_type' => 1,
                'target_id' => 3,
                'data_type' => 3
            ],
            [
                'id'    => 9,
                'label'  => 'Bloom Co2',
                'status' => 2,
                'value' => 1000,
                'target_type' => 1,
                'target_id' => 3,
                'data_type' => 4
            ],
            [
                'id'    => 10,
                'label'  => 'Dry Humidity',
                'status' => 2,
                'value' => 10,
                'target_type' => 1,
                'target_id' => 4,
                'data_type' => 2
            ],
            [
                'id'    => 11,
                'label'  => 'Dry Air Temperature',
                'status' => 2,
                'value' => 20,
                'target_type' => 1,
                'target_id' => 4,
                'data_type' => 3
            ],
            [
                'id'    => 12,
                'label'  => 'Dry Co2',
                'status' => 2,
                'value' => 0,
                'target_type' => 1,
                'target_id' => 4,
                'data_type' => 4
            ],
            [
                'id'    => 13,
                'label'  => 'Cure Humidity',
                'status' => 2,
                'value' => 10,
                'target_type' => 1,
                'target_id' => 5,
                'data_type' => 2
            ],
            [
                'id'    => 14,
                'label'  => 'Cure Air Temeprature',
                'status' => 2,
                'value' => 22,
                'target_type' => 1,
                'target_id' => 5,
                'data_type' => 3
            ],
            [
                'id'    => 15,
                'label'  => 'Cure Co2',
                'status' => 2,
                'value' => 0,
                'target_type' => 1,
                'target_id' => 5,
                'data_type' => 4
            ],
            [
                'id'    => 16,
                'label'  => 'Processing Humidity',
                'status' => 2,
                'value' => 20,
                'target_type' => 1,
                'target_id' => 6,
                'data_type' => 2
            ],
            [
                'id'    => 17,
                'label'  => 'Processing Air Temperature',
                'status' => 2,
                'value' => 22,
                'target_type' => 1,
                'target_id' => 6,
                'data_type' => 3
            ],
            [
                'id'    => 18,
                'label'  => 'Processing Co2',
                'status' => 2,
                'value' => 0,
                'target_type' => 1,
                'target_id' => 6,
                'data_type' => 4
            ],
            [
                'id'    => 19,
                'label'  => 'Storage Humidity',
                'status' => 2,
                'value' => 20,
                'target_type' => 1,
                'target_id' => 7,
                'data_type' => 2
            ],
            [
                'id'    => 20,
                'label'  => 'Storage Air Temperature',
                'status' => 2,
                'value' => 22,
                'target_type' => 1,
                'target_id' => 7,
                'data_type' => 3
            ],
            [
                'id'    => 21,
                'label'  => 'Storage Co2',
                'status' => 2,
                'value' => 0,
                'target_type' => 1,
                'target_id' => 7,
                'data_type' => 4
            ],
            [
                'id'    => 22,
                'label'  => 'Shipping Humidity',
                'status' => 2,
                'value' => 20,
                'target_type' => 1,
                'target_id' => 8,
                'data_type' => 2
            ],
            [
                'id'    => 23,
                'label'  => 'Shipping Air Temperature',
                'status' => 2,
                'value' => 22,
                'target_type' => 1,
                'target_id' => 8,
                'data_type' => 3
            ],
            [
                'id'    => 24,
                'label'  => 'Shipping Co2',
                'status' => 2,
                'value' => 0,
                'target_type' => 1,
                'target_id' => 8,
                'data_type' => 4
            ]
        ];
        $this->insert('set_points', $rows);
    }
}
