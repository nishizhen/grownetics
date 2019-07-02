<?php
use Migrations\AbstractMigration;

class AlterZoneTypesAgain extends AbstractMigration
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

        $this->dropTable('zone_types');

        $this->table('zone_types')
            ->addColumn('label', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->create();

        $table = $this->table('zone_types');

        $rows = [
            [
                'id' =>  1,
                'label' => 'Room'
            ],
            [
                'id'    => 2,
                'label'  => 'Clone'
            ],
            [
                'id'    => 3,
                'label'  => 'Veg'
            ],
            [
                'id'    => 4,
                'label'  => 'Bloom'
            ],
            [
                'id' => 5,
                'label' => 'Dry'
            ],
            [
                'id'    => 6,
                'label'  => 'Cure'
            ],
            [
                'id'    => 7,
                'label'  => 'Processing'
            ],
            [
                'id'    => 8,
                'label'  => 'Storage'
            ],
            [
                'id'    => 9,
                'label'  => 'Shipping'
            ],
            [
                'id'    => 10,
                'label'  => 'HVAC'
            ],
            [
                'id'    => 11,
                'label'  => 'Custom'
            ],
            [
                'id' => 12,
                'label' => 'Plant Zone'
            ]
        ];

        $this->insert('zone_types', $rows);
        $table->update();
    }
}
