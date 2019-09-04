<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * MapItemsZones Model
 *
 * @property \App\Model\Table\MapItemsTable|\Cake\ORM\Association\BelongsTo $MapItems
 * @property \App\Model\Table\ZonesTable|\Cake\ORM\Association\BelongsTo $Zones
 *
 * @method \App\Model\Entity\MapItemsZone get($primaryKey, $options = [])
 * @method \App\Model\Entity\MapItemsZone newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\MapItemsZone[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\MapItemsZone|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\MapItemsZone|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\MapItemsZone patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\MapItemsZone[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\MapItemsZone findOrCreate($search, callable $callback = null, $options = [])
 */
class MapItemsZonesTable extends Table
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

        $this->setTable('map_items_zones');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Organization');

        $this->belongsTo('MapItems', [
            'foreignKey' => 'map_item_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Zones', [
            'foreignKey' => 'zone_id',
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
            ->integer('owner_type')
            ->allowEmpty('owner_type');

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
        $rules->add($rules->existsIn(['map_item_id'], 'MapItems'));
        $rules->add($rules->existsIn(['zone_id'], 'Zones'));

        return $rules;
    }
}
