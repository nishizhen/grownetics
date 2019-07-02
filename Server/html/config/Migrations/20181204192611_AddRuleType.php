<?php
use Migrations\AbstractMigration;

class AddRuleType extends AbstractMigration
{

    public function up()
    {

        $this->table('rules')
            ->addColumn('type', 'boolean', [
                'after' => 'is_default',
                'default' => null,
                'length' => null,
                'null' => true,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('rules')
            ->removeColumn('type')
            ->update();
    }
}

