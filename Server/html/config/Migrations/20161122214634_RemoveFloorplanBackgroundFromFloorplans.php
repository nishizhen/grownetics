<?php
use Migrations\AbstractMigration;

class RemoveFloorplanBackgroundFromFloorplans extends AbstractMigration
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
        if ($table->hasColumn('floorplan_background')) {
            $table->removeColumn('floorplan_background');
        }
        $table->update();
    }
}
