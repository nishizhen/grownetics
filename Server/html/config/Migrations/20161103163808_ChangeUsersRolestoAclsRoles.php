<?php
use Migrations\AbstractMigration;

class ChangeUsersRolestoAclsRoles extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $table = $this->table('users');
        $table->removeColumn('role');
        $table->addColumn('role_id', 'integer', [
            'null' => false,
        ]);
        $table->update();

        $table = $this->table('acls');
        $table->removeColumn('role_id');
        $table->update();

        // Remove UsersRoles
        $table = $this->table('users_roles');
        $table->drop();

        // Add AclsRoles
        $table = $this->table('acls_roles');
        $table->addColumn('acl_id', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
        ]);
        $table->addColumn('role_id', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
        ]);
        $table->create();
    }
}
