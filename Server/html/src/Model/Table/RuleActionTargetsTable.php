<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\TableRegistry;


/**
 * RuleActionTargets Model
 *
 * @property \App\Model\Table\RuleActionsTable|\Cake\ORM\Association\BelongsTo $RuleActions
 * @property \Cake\ORM\Association\BelongsTo $Targets
 * @property \Cake\ORM\Association\BelongsTo $ApplianceTypes
 *
 * @method \App\Model\Entity\RuleActionTarget get($primaryKey, $options = [])
 * @method \App\Model\Entity\RuleActionTarget newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\RuleActionTarget[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\RuleActionTarget|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\RuleActionTarget patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\RuleActionTarget[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\RuleActionTarget findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @property \App\Model\Table\OutputsTable|\Cake\ORM\Association\BelongsTo $Outputs
 * @property \App\Model\Table\ZonesTable|\Cake\ORM\Association\BelongsTo $Zones
 * @property \App\Model\Table\ApplianceTemplatesTable|\Cake\ORM\Association\BelongsTo $ApplianceTemplates
 * @property \App\Model\Table\SetPointsTable|\Cake\ORM\Association\HasOne $SetPoints
 * @mixin \App\Model\Behavior\EnumBehavior
 */
class RuleActionTargetsTable extends Table
{

    public $enums = array(
        'target_type' => array(
            'Output',
            'Set Point',
            'Appliance',
            'ApplianceTemplate',
            'ApplianceType'
        ),
        'status' => array(
            'Disabled',
            'Enabled',
            'Powered',
            'Set'
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

        $this->setTable('rule_action_targets');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Enum');
        $this->addBehavior('Organization');

        $this->belongsTo('RuleActions', [
            'foreignKey' => 'rule_action_id'
        ]);
        $this->belongsTo('Outputs', [
            'foreignKey' => 'target_id',
            'conditions' => ['RuleActionTargets.target_type' => $this->enumValueToKey('target_type','Output')]
        ]);
        $this->belongsTo('Zones', [
            'foreignKey' => 'target_id',
            'conditions' => ['RuleActionTargets.target_type' => $this->enumValueToKey('target_type','Zone')]
        ]);
        $this->belongsTo('ApplianceTemplates', [
            'foreignKey' => 'appliance_template_id'
        ]);

        $this->hasOne('SetPoints', [
            'bindingKey' => 'target_id',
            'conditions' => ['RuleActionTargets.target_type' => $this->enumValueToKey('target_type','Set Point')]
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
            ->integer('target_type')
            ->allowEmpty('target_type');

        $validator
            ->integer('status')
            ->allowEmpty('status');

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
        $rules->add($rules->existsIn(['appliance_template_id'], 'ApplianceTemplates'));

        return $rules;
    }

    # If $enable is true, turn things on, otherwise, turn them off.
    public function actOnAction($action, $rat, $enable) {
        $Outputs = TableRegistry::get('Outputs');
        switch($rat->target_type) {
            case $this->enumValueToKey('target_type','Output'):
                $Outputs->actOnRule($action,$enable);
            break;
            case $this->enumValueToKey('target_type','Zone'):
                # Load all appliances of the appliance_template_id in the zone
            break;
            case $this->enumValueToKey('target_type','Set Point'):
                $rat->status = $this->enumValueToKey('status','Set');
                $this->save($rat);
            break;

        }
    }

}
