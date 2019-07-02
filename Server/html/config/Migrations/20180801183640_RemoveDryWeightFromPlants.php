<?php
use Migrations\AbstractMigration;

class RemoveDryWeightFromPlants extends AbstractMigration
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
        $table = $this->table('plants')
            ->removeColumn('dry_whole_weight')
            ->removeColumn('dry_waste_weight')
            ->removeColumn('dry_whole_trimmed_weight');
        $table->update();

        $table = $this->table('harvest_batches')
            ->addColumn('dry_whole_weight', 'decimal', [
                'default' => 0,
                'precision' => 12,
                'scale' => 8,
                'null' => true
            ])
            ->addColumn('dry_waste_weight', 'decimal', [
                'default' => 0,
                'precision' => 12,
                'scale' => 8,
                'null' => true
            ])
            ->addColumn('dry_whole_trimmed_weight', 'decimal', [
                'default' => 0,
                'precision' => 12,
                'scale' => 8,
                'null' => true
            ]);
        $table->update();
    }
}
