<?php
use Migrations\AbstractMigration;

class AddCultivarIdToPlants extends AbstractMigration
{

    public function up()
    {

        $this->table('plants')
            ->addColumn('cultivar_id', 'integer', [
                'after' => 'wet_whole_defoliated_weight',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('plants')
            ->removeColumn('cultivar_id')
            ->update();
    }
}

