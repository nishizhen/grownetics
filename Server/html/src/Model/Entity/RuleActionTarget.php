<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * RuleActionTarget Entity
 *
 * @property int $id
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $rule_action_id
 * @property int $target_type
 * @property int $target_id
 * @property int $status
 * @property int $appliance_type_id
 *
 * @property \App\Model\Entity\RuleAction $rule_action
 * @property \App\Model\Entity\Target $target
 * @property \App\Model\Entity\ApplianceTemplate $appliance_type
 * @property bool $is_default
 * @property \Cake\I18n\FrozenTime $deleted
 */
class RuleActionTarget extends Entity
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
