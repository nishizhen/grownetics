<?php
use Migrations\AbstractMigration;

class RenameStrainToCultivar extends AbstractMigration
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
        // Rename table

        // Rename `strain_id` on harvest batch table
        $table = $this->table('strains');
        $table->rename('cultivars');
        $table->update();

        $table = $this->table('harvest_batches');
        $table->renameColumn('strain_id', 'cultivar_id');
        $table->update();
    }
}
