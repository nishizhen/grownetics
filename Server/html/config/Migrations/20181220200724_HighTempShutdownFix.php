<?php
use Migrations\AbstractMigration;

class HighTempShutdownFix extends AbstractMigration
{

    public function up()
    {

        $this->table('outputs')
            ->addColumn('pre_high_temp_shutdown_status', 'integer', [
                'after' => 'deleted',
                'default' => null,
                'length' => 4,
                'null' => true,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('outputs')
            ->removeColumn('pre_high_temp_shutdown_status')
            ->update();
    }
}

