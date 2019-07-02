<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use SoftDelete\Model\Table\SoftDeleteTrait;

/**
 * MapItemTypes Model
 *
 * @method \App\Model\Entity\MapItemType get($primaryKey, $options = [])
 * @method \App\Model\Entity\MapItemType newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\MapItemType[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\MapItemType|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\MapItemType patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\MapItemType[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\MapItemType findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class MapItemTypesTable extends Table
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

        $this->setTable('map_item_types');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
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

        // $validator
        //     ->requirePresence('color', 'create')
        //     ->notEmpty('color');

        $validator
            ->decimal('opacity')
            ->requirePresence('opacity', 'create')
            ->notEmpty('opacity');

        // $validator
        //     ->requirePresence('style', 'create')
        //     ->notEmpty('style');

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
