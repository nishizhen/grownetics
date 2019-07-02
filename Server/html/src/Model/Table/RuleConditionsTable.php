<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use SoftDelete\Model\Table\SoftDeleteTrait;
use Cake\ORM\TableRegistry;
use App\Lib\SystemEventRecorder;
use Cake\Cache\Cache;

/**
 * RuleConditions Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Data
 *
 * @method \App\Model\Entity\RuleCondition get($primaryKey, $options = [])
 * @method \App\Model\Entity\RuleCondition newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\RuleCondition[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\RuleCondition|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\RuleCondition patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\RuleCondition[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\RuleCondition findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @property \App\Model\Table\RulesTable|\Cake\ORM\Association\BelongsTo $Rules
 * @mixin \App\Model\Behavior\EnumBehavior
 */
class RuleConditionsTable extends Table
{

    public $enums = [
        'status' => [
            'Disabled',
            'Enabled',
            'Triggered'
        ],
        'data_source' => [
            0 => 'Sensor',
            1 => 'Zone',
            // 2 - This space intentionally blank. Deprecated.
            3 => 'Time',
            4 => 'Interval',
            5 => 'Zone Type',
            6 => 'Zone Type Target'
        ],
        'zone_behavior' => [
            'Single Sensor',
            'Average Of Sensors'
        ],
        'operator' => [
            '>',
            '<'
        ],
        'averaging_method' => [
            'Average',
            'Median'
        ]

    ];

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('rule_conditions');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Enum');
        $this->addBehavior('Timestamp');

        $this->belongsTo('Rules', [
            'foreignKey' => 'rule_id'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->allowEmpty('label');

        $validator
            ->integer('data_source')
            ->allowEmpty('data_source');

        $validator
            ->integer('data_type')
            ->allowEmpty('data_type');

        $validator
            ->allowEmpty('operator');

        $validator
            ->integer('trigger_threshold')
            ->allowEmpty('trigger_threshold');

        $validator
            ->integer('reset_threshold')
            ->allowEmpty('reset_threshold');

        $validator
            ->integer('status')
            ->allowEmpty('status');

        $validator
            ->integer('zone_behavior')
            ->allowEmpty('zone_behavior');

        $validator
            ->integer('autoreset')
            ->allowEmpty('autoreset');

        $validator
            ->integer('trigger_delay')
            ->allowEmpty('trigger_delay');

        $validator
            ->integer('pending_time')
            ->allowEmpty('pending_time');

        $validator
            ->integer('deleted')
            ->allowEmpty('deleted');

        $validator
            ->dateTime('deleted_date')
            ->allowEmpty('deleted_date');
        
        $validator
            ->boolean('is_default')
            ->allowEmpty('is_default');

        return $validator;
    }

    public function processConditions($params, $zones = null)
    {
        $conditionIds = [];
        $DataPoints = TableRegistry::get('DataPoints');
        $Rules = TableRegistry::get('Rules');
        $this->RuleConditions = TableRegistry::get('RuleConditions');

        $conditions = $this->find('all', $params);
        foreach ($conditions as $condition) {
            $conditionIds[] = $condition->id;
            $dataPoints = $DataPoints->getDataPointsForCondition($condition);
            $dataPointValue = $DataPoints->getValueForRule($dataPoints, $condition);

            try {
                $rule = $Rules->get($condition->rule_id);
            } catch (\Exception $e) {
                continue;
            }

            if (!$dataPointValue) {
                continue;
            }
            if (
                (
                    $condition->status == $this->enumValueToKey('status', 'Enabled')
                    &&
                    (
                        (
                            $this->enumValueToKey('operator', '>') == $condition->operator
                            &&
                            (
                                $dataPointValue > $condition->trigger_threshold
                            )
                        )
                        ||
                        (
                            $this->enumValueToKey('operator', '<') == $condition->operator
                            &&
                            $dataPointValue < $condition->trigger_threshold
                        )
                    )
                )
                ||
                (
                    $condition->status == $this->enumValueToKey('status', 'Triggered')
                    &&
                    $rule->autoreset
                    &&
                    (
                        (
                            $this->enumValueToKey('operator', '>') == $condition->operator
                            &&
                            (
                                $dataPointValue < $condition->reset_threshold
                            )
                        )
                        ||
                        (
                            $this->enumValueToKey('operator', '<') == $condition->operator
                            &&
                            $dataPointValue > $condition->reset_threshold
                        )
                    )
                )
            ) {
                // The condition has been met. Now check to see if we need to delay triggering.
                $triggerRule = true;
                if ($condition->trigger_delay > 0) {
                    if ($condition->pending_time > 0) {
                        // We have an existing pending time, so the threshold has been crossed before. Check if it's time yet.
                        if (time() - $condition->pending_time < $condition->trigger_delay) {
                            // Not ready to trigger yet!
                            $triggerRule = false;
                        }
                    } else {
                        // Set the pending time for the next call.
                        $condition->pending_time = time();
                        $triggerRule = false;
                        $this->save($condition);
                    }
                }
                // If the condition is NOT met AND (it either has no trigger_delay OR the time for the trigger delay has passed)
                // OR
                // The condition is already met
                if (
                    (
                        $condition->status == $this->enumValueToKey('status', 'Enabled')
                        &&
                        (
                            $condition->trigger_delay < 1
                            ||
                            $triggerRule
                        )
                    )
                    ||
                    (
                        $condition->status == $this->enumValueToKey('status', 'Triggered')
                    )
                ) {
                    // Alright we either need to trigger the condition, or reset it.
                    if ($condition->status == $this->RuleConditions->enumValueToKey('status', 'Enabled')) {
                        $condition->status = $this->RuleConditions->enumValueToKey('status', 'Triggered');
                    } else if ($condition->status == $this->RuleConditions->enumValueToKey('status', 'Triggered')) {
                        $condition->status = $this->RuleConditions->enumValueToKey('status', 'Enabled');
                    }

                    $recorder = new SystemEventRecorder();
                    $recorder->recordEvent('system_events', 'rule_trigger', 1, [
                        'rule_id' => $rule->id,
                        'condition_id' => $condition->id,
                        'data_point_value' => $dataPointValue,
                        'condition_status' => $condition->status,
                    ]);

                    # Used by Notifications to be able to send notifications later using the correct value now
                    Cache::write('last-trigger-value-for-rule-condition-'.$condition->id, $dataPointValue);

                    // Reset pending time
                    $condition->pending_time = 0;
                    if (!$this->save($condition)) {
                        debug($this->validationErrors);
                    }
                }
            }
        }

        return $conditionIds;
    }
}
