<?php
use Migrations\AbstractMigration;

class AddHardwareTypeToOutputs extends AbstractMigration
{

    public function up()
    {

        $this->table('outputs')
            ->addColumn('hardware_type', 'integer', [
                'after' => 'output_type',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('outputs')
            ->removeColumn('hardware_type')
            ->update();
    }
}
