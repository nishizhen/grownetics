<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use SoftDelete\Model\Table\SoftDeleteTrait;
use Cake\ORM\TableRegistry;
use App\Lib\SystemEventRecorder;

/**
 * Outputs Model
 *
 * @property \App\Model\Table\DevicesTable|\Cake\ORM\Association\BelongsTo $Devices
 * @property \App\Model\Table\ZonesTable|\Cake\ORM\Association\BelongsTo $Zones
 * @property \App\Model\Table\RulesTable|\Cake\ORM\Association\HasMany $Rules
 *
 * @method \App\Model\Entity\Output get($primaryKey, $options = [])
 * @method \App\Model\Entity\Output newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Output[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Output|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Output patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Output[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Output findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @mixin \App\Model\Behavior\EnumBehavior
 * @mixin \App\Model\Behavior\NotifierBehavior
 */
class OutputsTable extends Table
{
    use SoftDeleteTrait;

    public $enums = array(
        'status' => array(
            'Disabled',
            'Off',
            'On',
            'Force On',
            'Force Off',
            'High Temp Shutdown'
        ),
        'output_type' => array(
            'Relay Output',
            'URL Output'
        ),
        'hardware_type' => array(
            'Generic',
            'Light',
            'Co2 Doser'
        )
    );

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('outputs');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Enum');
        $this->addBehavior('Notifier');
        $this->addBehavior('Organization');

        $this->belongsTo('Devices', [
            'foreignKey' => 'device_id'
        ]);
        $this->hasMany('Rules', [
            'foreignKey' => 'output_id'
        ]);
        $this->belongsTo('Zones', [
            'foreignKey' => 'zone_id',
            'strategy' => 'select'
        ]);
        $this->belongsTo('Sensors', [
            'foreignKey' => 'ct_sensor_id',
            'strategy' => 'select',
            'propertyName' => 'ct_sensor'
        ]);

        $this->hasMany('RuleActionTargets', [
            'foreignKey' => 'target_id',
            'conditions' => ['RuleActionTargets.target_type' => $this->enumValueToKey('target_type','Output')]
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
            ->integer('status')
            ->allowEmpty('status');

        $validator
            ->allowEmpty('label');

        $validator
            ->allowEmpty('output_target');

        $validator
            ->integer('output_type')
            ->allowEmpty('output_type');

        $validator
            ->integer('deleted')
            ->allowEmpty('deleted');

        $validator
            ->dateTime('deleted_date')
            ->allowEmpty('deleted_date');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['device_id'], 'Devices'));
        $rules->add($rules->existsIn(['zone_id'], 'Zones'));

