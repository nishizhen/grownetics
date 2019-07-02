<?php
use Migrations\AbstractMigration;

class AddLightLevelSetToZones extends AbstractMigration
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
        $table->addColumn('light_level_set', 'integer', [
            'after' => 'bacnet_timestamp',
            'default' => null,
            'length' => 11,
            'null' => true,
        ]);
        $table->update();
    }
}
