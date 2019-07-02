<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Rule Entity
 *
 * @property int $id
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property string $label

 * @property int $status
 * @property int $type
 * @property bool $autoreset
 * @property \Cake\I18n\FrozenTime $deleted
 * @property \Cake\I18n\Time $deleted_date
 *
 * @property \App\Model\Entity\Data $data
 * @property \App\Model\Entity\Output[] $outputs
 * @property \App\Model\Entity\ParentRule $parent_rule
 * @property \App\Model\Entity\Notification[] $notifications
 * @property bool $is_default
 */
class Rule extends Entity
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
