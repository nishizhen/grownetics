<?php
namespace App\Model\Behavior;

use App\Lib\SystemEventRecorder;

use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\ORM\Entity;
use Cake\ORM\Query;
use Cake\Utility\Inflector;
use Cake\ORM\TableRegistry;
use Cake\Http\ServerRequestFactory;
use Cake\Network\Session;


class NotifierBehavior extends Behavior
{
    protected $_defaultConfig = [
        // Default to logged only.
        'notification_level' => 0
    ];

    public function notify(Entity $entity, String $action, String $object)
    {
        $this->session = new Session();
        $config = $this->config();
        $notificationLevel = $config['notification_level'];
        $notificationsTable = TableRegistry::get('Notifications');

        $message = 'User '.$this->session->read('Auth.User.id').' - '.
            $this->session->read('Auth.User.name').' '.$action.' '.
            strtolower(Inflector::singularize($object)).' id: '.$entity->id;

        $notification = $notificationsTable->newEntity([
            'status' => 0,
            'message' => $message,
            'user_id' => ($this->session->read('Auth.User.id') ?: 0),
            'source_type' => $entity->notifier_source_type,
            'source_id' => $entity->id,
            'notification_level' => $notificationLevel
        ]);
        $notificationsTable->save($notification);
    }

    public function afterSave(Event $event, EntityInterface $entity)
    {
        $this->session = new Session();
        $recorder = new SystemEventRecorder();
        if ($entity->dontNotify == false) {
            if ($entity->isNew()) {
                $action = 'created';
                $recorder->recordEvent('user_actions','record_created',$this->session->read('Auth.User.id'),['type' => $event->subject()->alias(),'email' => $this->session->read('Auth.User.email')]);
            } else {
                $action = 'edited';
                $recorder->recordEvent('user_actions','record_edited',$this->session->read('Auth.User.id'),['type' => $event->subject()->alias(),'email' => $this->session->read('Auth.User.email')]);
            }

            $this->notify($entity, $action, $event->subject()->alias());
        }
    }

    public function afterDelete(Event $event, EntityInterface $entity)
    {
        $this->notify($entity,'deleted',$event->subject()->alias());
        $recorder = new SystemEventRecorder();
        $recorder->recordEvent('user_actions','record_deleted',$this->session->read('Auth.User.id'),['type' => $event->subject()->alias(),'email' => $this->session->read('Auth.User.email')]);
    }
}
