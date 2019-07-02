<?php
use Migrations\AbstractMigration;

class CreatePlantZoneTypesTable extends AbstractMigration
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
        $table = $this->table('plant_zone_types');
        $table->addColumn('label', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ]);
        $table->create();
        $rows = [
            [
                'id'    => 1,
                'label'  => 'Clone'
            ],
            [
                'id'    => 2,
                'label'  => 'Veg'
            ],
            [
                'id'    => 3,
                'label'  => 'Bloom'
            ],
            [
                'id'    => 4,
                'label'  => 'Dry'
            ],
            [
                'id'    => 5,
                'label'  => 'Cure'
            ],
            [
                'id'    => 6,
                'label'  => 'Processing'
            ],
            [
                'id'    => 7,
                'label'  => 'Storage'
            ],
            [
                'id'    => 8,
                'label'  => 'Shipping'
            ]
        ];
        $this->insert('plant_zone_types', $rows);
        $table->update();
    }
}
