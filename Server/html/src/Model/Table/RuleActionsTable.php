<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use SoftDelete\Model\Table\SoftDeleteTrait;
use Cake\ORM\TableRegistry;
/**
 * RuleActions Model
 *
 * @method \App\Model\Entity\RuleAction get($primaryKey, $options = [])
 * @method \App\Model\Entity\RuleAction newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\RuleAction[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\RuleAction|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\RuleAction patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\RuleAction[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\RuleAction findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @mixin \App\Model\Behavior\EnumBehavior
 */
class RuleActionsTable extends Table
{

    public $enums = array(
        'status' => array(
            'Disabled',
            'Enabled',
            'Triggered',
        ),
        'notification_level' => array(
            'Logged Only',
            'Dashboard Notification',
            'Dashboard Alert',
            'Dashboard Alarm',
            'Email',
            'Text Message',
            'Phone Call',
        ),
        'type' => array(
            'Notification Only',
            'Sensor Update',
            'Turn On',
            'Turn Off',
            'Toggle',
            'Set Point',
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

        $this->setTable('rule_actions');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Enum');

        $this->belongsTo('Rules', [
            'foreignKey' => 'rule_id'
        ]);

        $this->hasMany('RuleActionTargets', [
            'foreignKey' => 'rule_action_id'
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
            ->integer('type')
            ->allowEmpty('type');

        $validator
            ->integer('notification_level')
            ->allowEmpty('notification_level');

        $validator
            ->integer('notification_user_role')
            ->allowEmpty('notification_user_role');

        $validator
            ->allowEmpty('output_on_value');

        $validator
            ->allowEmpty('output_off_value');

        return $validator;
    }

    public function actOnRule($rule,$triggered,$notificationRuleCondition) {
        $Notifications = TableRegistry::get('Notifications');
        $RuleActionTargets = TableRegistry::get('RuleActionTargets');

        $actions = $this->find('all',[
            'conditions' => [
                'rule_id' => $rule->id,
                'is_default' => false
            ]
        ]);

        foreach ($actions as $action) {
            # If the rule has triggered, and the action is set to execute on trigger, or we're auto resetting
            # OR the rule is resetting, and the action is set to execute on reset
            if (($action->on_trigger && ($triggered || $rule->autoreset)) || (!$triggered && !$action->on_trigger)) {
                $Notifications->actOnRule($action,$notificationRuleCondition);
                $rats = $RuleActionTargets->find('all',[
                   'conditions' => [
                       'rule_action_id' => $action->id,
                       'status IS NOT' => $RuleActionTargets->enumValueToKey('status', 'Disabled')
                   ]
                ])->all();
                foreach ($rats as $rat) {
                    $RuleActionTargets->actOnAction($action, $rat,$triggered);
                }
            }
        }
    }
}
