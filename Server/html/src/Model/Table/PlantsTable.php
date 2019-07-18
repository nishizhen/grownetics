<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Cache\Cache;
use Cake\Validation\Validator;
use SoftDelete\Model\Table\SoftDeleteTrait;
use Cake\ORM\TableRegistry;
use App\Lib\DataConverter;


/**
 * Plants Model
 *
 * @property \App\Model\Table\PlantsTable|\Cake\ORM\Association\HasMany $Plants
 * @property \Cake\ORM\Association\BelongsTo $ShortPlants
 * @property \App\Model\Table\ZonesTable|\Cake\ORM\Association\BelongsTo $Zones
 * @property \App\Model\Table\MapItemsTable|\Cake\ORM\Association\BelongsTo $MapItems
 * @property \App\Model\Table\HarvestBatchesTable|\Cake\ORM\Association\BelongsTo $HarvestBatches
 * @property \App\Model\Table\RecipesTable|\Cake\ORM\Association\BelongsTo $Recipes
 * @property \Cake\ORM\Association\HasMany
 *
 * @method \App\Model\Entity\Plant get($primaryKey, $options = [])
 * @method \App\Model\Entity\Plant newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Plant[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Plant|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Plant patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Plant[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Plant findOrCreate($search, callable $callback = null, $options = [])
 * @property \App\Model\Table\TasksTable|\Cake\ORM\Association\BelongsTo $Tasks
 * @mixin \App\Model\Behavior\EnumBehavior
 */
class PlantsTable extends Table
{
    use SoftDeleteTrait;

    public $enums = array(
        'status' => array(
            'Pending',
            'Planted',
            'Harvested',
            'Destroyed'
        )
    );

    // @codeCoverageIgnoreStart
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('plants');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Enum');
        $this->addBehavior('Mappable');
        $this->addBehavior('Muffin/Footprint.Footprint', [
            'events' => [
                'Model.beforeSave' => [
                    'show_metric' => 'always'
                ]
            ],
            'propertiesMap' => [
                'show_metric' => '_footprint.show_metric',
            ],
        ]);
        $this->addBehavior('Organization');

