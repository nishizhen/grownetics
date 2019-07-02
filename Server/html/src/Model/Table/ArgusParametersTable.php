<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ArgusParameters Model
 *
 * @property \App\Model\Table\ArgusParametersTable|\Cake\ORM\Association\BelongsTo $ArgusParameters
 * @property \App\Model\Table\ArgusParametersTable|\Cake\ORM\Association\HasMany $ArgusParameters
 *
 * @method \App\Model\Entity\ArgusParameter get($primaryKey, $options = [])
 * @method \App\Model\Entity\ArgusParameter newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\ArgusParameter[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ArgusParameter|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ArgusParameter|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ArgusParameter patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ArgusParameter[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\ArgusParameter findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ArgusParametersTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('argus_parameters');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('ArgusParameters', [
            'foreignKey' => 'argus_parameter_id'
        ]);
        $this->hasMany('ArgusParameters', [
            'foreignKey' => 'argus_parameter_id'
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
            ->dateTime('deleted')
            ->allowEmpty('deleted');

        $validator
            ->integer('status')
            ->allowEmpty('status');

        $validator
            ->scalar('label')
            ->allowEmpty('label');

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
        $rules->add($rules->existsIn(['argus_parameter_id'], 'ArgusParameters'));

        return $rules;
    }
}
