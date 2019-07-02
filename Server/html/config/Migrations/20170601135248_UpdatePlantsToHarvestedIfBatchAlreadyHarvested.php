<?php
use Migrations\AbstractMigration;

class UpdatePlantsToHarvestedIfBatchAlreadyHarvested extends AbstractMigration
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
        $plants = $this->fetchAll('SELECT * FROM plants');
        foreach ($plants as $plant) {
            $batch = $this->fetchRow("SELECT * FROM harvest_batches WHERE id = ".$plant['harvest_batch_id']);
            if ($batch['status'] == 2) {
                $this->execute("UPDATE plants SET status=2,map_item_id=0,zone_id=0 WHERE id = ".$plant['id']);
            }
        }
    }
}
