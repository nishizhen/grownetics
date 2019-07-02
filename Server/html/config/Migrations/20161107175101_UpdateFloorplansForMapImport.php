<?php
use Migrations\AbstractMigration;

class UpdateFloorplansForMapImport extends AbstractMigration
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
        if ($table->hasColumn('square_footage')) {
            $table->removeColumn('square_footage');
        }
        if ($table->hasColumn('offsetRotation')) {
            $table->renameColumn('offsetRotation', 'offsetAngle');
        }
        $table->update();
    }
}
