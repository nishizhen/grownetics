<?php
use Migrations\AbstractMigration;

class ConvertRuleConditionThresholdsToFloats extends AbstractMigration
{

    public function up()
    {

        $this->table('rule_conditions')
            ->changeColumn('trigger_threshold', 'float', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->changeColumn('reset_threshold', 'float', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('rule_conditions')
            ->changeColumn('trigger_threshold', 'integer', [
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->changeColumn('reset_threshold', 'integer', [
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->update();
    }
}

