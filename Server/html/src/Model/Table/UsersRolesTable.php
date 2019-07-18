<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class UsersRolesTable extends Table
{
    public function initialize(array $config)
    {
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'inner'
        ]);
        $this->belongsTo('Roles', [
            'foreignKey' => 'role_id',
            'joinType' => 'inner'
        ]);
    }
}