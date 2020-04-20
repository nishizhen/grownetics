<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use SoftDelete\Model\Table\SoftDeleteTrait;
use Cake\Datasource\ConnectionManager;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;


/**
 * Floorplans Model
 *
 * @property \App\Model\Table\FacilitiesTable|\Cake\ORM\Association\BelongsTo $Facilities
 *
 * @method \App\Model\Entity\Floorplan get($primaryKey, $options = [])
 * @method \App\Model\Entity\Floorplan newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Floorplan[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Floorplan|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Floorplan patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Floorplan[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Floorplan findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @property \App\Model\Table\SensorsTable|\Cake\ORM\Association\HasMany $Sensors
 * @property \App\Model\Table\MapItemsTable|\Cake\ORM\Association\HasMany $MapItems
 * @property \App\Model\Table\ZonesTable|\Cake\ORM\Association\HasMany $Zones
 * @mixin \App\Model\Behavior\NotifierBehavior
 */
class FloorplansTable extends Table
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

        $this->setTable('floorplans');
        $this->setDisplayField('label');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Notifier');
        $this->addBehavior('Organization');

//        $this->belongsTo('Facilities', [
//            'foreignKey' => 'facility_id',
//            'joinType' => 'INNER'
//        ]);
//        $this->belongsTo('Sensors');
//
//        $this->hasMany('Sensors');

        $this->hasMany('MapItems');
        $this->hasMany('Zones');

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
            ->integer('floor_level')
            ->allowEmpty('floor_level');

        $validator
            ->integer('square_footage')
            ->allowEmpty('square_footage');

        $validator
            ->allowEmpty('description');

        $validator
            ->allowEmpty('floorplan_image');

        $validator
            ->decimal('latitude')
            ->allowEmpty('latitude');

        $validator
            ->decimal('longitude')
            ->allowEmpty('longitude');

        $validator
            ->integer('offsetRotation')
            ->allowEmpty('offsetRotation');

        $validator
            ->allowEmpty('geoJSON');

        $validator
            ->integer('status');
            //->requirePresence('status', 'create')
            //->notEmpty('status');

        $validator
            ->boolean('deleted')
            ->allowEmpty('deleted');

        $validator
            ->dateTime('deleted')
            ->allowEmpty('deleted');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
//    public function buildRules(RulesChecker $rules)
//    {
//
//        return $rules;
//    }

//    public function beforeSave(Event $event, EntityInterface $entity)
//    {
//        //TODO: move processing code from floorplan controller
//    }

    public function clearImport()
    {
        $connection = ConnectionManager::get('default');
        $results = $connection->query('
            TRUNCATE TABLE `appliances`;
            TRUNCATE TABLE `appliances_zones`;
            TRUNCATE TABLE `batch_recipe_entries`;
            TRUNCATE TABLE `cultivars`;
            TRUNCATE TABLE `devices`;
            TRUNCATE TABLE `floorplans`;
            TRUNCATE TABLE `harvest_batches`;
            TRUNCATE TABLE `map_items`;
            TRUNCATE TABLE `map_item_types`;
            TRUNCATE TABLE `map_items_zones`;
            TRUNCATE TABLE `notes`;
            TRUNCATE TABLE `notifications`;
            TRUNCATE TABLE `plants`;
            TRUNCATE TABLE `recipe_entries`;
            TRUNCATE TABLE `recipes`;
            TRUNCATE TABLE `sensors`;
            TRUNCATE TABLE `sensors_zones`;
            TRUNCATE TABLE `set_points`;
            TRUNCATE TABLE `tasks`;
            TRUNCATE TABLE `zones`;
        ');
    }
}
