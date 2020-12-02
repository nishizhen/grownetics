<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use SoftDelete\Model\Table\SoftDeleteTrait;
use Cake\I18n\Number;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use Cake\Cache\Cache;



/**
 * MapItems Model
 *
 * @property \App\Model\Table\FloorplansTable|\Cake\ORM\Association\BelongsTo $Floorplans
 * @property \Cake\ORM\Association\BelongsTo $Mapitemtypes
 *
 * @method \App\Model\Entity\MapItem get($primaryKey, $options = [])
 * @method \App\Model\Entity\MapItem newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\MapItem[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\MapItem|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\MapItem patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\MapItem[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\MapItem findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @property \App\Model\Table\MapItemTypesTable|\Cake\ORM\Association\BelongsTo $MapItemTypes

 */
class MapItemsTable extends Table
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

    $this->setTable('map_items');
    $this->setDisplayField('id');
    $this->setPrimaryKey('id');

    $this->addBehavior('Timestamp');
    //$this->addBehavior('Mappable');
    $this->addBehavior('Organization');

    $this->belongsTo('Floorplans', [
      'foreignKey' => 'floorplan_id',
      'joinType' => 'INNER'
    ]);
    $this->belongsTo('MapItemTypes', [
      'foreignKey' => 'map_item_type_id',
      'joinType' => 'INNER'
    ]);

    //        $this->belongsTo('Plants', [
    //            'joinType' => 'INNER'
    //        ]);

    $this->belongsToMany('Zones', [
      'joinTable' => 'map_items_zones'
    ]);
    //        $this->hasOne('Sensors');
    //        $this->hasOne('Devices');

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
    //            ->decimal('latitude')
    //            ->requirePresence('latitude', 'create')
    //            ->notEmpty('latitude');
    //
    //        $validator
    //            ->decimal('longitude')
    //            ->requirePresence('longitude', 'create')
    //            ->notEmpty('longitude');

    $validator
      ->allowEmpty('geoJSON');

    $validator
      ->decimal('offsetHeight')
      ->allowEmpty('offsetHeight');

    $validator
      ->requirePresence('label', 'create')
      ->notEmpty('label');

    $validator
      ->boolean('deleted')
      //->requirePresence('deleted', 'create')
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
  public function buildRules(RulesChecker $rules)
  {
    //        $rules->add($rules->existsIn(['floorplan_id'], 'Floorplans'));
    $rules->add($rules->existsIn(['map_item_type_id'], 'MapItemTypes'));

    return $rules;
  }

  public function beforeSave($event, $entity)
  {
    if ($entity->isNew()) {
      if (isset($entity->latitude) && isset($entity->longitude)) {
        $entity->latitude = Number::format($entity->latitude, ['precision' => 16]);
        $entity->longitude = Number::format($entity->longitude, ['precision' => 16]);
      }

      if (isset($entity->offsetHeight)) {
        $entity->offsetHeight = Number::format($entity->offsetHeight, ['precision' => 4]);
      }

      $entity->zones = [];


      if (!isset($this->MapItemTypes)) {
        $this->MapItemTypes = TableRegistry::get("MapItemTypes");
      }

      if (isset($entity->type)) {
        $mapItemType = $this->MapItemTypes->find()->where(['label' => $entity->type])->first();
        if (!isset($mapItemType)) {
          $mapItemType = $this->MapItemTypes->newEntity([
            'label' => $entity->type,
            'opacity' => 1
          ]);
          $this->MapItemTypes->save($mapItemType);
        }

        $entity->map_item_type = $mapItemType;
      } else {
        $mapItemType = $this->MapItemTypes->find()->where(['label' => "Map Item"])->first();
        if (!isset($mapItemType)) {
          $mapItemType = $this->MapItemTypes->newEntity([
            'label' => "Map Item",
            'opacity' => 1
          ]);
          $this->MapItemTypes->save($mapItemType);
        }
        $entity->map_item_type = $mapItemType;
      }
    }
  }

  public function afterSave($event, $entity, $options)
  {
    if (
      Cache::read('floorplan_map_items_json_decoded') === true ||
      Cache::read('floorplan_map_items') === true ||
      Cache::read('floorplan_plant_placeholders_json_decoded') === true ||
      Cache::read('floorplan_plant_placeholders') === true
    ) {
      Cache::delete('floorplan_map_items_json_decoded');
      Cache::delete('floorplan_map_items');
      Cache::delete('floorplan_plant_placeholders_json_decoded');
      Cache::delete('floorplan_plant_placeholders');
    }
  }

  public function afterDelete($event, $entity, $options)
  {
    Cache::delete('floorplan_map_items_json_decoded');
    Cache::delete('floorplan_map_items');
    Cache::delete('floorplan_plant_placeholders_json_decoded');
    Cache::delete('floorplan_plant_placeholders');
  }
}
