<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use SoftDelete\Model\Table\SoftDeleteTrait;

/**
 * Cultivar Model
 *
 * @property \Cake\ORM\Association\HasMany $HarvestBatch
 * @property \Cake\ORM\Association\HasMany $Votes
 *
 * @method \App\Model\Entity\Cultivar get($primaryKey, $options = [])
 * @method \App\Model\Entity\Cultivar newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Cultivar[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Cultivar|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Cultivar patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Cultivar[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Cultivar findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @mixin \App\Model\Behavior\NotifierBehavior
 */
class CultivarsTable extends Table
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

        $this->setTable('cultivars');
        $this->setDisplayField('label');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Notifier',[
            'notification_level' => 1
        ]);

        $this->hasMany('HarvestBatch', [
            'foreignKey' => 'cultivar_id'
        ]);
        $this->hasMany('Notes', [
            'foreignKey' => 'cultivar_id',
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
            ->requirePresence('label', 'create')
            ->notEmpty('label');

        $validator
            ->boolean('deleted')
            ->allowEmpty('deleted');

        $validator
            ->dateTime('deleted_date')
            ->allowEmpty('deleted_date');

        return $validator;
    }
}
