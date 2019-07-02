<?php
use Migrations\AbstractSeed;

/**
 * Notifications seed.
 */
class NotificationsSeed extends AbstractSeed
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
                'created' => '2017-07-28 18:20:55',
                'modified' => '2017-07-28 18:20:57',
                'status' => '1',
                'notification_level' => '0',
                'source_type' => NULL,
                'source_id' => '1',
                'message' => 'User 2 - Admin created floorplan id: 1. IP: 172.17.0.1',
                'rule_id' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'template' => '',
                'user_id' => '2',
            ],
            [
                'created' => '2017-08-02 17:04:43',
                'modified' => '2017-08-02 17:04:44',
                'status' => '1',
                'notification_level' => '0',
                'source_type' => NULL,
                'source_id' => '2',
                'message' => 'User 2 - Admin edited user id: 2. IP: 172.17.0.1',
                'rule_id' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'template' => '',
                'user_id' => '2',
            ],
            [
                'created' => '2017-08-02 17:04:44',
                'modified' => '2017-08-02 17:04:47',
                'status' => '1',
                'notification_level' => '0',
                'source_type' => NULL,
                'source_id' => '2',
                'message' => 'User 2 - Admin edited user id: 2. IP: 172.17.0.1',
                'rule_id' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'template' => '',
                'user_id' => '2',
            ],
            [
                'created' => '2017-08-02 17:05:12',
                'modified' => '2017-08-02 17:05:14',
                'status' => '1',
                'notification_level' => '0',
                'source_type' => NULL,
                'source_id' => '2',
                'message' => 'User 2 - Admin edited user id: 2. IP: 172.17.0.1',
                'rule_id' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'template' => '',
                'user_id' => '2',
            ],
            [
                'created' => '2017-08-02 17:05:46',
                'modified' => '2017-08-02 17:05:47',
                'status' => '1',
                'notification_level' => '0',
                'source_type' => NULL,
                'source_id' => '2',
                'message' => 'User 2 - Admin edited user id: 2. IP: 172.17.0.1',
                'rule_id' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'template' => '',
                'user_id' => '2',
            ],
        ];

        $table = $this->table('notifications');
        $table->insert($data)->save();
    }
}
