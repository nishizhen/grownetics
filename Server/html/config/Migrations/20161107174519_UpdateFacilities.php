<?php
use Migrations\AbstractMigration;

class UpdateFacilities extends AbstractMigration
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
        $table = $this->table('facilities');
        if ($table->hasColumn('bounding_box')) {
            $table->removeColumn('bounding_box');
        }
        if ($table->hasColumn('geoJSON')) {
            $table->removeColumn('geoJSON');
        }
        $table->update();
    }
}
