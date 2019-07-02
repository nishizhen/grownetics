<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Localized\Validation\UsValidation;
use SoftDelete\Model\Table\SoftDeleteTrait;

/**
 * UserContactMethods Model
 *
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 *
 * @method \App\Model\Entity\UserContactMethod get($primaryKey, $options = [])
 * @method \App\Model\Entity\UserContactMethod newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\UserContactMethod[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\UserContactMethod|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\UserContactMethod patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\UserContactMethod[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\UserContactMethod findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @mixin \App\Model\Behavior\EnumBehavior
 */
class UserContactMethodsTable extends Table
{
    use SoftDeleteTrait;

    public $enums = array(
        'type' => array(
            'SMS Number',
            'Phone Number',
            'Email'
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

        $this->setTable('user_contact_methods');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->addBehavior('Enum');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER'
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
            ->requirePresence('type', 'create')
            ->notEmpty('type');

        return $validator;
    
    }

    public function validationEmail($validator)
    {
        $validator
            ->add('email', 'valid-email', [
                'rule' => 'email',
                'message' => __('The format was incorrect.'),
            ]);
        return $validator;
    }

    public function validationPhone($validator)
    {   
        
        $validator->provider('us', UsValidation::class);
        $validator->add('phone', 'phoneFormatCheck', [
            'rule' => 'phone',
            'provider' => 'us',
            'message' => 'Example formats: (###) ### ###, ###-###-####'
        ])
        ->notEmpty('phone');
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
        $rules->add($rules->existsIn(['user_id'], 'Users'));

        return $rules;
    }
}
