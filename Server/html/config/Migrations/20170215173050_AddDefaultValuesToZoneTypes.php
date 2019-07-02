<?php
use Migrations\AbstractMigration;

class AddDefaultValuesToZoneTypes extends AbstractMigration
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
        $table = $this->table('zone_types');
        $rows = [
            [
                'id'    => 1,
                'zone_type'  => '1',
                'label' => 'Clone'
            ],
            [
                'id'    => 2,
                'zone_type'  => '2',
                'label' => 'Veg'
            ],
            [
                'id'    => 3,
                'zone_type'  => '3',
                'label' => 'Bloom'
            ],
            [
                'id'    => 4,
                'zone_type'  => '4',
                'label' => 'Other'
            ]
        ];

        $this->insert('zone_types', $rows);
        $table->update();
    }
}
