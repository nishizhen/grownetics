<?php
use Migrations\AbstractMigration;

class AddBacnetCo2ToZones extends AbstractMigration
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
        $table = $this->table('zones')
            ->addColumn('bacnet_co2_read', 'integer', [
                'after' => 'bacnet_temp_set',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->addColumn('bacnet_co2_set', 'integer', [
                'after' => 'bacnet_co2_read',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->update();
    }
}
