<?php
use Migrations\AbstractSeed;

/**
 * Roles seed.
 */
class RolesSeed extends AbstractSeed
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
        $this->execute('TRUNCATE roles');
        $data = [
            [
                'id' => 1,
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'Admin',
            ],
            [
                'id' => 2,
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'Owner',
            ],
            [
                'id' => 3,
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'Grower',
            ],
            [
                'id' => 4,
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'User',
            ],
            [
                'id' => 5,
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'Invitee',
            ],
            [
                'id' => 6,
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'Guest',
            ],
            [
                'id' => 7,
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'Organization Admin',
            ],
            [
                'id' => 8,
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'Organization Member',
            ],
            [
                'id' => 9,
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'Organization Invitee',
            ],            
        ];

        $table = $this->table('roles');
        $table->insert($data)->save();
    }
}
