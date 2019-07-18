<?php
namespace App\Model\Table;

use Cake\Datasource\EntityInterface;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use SoftDelete\Model\Table\SoftDeleteTrait;

/**
 * Appliances Model
 *
 * @property \App\Model\Table\OutputsTable|\Cake\ORM\Association\BelongsTo $Outputs
 * @property \Cake\ORM\Association\BelongsTo $ApplianceTypes
 *
 * @method \App\Model\Entity\Appliance get($primaryKey, $options = [])
 * @method \App\Model\Entity\Appliance newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Appliance[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Appliance|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Appliance patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Appliance[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Appliance findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @property \App\Model\Table\ApplianceTemplatesTable|\Cake\ORM\Association\BelongsTo $ApplianceTemplates
 * @property \App\Model\Table\ZonesTable|\Cake\ORM\Association\BelongsToMany $Zones
 */
class AppliancesTable extends Table
{
    use SoftDeleteTrait;

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('appliances');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Mappable');
        $this->addBehavior('Organization');

        $this->belongsTo('Outputs', [
            'foreignKey' => 'output_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('ApplianceTemplates', [
            'foreignKey' => 'appliance_template_id',
            'joinType' => 'INNER'
        ]);

        $this->belongsToMany('Zones', [
            'joinTable' => 'appliances_zones'
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

//        $validator
//            ->requirePresence('label', 'create')
//            ->notEmpty('label');

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
        $rules->add($rules->existsIn(['output_id'], 'Outputs'));
        //$rules->add($rules->existsIn(['appliance_template_id'], 'ApplianceTemplates'));

        return $rules;
    }
    
}
