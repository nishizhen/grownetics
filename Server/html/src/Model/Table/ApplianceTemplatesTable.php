<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use SoftDelete\Model\Table\SoftDeleteTrait;

/**
 * ApplianceTypes Model
 *
 * @property \Cake\ORM\Association\HasMany $RuleActionTargets
 *
 * @method \App\Model\Entity\ApplianceTemplate get($primaryKey, $options = [])
 * @method \App\Model\Entity\ApplianceTemplate newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\ApplianceTemplate[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ApplianceTemplate|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ApplianceTemplate patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ApplianceTemplate[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\ApplianceTemplate findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ApplianceTemplatesTable extends Table
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

        $this->setTable('appliance_templates');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');



//        $this->hasMany('RuleActionTargets', [
//            'foreignKey' => 'appliance_template_id'
//        ]);
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

        return $validator;
    }
}
