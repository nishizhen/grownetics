<?php

namespace App\Shell;

use Cake\Console\Shell;
use Cake\Datasource\ConnectionManager;
use Cake\Validation\Validator;
use Cake\Mailer\Email;
use Cake\Core\Configure;
use App\Lib\SystemEventRecorder;

/**
 * @property \App\Model\Table\NotificationsTable $Notifications
 * @property \App\Model\Table\SensorsTable $Sensors
 * @property \App\Model\Table\DevicesTable $Devices
 * @property \App\Model\Table\DataPointsTable $DataPoints
 * @property \App\Model\Table\RulesTable $Rules
 * @property \App\Model\Table\ChatsTable $Chats
 * @property \App\Model\Table\UsersTable $Users
 * @property \App\Model\Table\ZonesTable $Zones
 * @property \App\Model\Table\UserContactMethodsTable $UserContactMethods
 */
class NotificationShell extends Shell
{
    public $clients = NULL;

    public function initialize()
    {
        $this->loadModel('Notifications');
    }

    public function main()
    {
        $time = time() - 30;
        $this->out('Starting notification thread.');
        while (true) {

            // Send every 30 seconds
            if (time() - $time > 30) {

                $recorder = new SystemEventRecorder();
                $recorder->recordEvent('system_events', 'notifications_tick', 1, ['version'=>env('VERSION')]);

                # Send notifications that are in the queue
                $this->out('Send Notifications');
                $this->Notifications->process($this);

                $time = time();
                $this->out("Tick: " . $time);
            }
            sleep(1);
        }
    }
}
