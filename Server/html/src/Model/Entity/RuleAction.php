<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;

/**
 * RuleAction Entity
 *
 * @property int $id
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $type
 * @property int $notification_level
 * @property int $notification_user_role
 * @property string $output_on_value
 * @property string $output_off_value
 * @property int $rule_id
 * @property bool $on_trigger
 * @property bool $is_default
 * @property \Cake\I18n\FrozenTime $deleted
 * @property \App\Model\Entity\Rule $rule
 */
class RuleAction extends Entity
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

    # Return the rule action type that happens when auto resetting.
    protected function _getResetType() {
        $this->RuleActions = TableRegistry::get('RuleActions');

        if ($this->type == $this->RuleActions->enumValueToKey('type','Turn On')) {
            return $this->RuleActions->enumValueToKey('type','Turn Off');
        } else {
            return $this->RuleActions->enumValueToKey('type','Turn On');
        }
    }
}
