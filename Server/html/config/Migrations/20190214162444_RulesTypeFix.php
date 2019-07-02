<?php
use Migrations\AbstractMigration;

class RulesTypeFix extends AbstractMigration
{

    public function up()
    {

        $this->table('rules')
            ->changeColumn('type', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('rules')
            ->changeColumn('type', 'boolean', [
                'default' => null,
                'length' => null,
                'null' => true,
            ])
            ->update();
    }
}

