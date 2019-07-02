<?php
use Migrations\AbstractMigration;

class AlterZoneTypes extends AbstractMigration
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
                'label'  => 'Cure'
            ],
            [
                'id'    => 5,
                'label'  => 'Processing'
            ],
            [
                'id'    => 6,
                'label'  => 'Storage'
            ],
            [
                'id'    => 7,
                'label'  => 'Shipping'
            ],
            [
                'id'    => 8,
                'label'  => 'HVAC'
            ],
            [
                'id'    => 9,
                'label'  => 'Custom'
            ]
        ];

        $this->insert('zone_types', $rows);
    }
}
