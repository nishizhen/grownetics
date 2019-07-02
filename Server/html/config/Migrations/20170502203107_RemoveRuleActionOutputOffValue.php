<?php
use Migrations\AbstractMigration;

class RemoveRuleActionOutputOffValue extends AbstractMigration
{

    public function up()
    {

        $this->table('rule_actions')
            ->removeColumn('output_on_value')
            ->removeColumn('output_off_value')
            ->update();

        $this->table('rule_action_targets')
            ->addColumn('output_value', 'string', [
                'default' => null,
                'length' => 255,
                'null' => true,
            ])
            ->update();
    }

    public function down()
    {


    }
}

