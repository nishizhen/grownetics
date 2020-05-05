<?php

namespace App\Shell;

use Cake\Console\Shell;
use Cake\Datasource\ConnectionManager;
use Cake\Validation\Validator;
use Cake\Mailer\Email;
use Cake\Core\Configure;
use App\Lib\SystemEventRecorder;
use App\Lib\SystemHealth;
use App\Lib\Controls\OverrideDetector;


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
class GrowpulseShell extends Shell
{
    public $clients = NULL;

    public function initialize()
    {
        $this->loadModel('Notifications');
        $this->loadModel('Devices');
        $this->loadModel('Zones');
        $this->loadModel('Rules');
        $this->loadModel('DataPoints');
    }

    public function main()
    {
        $time = time();
        $recorder = new SystemEventRecorder();
        $systemHealth = new SystemHealth();
        $detector = new OverrideDetector();

        while (true) {

            // Run every 5 seconds
            if (time() - $time > 4) {
                $recorder->recordEvent('system_events', 'growpulse_tick', 1, ['version'=>env('VERSION')]);

                # Set devices from Enabled to Rebooting
                $this->out('Update Statuses');
                $this->Devices->updateStatuses($this);

                # Check rules that are triggered based on time
                $this->out('Process Timed Rules');
                $this->Rules->processTimedRules($this);

                # Create Zone data points
                $this->out('Process Zone Datapoints');
                $this->Zones->processData();

                # Process Zone based Rules
                $this->out('Process Zone Rules');
                try {
                    $this->Zones->processRules();
                } catch (\Exception $exception) {
                    $this->out($exception);
                }

                # Store System Health Statuses
                $this->out('Store System Statuses');
                $systemHealth->storeStatuses();

                # Detect power overrides
                $detector->detect();

                $time = time();
                $this->out("Tick: " . $time);
            }
            sleep(1);
        }
    }
}