        $this->belongsTo('MapItems', [
            'foreignKey' => 'map_item_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('HarvestBatches', [
            'foreignKey' => 'harvest_batch_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Recipes', [
            'foreignKey' => 'recipe_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Tasks', [
            'foreignKey' => 'harvest_batch_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Zones', [
            'foreignKey' => 'zone_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsToMany('Notes', [
            'foreignKey' => 'plant_id',
            'targetForeignKey' => 'note_id',
            'joinTable' => 'notes_plants'
        ]);
        $this->belongsTo('Cultivars', [
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
            ->integer('status');

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
        //$rules->add($rules->existsIn(['map_item_id'], 'MapItems'));
        $rules->add($rules->existsIn(['harvest_batch_id'], 'HarvestBatches'));
        $rules->add($rules->existsIn(['recipe_id'], 'Recipes'));

        return $rules;
    }
    // @codeCoverageIgnoreEnd

    public function beforeSave($event, $entity, $options)
    {
        $this->Sensors = TableRegistry::get('Sensors');
        $converter = new DataConverter();

        if (isset($options['_footprint'])) {
            $metric = $options['_footprint']['show_metric'];
        } else {
            $metric = true;
        }
        if (isset($options['weightField'])) {
            $entity[$options['weightField']] = $converter->convertUnits($entity[$options['weightField']], $this->Sensors->enumValueToKey('data_type', 'Weight'), $metric);
        }
    }

    public function afterSave($event, $entity, $options)
    {
        Cache::delete('floorplan_plants_json_decoded');
        Cache::delete('floorplan_plants');
    }

    public function afterDelete($event, $entity, $options)
    {
        Cache::delete('floorplan_plants_json_decoded');
        Cache::delete('floorplan_plants');
    }

    public function findNotDestroyed(Query $query, array $options)
    {
        $batchId = $options['batchId'];
        return $query->where(['harvest_batch_id' => $batchId, 'status IS NOT' => $this->enumValueToKey('status', 'Destroyed')])->order(['short_plant_id' => 'asc']);
    }

    public function generatePlantsForBatch($batch_id = null, $start_id = null, $end_id = null, $plant_list = null, $cultivar_id = null)
    {
        $this->HarvestBatches = TableRegistry::get('HarvestBatches');
        $batch = $this->HarvestBatches->get($batch_id);
        if ($batch->current_zone) {
            $batch_zone = $batch->current_zone;
        } else {
            $batch_zone = ['id' => 0];
        }
        $iterator = 0;
        $beginning_id = '';
        $new_plants = [];

        # If we have a range of IDs, generate the relevant plants.
        if ($start_id != '' && $end_id != '') {
            if (strcasecmp($start_id, $end_id) != 0) {
                while (substr_compare($start_id, $end_id, 0, $iterator) == 0) {
                    $iterator++;
                }
            }
            $beginning_id = substr($start_id, 0, $iterator - 1);
            if (!is_numeric($start_id) && !is_numeric($end_id)) {
                //Slice ids starting at the different character
                $start_id = substr($start_id, $iterator - 1); // 857
                $end_id = substr($end_id, $iterator - 1); // 1257
            } else {
                if ($start_id != 0) {
                    // if beginning_id contains a value other than 0*, don't use it
                    if (preg_match('/[^0]/', $beginning_id)) {
                        $beginning_id = '';
                    }
                    $start_id = ltrim($start_id, "0");
                    $end_id = ltrim($end_id, "0");
                }
            }
            while ($start_id <= $end_id) {
                $existing_plant = $this->find('all', ['conditions' => ['plant_id' => $beginning_id . $start_id, 'harvest_batch_id' => $batch->id, 'status IS NOT' => $this->enumValueToKey('status', 'Destroyed')]])->first();
                if (!$existing_plant) {
                    $plantEntry = $this->newEntity();
                    $plantEntry->plant_id = $beginning_id . $start_id;
                    $plantEntry->harvest_batch_id = $batch->id;
                    $plantEntry->recipe_id = $batch->recipe_id;
                    $plantEntry->zone_id = $batch_zone['id'];
                    $plantEntry->cultivar_id = $cultivar_id;
                    array_push($new_plants, $plantEntry);
                }
                $start_id++;
            }
        }

        # If we have a list of IDs, add those as well
        if ($plant_list) {
            $plant_list = explode(",", $plant_list);
            foreach ($plant_list as $plant) {
                $plant = preg_replace('/\s/', '', $plant);
                $existing_plant = $this->find('all', ['conditions' => ['plant_id' => $plant, 'harvest_batch_id' => $batch->id, 'status IS NOT' => $this->enumValueToKey('status', 'Destroyed')]])->first();
                if (!$existing_plant) {
                    $plantEntry = $this->newEntity();
                    $plantEntry->plant_id = $plant;
                    $plantEntry->harvest_batch_id = $batch->id;
                    $plantEntry->recipe_id = $batch->recipe_id;
                    $plantEntry->zone_id = $batch_zone['id'];
                    $plantEntry->cultivar_id = $cultivar_id;
                    array_push($new_plants, $plantEntry);
                }
            }
        }
        if ($batch_zone['id'] != 0) {
            $open_pots = $batch_zone->available_plant_placeholders;
            if ((count($new_plants) > count($open_pots['plant_placeholders'])) && $open_pots['roomHasBenches'] == true) {
                $this->error = "You are trying to add " . count($new_plants) . " plants to " . count($open_pots['plant_placeholders']) . " available plant placeholders in Zone: " . $batch_zone->label . ". Try adding less Plants.";
                return false;
            }
            $tt = 0;
            while ($tt < sizeof($new_plants)) {
                $new_plants[$tt]->status = $this->enumValueToKey('status', 'Planted');
                if (sizeof($open_pots['plant_placeholders']) > 0) {
                    $new_plants[$tt]->map_item_id = $open_pots['plant_placeholders'][$tt]->id;
                    $new_plants[$tt]->zone_id = $open_pots['plant_placeholders'][$tt]->zone_id;
                } else {
                    $new_plants[$tt]->map_item_id = 0;
                    $new_plants[$tt]->zone_id = $batch_zone['id'];
                }
                $tt++;
            }
        }
        $this->saveMany($new_plants);
        $this->updateShortPlantIds($batch_id);
        return true;
    }

    public function updateShortPlantIds($batch_id = null)
    {
        $batchPlants = $this->find('all', ['conditions' => ['harvest_batch_id' => $batch_id, 'status IS NOT' => $this->enumValueToKey('status', 'Destroyed')]]);
        $plantCount = 1;
        foreach ($batchPlants as $plant) {
            $plant->short_plant_id = $plantCount;
            $plantCount++;
        }
        $this->saveMany($batchPlants);
    }

    # Return an array of IDs of map_items that are currently available as plant placeholders
    public function getAvailablePlaceholdersInZone($zone)
    {
        $this->MapItemTypes = TableRegistry::get('MapItemTypes');
        $targetZones = [];

        // if moving the batch to a Room.
        if ($zone->room_zone_id == 0) {
            // first get all bench id's in room
            $roomBenchIds = $this->Zones->find('all', ['conditions' => [
                'room_zone_id' => $zone->id,
                'zone_type_id' => $this->Zones->enumValueToKey('zone_types', 'Group')
            ], 'fields' => ['id']])->toArray();

            foreach ($roomBenchIds as $id) {
                array_push($targetZones, $id->id);
            }

            //if no benches in room, use Room's zone_id
            if ($targetZones == []) {
                array_push($targetZones, $zone->id);
            }
        } else {
            $targetZones = $zone->id;
        }

        //find ALL currPlantsInROOM
        $currPlantsInZone = $this->find('all', [
            'conditions' => [
                'zone_id IN' => $targetZones,
                'map_item_id !=' => 0
            ],
            'fields' => ['map_item_id']
        ])->toArray();
        $plantMapItemIds = [];

        //check against ALL plant place holders in room.
        foreach ($currPlantsInZone as $currPlantInZone) {
            array_push($plantMapItemIds, $currPlantInZone->map_item_id);
        }
        # If we have plants currently in the room, only return empty placeholders.
        if ($currPlantsInZone) {
            $available_plant_placeholders = $this->MapItems->find(
                'all',
                [
                    'conditions' =>
                    ['id NOT IN' => $plantMapItemIds, 'map_item_type_id' => $this->MapItemTypes->find()->select('id')->where(['label' => 'Plant Placeholder']), 'zone_id IN' => $targetZones],
                    'order' => ['zone_id' => 'ASC', 'ordinal' => 'ASC']
                ]
            )->toArray();
            # No plants currently in the room, return all placeholders
        } else {
            $available_plant_placeholders = $this->MapItems->find(
                'all',
                [
                    'conditions' =>
                    ['map_item_type_id' => $this->MapItemTypes->find()->select('id')->where(['label' => 'Plant Placeholder']), 'zone_id IN' => $targetZones],
                    'order' => ['zone_id' => 'ASC', 'ordinal' => 'ASC']
                ]
            )->toArray();
        }

        return $available_plant_placeholders;
    }

    public function movePlantsToZone($plants, $zone)
    {
        $available_plant_placeholders = $this->getAvailablePlaceholdersInZone($zone);

        if (sizeof($plants) > sizeof($available_plant_placeholders)) {
            throw new \Exception('The task could not be marked completed because you are moving ' . sizeof($plants) . ' plants into ' . sizeof($available_plant_placeholders) . ' available pots. (Zone: ' . $zone->label . ')');
        }

        $filteredPlants = array();
        /*
             *  Logic so we can move a batch to the zone it's already in. (Used when moving plants to an existing batch)
             *  Loop here and unset all plants that are already in the zone
             */
        foreach ($plants as $plant) {
            if ($plant->zone_id != null) {
                $plant_zone = $this->Zones->get($plant->zone_id);

                if ($plant_zone->room_zone_id != 0) { // plant is a bench
                    if ($zone->room_zone_id != 0) {
                        if ($plant_zone->room_zone_id == $zone->room_zone_id) {
                            continue;
                        }
                    } else {
                        if ($plant_zone->room_zone_id == $zone->id) {
                            continue;
                        }
                    }
                } else {
                    if ($zone->room_zone_id == 0) {
                        if ($plant_zone->id == $zone->id) {
                            continue;
                        }
                    } else {
                        if ($plant_zone->id == $zone->room_zone_id) {
                            continue;
                        }
                    }
                }
            }

            $filteredPlants[] = $plant;
        }

        # Now let's actually move the plants

        $ii = 0;
        while ($ii < sizeof($filteredPlants)) {
            if (sizeof($available_plant_placeholders) > 0) {
                $filteredPlants[$ii]->map_item_id = $available_plant_placeholders[$ii]->id;
                $filteredPlants[$ii]->zone_id = $available_plant_placeholders[$ii]->zone_id;
            } else {
                $filteredPlants[$ii]->map_item_id = 0;
                $filteredPlants[$ii]->zone_id = $zone->id;
            }
            $filteredPlants[$ii]->status = $this->enumValueToKey('status', 'Planted');
            $ii++;
        }
        $this->saveMany($filteredPlants);
    }
}
