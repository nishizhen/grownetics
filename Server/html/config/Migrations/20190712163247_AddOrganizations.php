<?php
use Migrations\AbstractMigration;

class AddOrganizations extends AbstractMigration
{

    public function up()
    {

        $this->table('organizations')
            ->addColumn('label', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
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
            ->addColumn('user_id', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->create();

        $this->table('users_roles')
            ->addColumn('organization_id', 'string', [
                'after' => 'role_id',
                'default' => null,
                'length' => 255,
                'null' => true,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('users_roles')
            ->removeColumn('organization_id')
            ->update();

        $this->table('organizations')->drop()->save();
    }
}

