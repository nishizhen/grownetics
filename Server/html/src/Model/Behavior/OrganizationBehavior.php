<?php

namespace App\Model\Behavior;

use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\Network\Session;


class OrganizationBehavior extends Behavior
{
    protected $_TYPES = [
        "User" => 0,
        "Organization" => 1,
    ];

    public function beforeSave($event, $entity, $options)
    {
        $session = new Session();

        # Only auto-add owner information for logged in users
        if ($session->read('Auth.User')) {
            if ($session->read('Config.organization_id')) {
                $entity->owner_type = $this->_TYPES["Organization"];
                $entity->owner_id = $session->read('Config.organization_id');
            } else if ($session->read('Auth.User.id')) {
                $entity->owner_type = $this->_TYPES["User"];
                $entity->owner_id = $session->read('Auth.User.id');
            }
        }
    }

    public function beforeFind($event, $query, $options, $primary)
    {
        $session = new Session();

        # Only auto-add owner information for logged in users
        if ($session->read('Auth.User')) {
            if ($session->read('Config.organization_id')) {
                $query->where([
                    'owner_type' => $this->_TYPES["Organization"],
                    'owner_id' => $session->read('Config.organization_id')
                ]);
            } else if ($session->read('Auth.User.id')) {
                $query->where([
                    'owner_type' => $this->_TYPES["User"],
                    'owner_id' => $session->read('Auth.User')->id
                ]);
            }
        }
    }
}
