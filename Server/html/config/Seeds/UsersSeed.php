<?php
use Migrations\AbstractSeed;

/**
 * Users seed.
 */
class UsersSeed extends AbstractSeed
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

        $table = $this->table('users');
        $rows = $this->fetchAll('SELECT * FROM users');
        $data = [
          [
              'email'       => 'hello@grownetics.co',
              'password'    => '$2y$10$2SfSC4i4BhThf.m/QeNsCekrqeE47G7i3aZBdzPjw1m/yf6y5/2V6', #GrowBetter
              'role_id'        => 4,
              'name'        => 'Demo User'
          ],
          [
              'email'       => 'admin@grownetics.co',
              'password'    => '$2y$10$2SfSC4i4BhThf.m/QeNsCekrqeE47G7i3aZBdzPjw1m/yf6y5/2V6',
              'role_id'        => 1,
              'name'        => 'Admin'
          ],
          [
              'email'       => 'owner@grownetics.co',
              'password'    => '$2y$10$2SfSC4i4BhThf.m/QeNsCekrqeE47G7i3aZBdzPjw1m/yf6y5/2V6',
              'role_id'        => 2,
              'name'        => 'Owner'
          ],
          [
              'email'       => 'grower@grownetics.co',
              'password'    => '$2y$10$2SfSC4i4BhThf.m/QeNsCekrqeE47G7i3aZBdzPjw1m/yf6y5/2V6',
              'role_id'        => 3,
              'name'        => 'Grower'
          ]
        ];
        if ($rows == null) {
            $table->insert($data)->save();
        }
    }
}
