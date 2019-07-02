<?php
namespace App\View\Cell;

use Cake\View\Cell;

/**
 * Notifications cell
 */
class NotificationsCell extends Cell
{

    /**
     * List of valid options that can be passed into this
     * cell's constructor.
     *
     * @var array
     */
    protected $_validCellOptions = [];

    /**
     * Default display method.
     *
     * @return void
     */
    public function popout()
    {
        $this->loadModel('Notifications');
        $params = [
            'order' => ['Notifications.created' => 'desc'],
            'limit' => 5,
            'conditions' => [
                'notification_level >' => $this->Notifications->enumValueToKey('notification_level','Logged Only')
            ],
            'fields' => ['Notifications.created','Notifications.message', 'Notifications.id', 'Users.email', 'Users.name'],
            'contain' => ['Users']
        ];
        $notifications = $this->Notifications->find('all',$params);
        $notifications = $notifications->toArray(); 
        $this->set('notifications', $notifications);
    }

    public function box() 
    {
        $this->loadModel('Notifications');
        $params = [
            'order' => ['created' => 'desc'],
            'limit' => 50,
            'conditions' => [
                'notification_level >' => 0,
                'message !=' => ''
            ],
            'fields' => ['created','message']
        ];
        $notifications = $this->Notifications->find('all',$params);
        $notifications = $notifications->toArray();
        $this->set('notifications', $notifications);
    }
}
