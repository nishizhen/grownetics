<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;

/**
 * RuleCondition Entity
 *
 * @property int $id
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property string $label
 * @property int $data_source
 * @property int $data_type
 * @property int $data_id
 * @property string $operator
 * @property int $trigger_threshold
 * @property int $reset_threshold
 * @property int $status
 * @property int $zone_behavior
 * @property int $autoreset
 * @property int $trigger_delay
 * @property int $pending_time
 * @property \Cake\I18n\FrozenTime $deleted
 * @property \Cake\I18n\Time $deleted_date
 *
 * @property \App\Model\Entity\Data $data
 * @property int $rule_id
 * @property bool $is_default
 */
class RuleCondition extends Entity
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

    # Get the object this condition is pointing to as it's source.
    protected function _getSourceTarget() {
        $this->RuleConditions = TableRegistry::get('RuleConditions');
        $this->Sensors = TableRegistry::get('Sensors');
        $this->Zones = TableRegistry::get('Zones');

        $source = false;
        switch ($this->data_source) {
            case $this->RuleConditions->enumValueToKey('data_source','Sensor'):
                $source = $this->Sensors->get($this->data_id);
            break;
            case $this->RuleConditions->enumValueToKey('data_source','Zone'):
                $source = $this->Zones->get($this->data_id);
            break;
        }

        return $source;
    }

    # Get the inverse operator, or the operator that needs to be true for autoreset to work
    # So if <, return >, and vice versa.
    protected function _getResetOperator() {
        if ($this->operator === 0) {
            return "1";
        } else {
            return "0";
        }
    }
}
