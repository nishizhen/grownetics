<?php
use Migrations\AbstractMigration;

class RemoveStrainIdFromRecipes extends AbstractMigration
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
        $table = $this->table('recipes');
        if ($table->hasColumn('strain_id')) {
            $table->removeColumn('strain_id');
        }
        $table->update();
    }
}
