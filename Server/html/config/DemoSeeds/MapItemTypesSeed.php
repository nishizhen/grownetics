<?php
use Migrations\AbstractSeed;

/**
 * MapItemTypes seed.
 */
class MapItemTypesSeed extends AbstractSeed
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
                'color' => '',
                'opacity' => '1',
                'style' => '',
                'label' => 'Zone',
                'created' => '2018-02-23 22:50:07',
                'modified' => '2018-02-23 22:50:07',
                'deleted' => '0000-00-00 00:00:00',
            ],
            [
                'id' => '2',
                'color' => '',
                'opacity' => '1',
                'style' => '',
                'label' => 'Device',
                'created' => '2018-02-23 22:50:13',
                'modified' => '2018-02-23 22:50:13',
                'deleted' => '0000-00-00 00:00:00',
            ],
            [
                'id' => '3',
                'color' => '',
                'opacity' => '1',
                'style' => '',
                'label' => 'Map Item',
                'created' => '2018-02-23 22:50:13',
                'modified' => '2018-02-23 22:50:13',
                'deleted' => '0000-00-00 00:00:00',
            ],
            [
                'id' => '4',
                'color' => '',
                'opacity' => '1',
                'style' => '',
                'label' => 'Sensor',
                'created' => '2018-02-23 22:50:13',
                'modified' => '2018-02-23 22:50:13',
                'deleted' => '0000-00-00 00:00:00',
            ],
            [
                'id' => '5',
                'color' => '',
                'opacity' => '1',
                'style' => '',
                'label' => 'Plant Placeholder',
                'created' => '2018-02-23 22:50:37',
                'modified' => '2018-02-23 22:50:37',
                'deleted' => '0000-00-00 00:00:00',
            ],
            [
                'id' => '6',
                'color' => '',
                'opacity' => '1',
                'style' => '',
                'label' => 'Server_Switches',
                'created' => '2018-02-23 22:51:25',
                'modified' => '2018-02-23 22:51:25',
                'deleted' => '0000-00-00 00:00:00',
            ],
            [
                'id' => '7',
                'color' => '',
                'opacity' => '1',
                'style' => '',
                'label' => 'Power_Panel',
                'created' => '2018-02-23 22:51:25',
                'modified' => '2018-02-23 22:51:25',
                'deleted' => '0000-00-00 00:00:00',
            ],
        ];

        $table = $this->table('map_item_types');
        $table->insert($data)->save();
    }
}
