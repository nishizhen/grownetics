<?php
use Migrations\AbstractMigration;

class AddRuleActionOutputObjectOutputProperty extends AbstractMigration
{

    public function up()
    {

        $this->table('rule_action_targets')
            ->addColumn('output_object', 'string', [
                'default' => null,
                'length' => 45,
                'null' => true,
            ])
            ->addColumn('output_property', 'string', [
                'after' => 'output_object',
                'default' => null,
                'length' => 45,
                'null' => true,
            ])
            ->update();
    }

    public function down()
    {

    }
}

