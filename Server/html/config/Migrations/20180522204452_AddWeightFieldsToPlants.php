<?php
use Migrations\AbstractMigration;

class AddWeightFieldsToPlants extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function up()
    {
        $this->table('plants')
            ->addColumn('wet_whole_weight', 'decimal', [
                'default' => 0,
                'precision' => 12,
                'scale' => 8,
                'null' => true
            ])
            ->addColumn('wet_waste_weight', 'decimal', [
                'default' => 0,
                'precision' => 12,
                'scale' => 8,
                'null' => true
            ])
            ->addColumn('wet_whole_defoliated_weight', 'decimal', [
                'default' => 0,
                'precision' => 12,
                'scale' => 8,
                'null' => true
            ])
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
            ])
            ->update();
    }

    public function down()
    {

        $this->table('Plants')
            ->removeColumn('wet_whole_weight')
            ->removeColumn('wet_waste_weight')
            ->removeColumn('wet_whole_defoliated_weight')
            ->removeColumn('dry_whole_weight')
            ->removeColumn('dry_waste_weight')
            ->removeColumn('dry_whole_trimmed_weight')
            ->update();
    }
}
