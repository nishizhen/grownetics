<?php
use Migrations\AbstractMigration;

class UpdateFloorplanLatLons extends AbstractMigration
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
        $table = $this->table('floorplans');
        $table->changeColumn('latitude', 'decimal', [
            'default' => null,
            'null' => false,
            'precision' => 19,
            'scale' => 16
        ]);
        $table->changeColumn('longitude', 'decimal', [
            'default' => null,
            'null' => false,
            'precision' => 19,
            'scale' => 16
        ]);

    }
}
