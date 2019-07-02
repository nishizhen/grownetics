<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use SoftDelete\Model\Table\SoftDeleteTrait;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;

/**
 * Rules Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Data
 * @property \Cake\ORM\Association\BelongsTo $ParentRules
 * @property \App\Model\Table\NotificationsTable|\Cake\ORM\Association\HasMany $Notifications
 * @property \Cake\ORM\Association\BelongsToMany $Outputs
 *
 * @method \App\Model\Entity\Rule get($primaryKey, $options = [])
 * @method \App\Model\Entity\Rule newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Rule[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Rule|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Rule patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Rule[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Rule findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @property \App\Model\Table\RuleConditionsTable|\Cake\ORM\Association\HasMany $RuleConditions
 * @mixin \App\Model\Behavior\EnumBehavior
 * @mixin \App\Model\Behavior\NotifierBehavior
 */
class RulesTable extends Table
{
    use SoftDeleteTrait;

    public $enums = array(
        'status' => array(
            'Disabled',
            'Enabled',
            'Triggered'
        ),
        'type' => array(
            'Generic',
            'Lighting Schedule',
            'High Temp Shutdown',
            'HVAC',
            'Co2 Control'
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

        $this->setTable('rules');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Enum');
        $this->addBehavior('Notifier',[
            'notification_level' => 1
        ]);
        $this->addBehavior('FeatureFlags.FeatureFlags');

        $this->hasMany('Notifications', [
            'foreignKey' => 'rule_id'
        ]);
        $this->hasMany('RuleConditions', [
            'foreignKey' => 'rule_id'
        ]);
        $this->hasMany('RuleActions', [
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
            ->integer('status')
            ->requirePresence('status', 'create')
            ->notEmpty('status');

        return $validator;
    }

    # This sets the statuses of the various conditions. These rules are then
    # handled with the rest of the Zone rules in ZonesTable->processRules

    public function processTimedRules($shell=null) {
        $RuleActions = TableRegistry::get('RuleActions');
        $RuleConditions = TableRegistry::get('RuleConditions');
        $Outputs = TableRegistry::get('Outputs');

        $params = array(
            'conditions'=>array(
                'OR' => [['status'=>$Outputs->enumValueToKey('status','On')],
                    ['status'=>$Outputs->enumValueToKey('status','Off')]]
            ),
            'fields' => array('id')
        );
        $all_outputs = $Outputs->find('all',$params)->all();
        $all_outputs_ids = array();

        foreach ($all_outputs as $output) {
            $all_outputs_ids[] = $output->id;
        }

        if (sizeof($all_outputs_ids) < 1) {
            return false;
        }

        $this->recursive = -1;
        $rule_conditions = $RuleConditions->find('all',array(
            'conditions' => array(
                'data_source IN' => array(
                    $RuleConditions->enumValueToKey('data_source','Time'),
                    $RuleConditions->enumValueToKey('data_source','Interval')
                ),
                'is_default' => false,
                'status IN' => [
                    $RuleConditions->enumValueToKey('status', 'Enabled'),
                    $RuleConditions->enumValueToKey('status', 'Triggered')
                ]
            )
        ));


        $today = strtotime('today');
        $seconds = time() - $today;
        if (env('DEV') && $this->getFeatureFlagValue("time_warp")) {
            if ($shell)
                $shell->out($seconds);
            # Make time run faster!! Muahaha
            # This basically makes the system run through a full day/night cycle every 2 minutes.
            # 24 hours * 30 minutes
            $seconds = $seconds * 720 % 86400;
            if ($shell)
                $shell->out($seconds);
        }

        foreach ($rule_conditions as $rule_condition) {
            if ($shell)
                $shell->out($rule_condition['label'].' - '.$rule_condition['status']);
            $rule = $this->get($rule_condition['rule_id'], ['contain' => ['RuleConditions']]);
            if ($rule_condition['data_source'] == $RuleConditions->enumValueToKey('data_source','Time')) {
                if ($rule['status'] == $this->enumValueToKey('status','Enabled')
                    &&
                    $seconds > $rule_condition['trigger_threshold']
                    &&
                    ( $seconds < $rule_condition['reset_threshold'] || !$rule_condition['reset_threshold'])
                ) {
                    $rule_condition['status'] = $RuleConditions->enumValueToKey('status', 'Triggered');
                } elseif (
                    $rule['status'] == $this->enumValueToKey('status','Triggered')
                    &&
                    (
                        $seconds > $rule_condition['reset_threshold']
                        ||
                        $seconds < $rule_condition['trigger_threshold']
                    )
                ) {
                    $rule_condition['status'] = $RuleConditions->enumValueToKey('status', 'Enabled');
                }
            } elseif ($rule['data_source'] == $RuleConditions->enumValueToKey('data_source','Interval')) {
                $intervalSeconds = $rule_condition['trigger_threshold'];
                $seconds = date('U');
                if ($seconds % $intervalSeconds == $seconds % ($intervalSeconds * 2)) {
                    // Intervals are toggles
                    if ($rule['status'] == $this->enumValueToKey('status','Enabled')) {
                        $rule_condition['status'] = $RuleConditions->enumValueToKey('status', 'Triggered');
                    }
                } else {
                    if ($rule['status'] == $this->enumValueToKey('status','Triggered')) {
                        $rule_condition['status'] = $RuleConditions->enumValueToKey('status', 'Enabled');
                    }
                }
            } // If time or interval
            $RuleConditions->save($rule_condition);
        } // Foreach
    }

    # This function decides whether to take action on each of a set of rules
    # based on the condition of all of the rules respective conditions

    # The conditions' statuses are set by RuleConditions->processConditions
    # and by RulesTable->processTimedRules. It is
    # called by the API controller to check rules on individual sensors and
    # called by ZonesTable when processing Zone rules

    public function processRules($ruleConditionIds) {
        if (empty($ruleConditionIds)) {
            return;
        }

        $RuleConditions = TableRegistry::get('RuleConditions');
        $RuleActions = TableRegistry::get('RuleActions');
        if ($ruleConditionIds != []) {
            $rules = $this->find('all', [
                    'contain' => [
                        'RuleConditions' => [
                            'conditions' => [
                                'RuleConditions.id IN' => $ruleConditionIds
                            ]
                        ]
                    ],
                    'conditions' => [
                        'is_default' => false
                    ]
                ]
            );
        } else {
            $rules = [];
        }

        foreach ($rules as $rule) {
            # Skip rules that did not return any conditions
            if (sizeof($rule->rule_conditions) < 1) {
                continue;
            }
            $ruleTriggered = true;
            # If any of the rule conditions are not triggered, than the rule isn't triggered
            foreach ($rule->rule_conditions as $condition) {
                if ($condition['status'] == $RuleConditions->enumValueToKey('status','Enabled')) {
                    $ruleTriggered = false;
                }
            }
            # Grab the first rule condition as the condition to send notifications with
            $notificationRuleCondition = $rule->rule_conditions[0];

            $rule->dontNotify = true;
            # If we need to act on the rule, i.e., the rule status needs to change
            if (
                (
                    $rule->status == $this->enumValueToKey('status','Enabled')
                    &&
                    $ruleTriggered
                )
                ||
                (
                    $rule->status == $this->enumValueToKey('status','Triggered')
                    &&
                    !$ruleTriggered
                )
            ) {
                if ($rule->status == $this->enumValueToKey('status','Enabled'))
                {
                    $RuleActions->actOnRule($rule,true,$notificationRuleCondition);
                    $rule->status = $this->enumValueToKey('status','Triggered');
                    $this->save($rule);
                }
                else if ($rule->status == $this->enumValueToKey('status','Triggered'))
                {
                    $RuleActions->actOnRule($rule,false,$notificationRuleCondition);
                    $rule->status = $this->enumValueToKey('status','Enabled');
                    $this->save($rule);
                }
            }
        }
    }

    public function generateFromDefaultRules() {
        $this->RuleConditions = TableRegistry::get('RuleConditions');
        $this->RuleActions = TableRegistry::get('RuleActions');
        $this->RuleActionTargets = TableRegistry::get('RuleActionTargets');
        $this->Rules = TableRegistry::get('Rules');
        $this->Zones = TableRegistry::get('Zones');
        $this->Appliances = TableRegistry::get('Appliances');
        $this->ApplianceTemplates = TableRegistry::get('ApplianceTemplates');

        $zones = $this->Zones->find('all', ['contain' => ['Sensors']]);


        foreach ($zones as $zone)
        {
            $sensorTypes = [];
            if (!empty($zone['sensors'])) {
                foreach($zone['sensors'] as $sensor) {
                    if (!isset($sensorTypes[$sensor['sensor_type_id']]) || !is_array($sensorTypes[$sensor['sensor_type_id']])) {
                        $sensorTypes[$sensor['sensor_type_id']] = [];
                    }
                    array_push($sensorTypes[$sensor['sensor_type_id']],"source_id = '".$sensor['id']."'");
                }
            }

            foreach ($sensorTypes as $type => $value) {
                $activeRuleCondition = $this->RuleConditions->find('all', ['conditions' => ['is_default' => 0, 'data_id' => $zone->id, 'sensor_type' => $type]])->first();
                //if no non-default RuleCondition exists for a sensor_type, make Active Rules based on each Default Rule
                if (!$activeRuleCondition) {

                    //Find all Grownetics Default Rule Conditions for the Zone's plant_zone_type and 
                    // Datapoint's type.
                    $defaultRuleConditions = $this->RuleConditions->find('all', ['conditions' => ['is_default' => 1, 'data_source' => $this->RuleConditions->enumValueToKey('data_source', 'Zone Type'), 'data_id' => $zone->plant_zone_type_id, 'sensor_type' => $type]])->toArray();
                    if ($defaultRuleConditions) {
                        foreach ($defaultRuleConditions as $defaultRuleCondition) {

                            $newRule = $this->Rules->newEntity();
                            $newRuleCondition = $this->RuleConditions->newEntity();
                            $newRuleAction = $this->RuleActions->newEntity();
                            $newRuleActionTarget = $this->RuleActionTargets->newEntity();

                            $defaultRule = $this->Rules->get($defaultRuleCondition->rule_id);
                            $defaultRuleAction = $this->RuleActions->find('all', ['conditions' => ['is_default' => 1, 'rule_id' => $defaultRule->id]])->first();
                            $defaultRuleActionTarget = $this->RuleActionTargets->find('all', ['conditions' => ['is_default' => 1, 'rule_action_id' => $defaultRuleAction->id]])->first();

                            $newRule->status = $defaultRule->status;
                            $newRule->is_default = 0;
                            $newRule->label = $zone->label. " - ".$defaultRule->label;
                            $newRule->autoreset = $defaultRule->autoreset;
                            $this->Rules->save($newRule);

                            $newRuleCondition->label = $defaultRuleCondition->label;
                            $newRuleCondition->data_source = $this->RuleConditions->enumValueToKey('data_source', 'Zone');
                            $newRuleCondition->data_id = $zone->id;
                            $newRuleCondition->sensor_type = $defaultRuleCondition->sensor_type;
                            $newRuleCondition->operator = $defaultRuleCondition->operator;
                            $newRuleCondition->trigger_threshold = $defaultRuleCondition->trigger_threshold;
                            $newRuleCondition->reset_threshold = $defaultRuleCondition->reset_threshold;
                            $newRuleCondition->status = $defaultRuleCondition->status;
                            $newRuleCondition->zone_behavior = $defaultRuleCondition->zone_behavior;
                            $newRuleCondition->trigger_delay = $defaultRuleCondition->trigger_delay;
                            $newRuleCondition->pending_time = $defaultRuleCondition->pending_time;
                            $newRuleCondition->rule_id = $newRule->id;
                            $newRuleCondition->is_default = 0;
                            $this->RuleConditions->save($newRuleCondition);

                            $newRuleAction->type = $defaultRuleAction->type;
                            $newRuleAction->status = $defaultRuleAction->status;
                            $newRuleAction->notification_level = $defaultRuleAction->notification_level;
                            $newRuleAction->rule_id = $newRule->id;
                            $newRuleAction->on_trigger = $defaultRuleAction->on_trigger;
                            $newRuleAction->is_default = 0;
                            $this->RuleActions->save($newRuleAction);

                            // if there is a defaultRAT set then
                            // find all ApplianceTemplates with Type = the default rule's target_id
                            if (isset($defaultRuleActionTarget->target_id)) {
                                $applianceTemplates = $this->ApplianceTemplates->find('all', ['conditions' => ['appliance_type_id' => $defaultRuleActionTarget->target_id]]);
                            } else {
                                $applianceTemplates = [];
                            }
                            foreach ($applianceTemplates as $applianceTemplate) {
                                $appliances = $this->Appliances->find('all', ['conditions' => ['appliance_template_id' => $applianceTemplate->id]]);

                                foreach ($appliances as $appliance) {
                                    $newRuleActionTarget->rule_action_id = $newRuleAction->id;
                                    //Appliance target_type
                                    $newRuleActionTarget->target_type = $this->RuleActionTargets->enumValueToKey('target_type', 'Output');
                                    $newRuleActionTarget->target_id = $appliance->output_id;
                                    $newRuleActionTarget->status = $defaultRuleActionTarget->status;
                                    $newRuleActionTarget->is_default = 0;
                                    $this->RuleActionTargets->save($newRuleActionTarget);

                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
