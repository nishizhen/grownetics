<?php
namespace App\Model\Entity;

use Cake\Auth\DefaultPasswordHasher;
use Cake\ORM\Entity;

/**
 * @property int $id
 * @property string $username
 * @property string $password
 * @property string $email
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property string $name
 * @property string $company
 * @property string $address
 * @property string $address_2
 * @property string $city
 * @property string $state
 * @property string $country
 * @property string $zip
 * @property string $access_code
 * @property string $email_token
 * @property string $dashboard_config
 * @property \Cake\I18n\FrozenTime $deleted
 * @property int $role_id
 * @property bool $show_metric
 */
class User extends Entity
{

    // Make all fields mass assignable except for primary key field "id".
    protected $_accessible = [
        '*' => true,
        'id' => false,        
    ];

    protected function _setPassword($password)
    {
        return (new DefaultPasswordHasher)->hash($password);
    }
    
}