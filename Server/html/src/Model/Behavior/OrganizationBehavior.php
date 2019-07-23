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
        # Only perform Organization separation if we are not Onsite
        if (!env('ONSITE') && !$entity->owner_id) {
            $session = new Session();

            # Only auto-add owner information for logged in users
            if (isset($options['_footprint'])) {
                if ($session->read('Auth.User.current_organization_id')) {
                    $entity->owner_type = $this->_TYPES["Organization"];
                    $entity->owner_id = $session->read('Auth.User.current_organization_id');
                } else if ($options['_footprint']['id']) {
                    $entity->owner_type = $this->_TYPES["User"];
                    $entity->owner_id = $options['_footprint']['id'];
                }
            }
        }
    }

    public function beforeFind($event, $query, $options, $primary)
    {
        # Only perform Organization separation if we are not Onsite
        if (!env('ONSITE')) {
            $session = new Session();

            # Only auto-add owner information for logged in users
            if ($session->read('Auth.User.id')) {
                if ($session->read('Auth.User.current_organization_id')) {
                    $query->where([
                        'owner_type' => $this->_TYPES["Organization"],
                        'owner_id' => $session->read('Auth.User.current_organization_id')
                    ]);
                } else {
                    $query->where([
                        'owner_type' => $this->_TYPES["User"],
                        'owner_id' => $session->read('Auth.User.id')
                    ]);
                }
            }
        }
    }
}