        return $rules;
    }

    public function getOutputsAndTimedScheduleForDevice($deviceId) {
        $this->RuleActionTargets = TableRegistry::get('RuleActionTargets');
        $this->RuleActions = TableRegistry::get('RuleActions');
        $this->RuleConditions = TableRegistry::get('RuleConditions');

        // Check for outputs
        $light_outputs = $this->find('all', [
            'conditions'=> [
                'device_id' => $deviceId,
                'output_type' => $this->enumValueToKey('output_type','Relay Output'),
                'hardware_type' => $this->enumValueToKey('hardware_type', 'Light'),
                'status IN' => [
                    $this->enumValueToKey('status','On'),
                    $this->enumValueToKey('status','Force On')
                ]
            ],
            'fields' => ['output_target', 'id']
        ]);
        $generic_outputs = $this->find('all', [
            'conditions'=> [
                'device_id' => $deviceId,
                'output_type' => $this->enumValueToKey('output_type','Relay Output'),
                'hardware_type' => $this->enumValueToKey('hardware_type', 'Generic'),
                'status IN' => [
                    $this->enumValueToKey('status','On'),
                    $this->enumValueToKey('status','Force On')
                ]
            ],
            'fields' => ['output_target', 'id']
        ]);

        $timestamp = "\"current_time\":".strtotime('now').",";

        $outputsData = '';
        $lightOutputsData = '';
        foreach ($light_outputs as $output) {
            if (strlen($lightOutputsData)>0) {
                $lightOutputsData .= ',';
            }
            $lightOutputsData .= "\"" . $output->output_target . "\"";
        }
        $lightOutputsData = "\"light_outs\":[".$lightOutputsData."]";

        $genericOutputsData = '';
        foreach ($generic_outputs as $output) {
            if (strlen($genericOutputsData)>0) {
                $genericOutputsData .= ',';
            }
            $genericOutputsData .= "\"" . $output->output_target . "\"";
        }
        $genericOutputsData = "\"generic_outs\":[".$genericOutputsData."]";
        $outputsData = $timestamp.$lightOutputsData.",".$genericOutputsData;

        $on_schedule = array();
        $off_schedule = array();

        foreach($light_outputs as $output) {
            $rule_action_targets = $this->RuleActionTargets->find('all', [
                'conditions' => [
                    'target_id' => $output->id
                ]
            ]);

            foreach ($rule_action_targets as $rat) {
                $rule_action = $this->RuleActions->get($rat->rule_action_id);
                $rule_condition = $this->RuleConditions->findByRuleId($rule_action->rule_id)->first();
                if ($rule_condition->data_source == $this->RuleConditions->enumValueToKey('data_source', 'Time')) {
                    if ($rule_action->type == $this->RuleActions->enumValueToKey('type', 'Turn Off')) {
                        $offTime =  strtotime('today') + $rule_condition->trigger_threshold;
                        $onTime =  strtotime('today') + $rule_condition->reset_threshold;
                        if ( array_key_exists((string) $offTime, $off_schedule) ) {
                            $off_schedule[$offTime][] .= $output->output_target;
                        } else {
                            $off_schedule[$offTime] = [$output->output_target];
                        }

                        if ( array_key_exists((string) $onTime, $on_schedule) ) {
                            $on_schedule[$onTime][] .= $output->output_target;
                        } else {
                            $on_schedule[$onTime] = [$output->output_target];
                        }
                    } else if ($rule_action->type == $this->RuleActions->enumValueToKey('type', 'Turn On')) {
                        $offTime = strtotime('today') + $rule_condition->reset_threshold;
                        $onTime =  strtotime('today') + $rule_condition->trigger_threshold;
                        if ( array_key_exists((string) $offTime, $off_schedule) ) {
                            $off_schedule[$offTime][] .= $output->output_target;
                        } else {
                            $off_schedule[$offTime] = [$output->output_target];
                        }

                        if ( array_key_exists((string) $onTime, $on_schedule) ) {
                            $on_schedule[$onTime][] .= $output->output_target;
                        } else {
                            $on_schedule[$onTime] = [$output->output_target];
                        }
                    }
                }
            }
        }
        $failover_schedule = [];
        foreach($off_schedule as $key => $value) {
            $outputObject = (object) array(
                'light_outputs' => $value,
                'action' => 'off',
                'timestamp' => $key
            );
            array_push($failover_schedule, $outputObject);
        }

        foreach($on_schedule as $key => $value) {
            $outputObject = (object) array(
                'light_outputs' => $value,
                'action' => 'on',
                'timestamp' => $key
            );
            array_push($failover_schedule, $outputObject);
        }


        $schedule = "\"failover_schedule\":".json_encode($failover_schedule);
        $outputsData = $outputsData. ', '. $schedule;
        return $outputsData;
    }

    public function getRelayOutputsForDevice($deviceId) {
        $recorder = new SystemEventRecorder();
        // Check for outputs
        $params = [
            'conditions'=>[
                'device_id' => $deviceId,
                'output_type' => $this->enumValueToKey('output_type','Relay Output'),
                'status IN' => [$this->enumValueToKey('status','On'), $this->enumValueToKey('status','Force On')]
            ],
            'fields' => [
                'output_target',
                'id',
                'hardware_type'
            ]
        ];

        $outputs = $this->find('all',$params);
        $outputsData = '';
        foreach ($outputs as $output) {
            if (strlen($outputsData)>0) {
                $outputsData .= ',';
            }
            $outputsData .= "\"" . $output->output_target . "\"";

            $recorder->recordEvent('system_events', 'returned_output', 1, [
                'output_id' => $output->id, 
                'device_id' => $deviceId, 
                'hardware_type' => $output->hardware_type
            ]);
        }
        if (strlen($outputsData)>0)
        {
            $outputsData = "\"outs\":[".$outputsData."]";
        }
        return $outputsData;
    }

    // If $enable is true, turn things on, otherwise, turn them off.
    public function actOnRule($ruleAction,$enable) {
        $this->RuleActionTargets = TableRegistry::get('RuleActionTargets');
        $targets = $this->RuleActionTargets->findAllByRuleActionId($ruleAction->id);
        foreach($targets as $target) {
            if ($target->target_type == $this->RuleActionTargets->enumValueToKey('target_type','Output') &&
                $target->is_default == false &&
                $target->status != $this->RuleActionTargets->enumValueToKey('status', 'Disabled')
            ) {
                $output = $this->get($target->target_id);
                $this->_toggleOutput($ruleAction,$enable,$output);
            }
        }
    }

    private function _toggleOutput($ruleAction,$enable,$output) {
        $RuleActions = TableRegistry::get('RuleActions');
        $this->Rules = TableRegistry::get('Rules');

        $recorder = new SystemEventRecorder();
        $rule = $this->Rules->get($ruleAction->rule_id);

        # As long as we're not in a Forced Override State, then toggle the output.
        if (
            $output->status != $this->enumValueToKey('status','Force On') && 
            $output->status != $this->enumValueToKey('status','Force On')
        ) {
            # Evaluate High Temp Shutdown rules differently.
            if (intval($rule->type) == intval($this->Rules->enumValueToKey('type','High Temp Shutdown'))) {
                if ($enable == false && $output->pre_high_temp_shutdown_status != null) {
                    $output->status = $output->pre_high_temp_shutdown_status;
                    $output->pre_high_temp_shutdown_status = null;
                } else if ($enable == true && $output->status != $this->enumValueToKey('status','High Temp Shutdown')) {
                    # Save the pre shutdown status for when the rule is reset
                    $output->pre_high_temp_shutdown_status = $output->status;
                    $output->status = $this->enumValueToKey('status','High Temp Shutdown');
                }
                $this->save($output);
                return;
            } else {
                # Don't undo High Temp Shutdown Outputs. Only those rules should do that.
                if ($output->status != $this->enumValueToKey('status','High Temp Shutdown')) {
                    # Rule threshold is crossed, enact action
                    if (
                        ($enable == true && $ruleAction->type == $RuleActions->enumValueToKey('type','Turn On'))
                        ||
                        ($enable == false && $ruleAction->type == $RuleActions->enumValueToKey('type','Turn Off'))
                    ) {
                        $this->_turnOn($output, $ruleAction);
                    } else  if (
                        ($enable == false && $ruleAction->type == $RuleActions->enumValueToKey('type','Turn On'))
                        ||
                        ($enable == true && $ruleAction->type == $RuleActions->enumValueToKey('type','Turn Off'))
                    ) {
                        $this->_turnOff($output, $ruleAction);
                    }
                    $output->dontNotify = true;
                    $this->save($output);
                }
            }
        }
    }

    private function _turnOn($output, $ruleAction) {
        $recorder = new SystemEventRecorder();
        $recorder->recordEvent('system_events', 'toggle_output', 1, [
            'output_id' => $output->id, 
            'pre_toggle_status' => $output->status, 
            'post_toggle_status' => $this->enumValueToKey('status','On'), 
            'notification_level' => $ruleAction->notification_level, 
            'rule_id' => $ruleAction->rule_id, 
            'rule_action_id' => $ruleAction->id,
            'output_type' => $output->type

        ]);
        $output->status = $this->enumValueToKey('status','On');
        if ($output->output_type == $this->enumValueToKey('output_type','URL Output')) {
            exec("nohup curl -k ".$ruleAction->output_on_value." > /dev/null &");
        }
    }

    private function _turnOff($output, $ruleAction) {
        $recorder = new SystemEventRecorder();
        $recorder->recordEvent('system_events', 'toggle_output', 1, [
            'output_id' => $output->id, 
            'pre_toggle_status' => $output->status, 
            'post_toggle_status' => $this->enumValueToKey('status','Off'), 
            'notification_level' => $ruleAction->notification_level, 
            'rule_id' => $ruleAction->rule_id, 
            'rule_action_id' => $ruleAction->id,
            'output_type' => $output->type
        ]);
        $output->status = $this->enumValueToKey('status','Off');
        if ($output->output_type == $this->enumValueToKey('output_type','URL Output')) {
            exec("nohup curl -k ".$ruleAction->output_off_value." > /dev/null &");
        }
    }
}
