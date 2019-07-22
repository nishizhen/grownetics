<?php
use Migrations\AbstractSeed;

/**
 * Users seed.
 */
class UsersRolesSeed extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     *
     * @return void
     */
    public function run()
    {

        $table = $this->table('users_roles');
        $rows = $this->fetchAll('SELECT * FROM users_roles');
        $data = [
          [
            'user_id' => 2,
            'role_id' => 1
          ]
        ];
        if ($rows == null) {
            $table->insert($data)->save();
        }
    }
}
