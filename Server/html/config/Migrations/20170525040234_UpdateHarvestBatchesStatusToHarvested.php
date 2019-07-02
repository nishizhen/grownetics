<?php
use Migrations\AbstractMigration;
use Cake\Chronos\Chronos;

class UpdateHarvestBatchesStatusToHarvested extends AbstractMigration
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
        $tasks = $this->fetchAll('SELECT * FROM tasks');
        foreach ($tasks as $task) {
            if ($task['type'] == 0 && $task['status'] == 1) {
                $this->execute("UPDATE harvest_batches SET status = 1 WHERE id = ".$task['harvestbatch_id']);
            } else if ($task['type'] == 2 && $task['status'] == 1) {
                $this->execute("UPDATE harvest_batches SET status = 2 WHERE id = ".$task['harvestbatch_id']);
            }
        }
    }
}
