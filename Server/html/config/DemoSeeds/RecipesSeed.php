<?php
use Migrations\AbstractSeed;

/**
 * Recipes seed.
 */
class RecipesSeed extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeds is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'id' => '1',
                'label' => 'Girl Scout Cookie v1',
                'created' => '2018-02-23 17:56:33',
                'modified' => '2018-02-23 17:56:33',
                'deleted' => '0000-00-00 00:00:00',
            ],
            [
                'id' => '2',
                'label' => 'Generic Sativa Recipe',
                'created' => '2018-02-23 17:56:50',
                'modified' => '2018-02-23 17:56:50',
                'deleted' => '0000-00-00 00:00:00',
            ],
            [
                'id' => '3',
                'label' => 'Generic Indica Recipe',
                'created' => '2018-02-23 17:57:05',
                'modified' => '2018-02-23 17:57:05',
                'deleted' => '0000-00-00 00:00:00',
            ],
            [
                'id' => '4',
                'label' => 'Orange Kush v1',
                'created' => '2018-02-23 17:57:19',
                'modified' => '2018-02-23 17:57:19',
                'deleted' => '0000-00-00 00:00:00',
            ],
            [
                'id' => '5',
                'label' => 'Kosher v1',
                'created' => '2018-02-23 17:57:35',
                'modified' => '2018-02-23 17:57:35',
                'deleted' => '0000-00-00 00:00:00',
            ],
            [
                'id' => '6',
                'label' => 'ACDC v1 - 70 Day',
                'created' => '2018-02-23 17:58:01',
                'modified' => '2018-02-23 17:58:01',
                'deleted' => '0000-00-00 00:00:00',
            ],
            [
                'id' => '7',
                'label' => 'Generic Recipe - 70 Day',
                'created' => '2018-02-23 17:58:18',
                'modified' => '2018-02-23 17:58:18',
                'deleted' => '0000-00-00 00:00:00',
            ],
        ];

        $table = $this->table('recipes');
        $table->insert($data)->save();
    }
}
