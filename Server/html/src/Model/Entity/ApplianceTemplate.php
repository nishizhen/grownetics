<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ApplianceTemplate Entity
 *
 * @property int $id
 * @property string $label
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\RuleActionTarget[] $rule_action_targets
 * @property int $appliance_type_id
 * @property int $map_item_type_id
 * @property \Cake\I18n\FrozenTime $deleted
 */
class ApplianceTemplate extends Entity
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
