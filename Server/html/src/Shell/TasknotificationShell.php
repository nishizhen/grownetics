<?php
namespace App\Shell;

use Cake\Console\Shell;
use Cake\ORM\TableRegistry;

/**
 * TaskNotification shell command.
 */
class TasknotificationShell extends Shell
{
    public $clients = NULL;

    public function initialize()
    {
        $this->loadModel('Notifications');
        $this->loadModel('Users');
        $this->loadModel('Zones');
        $this->loadModel('UserContactMethods');
        $this->loadModel('Tasks');
        $this->loadModel('RuleActions');
    }

    /**
     * main() method.
     *
     * @return bool|int|null Success or error code.
     */
    public function main()
    {
        $this->Tasks = TableRegistry::get('Tasks');
        $this->Notifications = TableRegistry::get('Notifications');
        $this->Users = TableRegistry::get('Users');

        $currentDateTime = date('Y-m-d H:i:s', strtotime('today'));
        $newDateTime = date('h:i A', strtotime($currentDateTime));
        $contacts = [];

        $todays_task = $this->Tasks->find('all', ['conditions' => ['due_date' => $currentDateTime]])->toArray();

        if (substr($newDateTime, -2) == "AM") {
            // Get the tasks due today and alert assignees
            foreach ($todays_task as $task) {
                // GEt the type of task
                if ( array_key_exists($task->assignee, $contacts)) {
                    $contacts[$task->assignee][] .= $task->type;
                } else {
                    $contacts[$task->assignee] = [$task->type];
                }
            }

            foreach($contacts as $key => $value) {
                $notificationData = array(
                    'source_type' => $this->Notifications->enumValueToKey('source_type', 'HarvestBatch'),
                    'status' => $this->Notifications->enumValueToKey('status', 'Queued'),
                    'message' => is_array($value) ? 'Multiple tasks assigned to you, are due today. Please check the task list.' : 'A task of type '. $this->Tasks->enumKeyToValue('type', $value). ' is assigned to you and due today.',
                    'notification_level' => $this->RuleActions->enumValueToKey('notification_level', 'Dashboard Notification'),
                    'user_id' => $key
                );
                $notification = $this->Notifications->newEntity($notificationData);
                $this->Notifications->save($notification);
            }
        } else {
            // Verify all tasks due today were done
            $uncompleted = [];
            $grower = $this->Users->find('all', ['conditions' => ['role_id' => 3]])->first();
            foreach($todays_task as $task) {
                if ($task->completed_date == null) {
                    // Send notificiation to grower
                    $uncompleted[] = $task;
                }
            }
            if ( count($uncompleted) > 0 ) {
                $notificationData = array(
                    'source_type' => $this->Notifications->enumValueToKey('source_type', 'HarvestBatch'),
                    'status' => $this->Notifications->enumValueToKey('status', 'Queued'),
                    'message' => count($uncompleted) > 1 ? 'Multiple tasks that were due today were left un-completed.' : 'A task of type '. $this->Tasks->enumKeyToValue('type', $uncompleted[0]->type). ' was due today but was left un-completed.',
                    'notification_level' => $this->RuleActions->enumValueToKey('notification_level', 'Dashboard Notification'),
                    'user_id' => $grower->id
                );
                $notification = $this->Notifications->newEntity($notificationData);
                $this->Notifications->save($notification);
            }
        }
    }
}
