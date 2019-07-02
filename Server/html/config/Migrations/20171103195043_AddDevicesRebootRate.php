<?php
use Migrations\AbstractMigration;

class AddDevicesRebootRate extends AbstractMigration
{

    public function up()
    {

        $this->table('devices')
            ->addColumn('reboot_rate', 'integer', [
                'after' => 'api_id',
                'default' => '1',
                'length' => 11,
                'null' => true,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('devices')
            ->removeColumn('reboot_rate')
            ->update();
    }
}

