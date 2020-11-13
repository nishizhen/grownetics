<?php
use Migrations\AbstractMigration;

class ApiKeyToVarChar extends AbstractMigration
{

    public function up()
    {

        $this->table('devices')
            ->changeColumn('api_id', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('devices')
            ->changeColumn('api_id', 'integer', [
                'default' => null,
                'length' => 11,
                'null' => false,
            ])
            ->update();
    }
}

