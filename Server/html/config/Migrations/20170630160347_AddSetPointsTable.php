<?php
use Migrations\AbstractMigration;

class AddSetPointsTable extends AbstractMigration
{

    public function up()
    {

//        $this->table('rule_action_targets')
//            ->removeColumn('output_value')
//            ->removeColumn('output_object')
//            ->removeColumn('output_property')
//            ->update();

        $this->table('set_points')
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('deleted', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('label', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('status', 'integer', [
                'default' => 0,
                'limit' => 1,
            ])
            ->addColumn('value', 'string', [
                'default' => null,
                'limit' => 45,
                'null' => true,
            ])
            ->addColumn('target_type', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('target_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('data_type', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->create();
    }

    public function down()
    {

        $this->table('rule_action_targets')
            ->addColumn('output_value', 'string', [
                'after' => 'status',
                'default' => null,
                'length' => 255,
                'null' => true,
            ])
            ->addColumn('output_object', 'string', [
                'after' => 'output_value',
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

        $this->dropTable('set_points');
    }
}

