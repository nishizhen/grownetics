<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * AclsRole Entity
 *
 * @property int $id
 * @property string $acl_id
 * @property string $role_id
 *
 * @property \App\Model\Entity\Acl $acl
 * @property \App\Model\Entity\Role $role
 * @property \Cake\I18n\FrozenTime $deleted
 */
class AclsRole extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false
    ];
}
