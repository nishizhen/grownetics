<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\TableRegistry;
use SoftDelete\Model\Table\SoftDeleteTrait;
use App\Lib\DataConverter;
use Cake\I18n\Time;


/**
 * HarvestBatches Model
 *
 * @property \App\Model\Table\CultivarsTable|\Cake\ORM\Association\BelongsTo $Strains
 * @property \App\Model\Table\RecipesTable|\Cake\ORM\Association\BelongsTo $Recipes
 * @property \App\Model\Table\BatchNotesTable|\Cake\ORM\Association\HasMany $BatchNotes
 *
 * @method \App\Model\Entity\HarvestBatch get($primaryKey, $options = [])
 * @method \App\Model\Entity\HarvestBatch newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\HarvestBatch[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\HarvestBatch|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\HarvestBatch patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\HarvestBatch[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\HarvestBatch findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @property \App\Model\Table\batchRecipeEntriesTable|\Cake\ORM\Association\HasMany $batchRecipeEntries
 * @mixin \App\Model\Behavior\EnumBehavior
 * @mixin \App\Model\Behavior\NotifierBehavior
 */
class HarvestBatchesTable extends Table
{
    use SoftDeleteTrait;

    public $enums = [
        'status' => [
            'Pending',
            'Active',
            'Harvested'
        ],
    ];

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('harvest_batches');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Enum');
        $this->addBehavior('Notifier', [
            'notification_level' => 1
        ]);
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
        $this->belongsTo('Cultivars', [
            'foreignKey' => 'cultivar_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Recipes', [
            'foreignKey' => 'recipe_id',
            'joinType' => 'INNER'
        ]);
        $this->hasMany('batchRecipeEntries', [
            'foreignKey' => 'batch_id'
        ]);
        $this->hasMany('Tasks', [
            'foreignKey' => 'harvestbatch_id'
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
            ->notEmpty('recipe_id');
        $validator
            ->notEmpty('cultivar_id');
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
        $rules->add($rules->existsIn(['cultivar_id'], 'Cultivars'));
        $rules->add($rules->existsIn(['recipe_id'], 'Recipes'));

        return $rules;
    }

    public function beforeSave($event, $harvestBatch, $options)
    {
        $this->Sensors = TableRegistry::get('Sensors');
        // for any weight properties on a batch, convert values as needed
        foreach ($harvestBatch->getDirty() as $key) {
            if (strpos($key, 'weight') !== false) {
                if (isset($options['_footprint'])) {
                    $converter = new DataConverter();
                    $harvestBatch[$key] = $converter->convertUnits($harvestBatch[$key], $this->Sensors->enumValueToKey('data_type', 'Weight'), $options['_footprint']['show_metric']);
                }
            }
        }

        if ($harvestBatch->isNew()) {
            $this->recipe_entries = TableRegistry::get('recipe_entries');
            $this->recipes = TableRegistry::get('recipes');
            $this->harvest_batches = TableRegistry::get('harvest_batches');
            $start_id = $harvestBatch->start_id; // 1A400021266EF49000000857
            $end_id =   $harvestBatch->end_id;     // 1A400021266EF49000001257
            $roomCount = 0;
            $zones = [];

            // check that each room is selected and use Room id or Group id for Zones
            if (isset($options['room_ids'])) {
                foreach ($options['room_ids'] as $room_id) {
                    if ($options['group_ids'][$roomCount] == '' || $options['group_ids'][$roomCount] == 0) {
                        array_push($zones, $room_id);
                    } else {
                        array_push($zones, $options['group_ids'][$roomCount]);
                    }
                    if ($zones[$roomCount] == '') {
                        $this->error = __('One or more Room\'s were not selected. Please select a Room for each step of the recipe.');
                        return false;
                    }
                    $roomCount++;
                }
            }
            //check if recipe has entries
            if ($this->recipe_entries->find('all', ['conditions' => ['recipe_id' => $harvestBatch->recipe_id]])->toArray() == null) {
                $empty_recipe = $this->recipes->get($harvestBatch->recipe_id);
                $this->error = __($empty_recipe->label . ' has no recipe entries. Please add recipe entries to ' . $empty_recipe->label . '.');
                return false;
            }
            if ($start_id != '' && $end_id != '') {
                //check ids are strictly numbers or mix of numbers and letters
                if ((is_numeric($start_id) && !(is_numeric($end_id))) || (!(is_numeric($start_id)) && is_numeric($end_id))) {
                    $this->error = __('The ID\'s are of different types. Make sure both ID\'s are either numbers or in a MetRC tag format. (E.g. 1A400021266EF49000001257 or Start ID:1, End ID:49)');
                    return false;
                }
                //check if beginning letters of RFID match
                if (!(is_numeric($start_id)) && !(is_numeric($start_id))) {
                    $no_num_start_id = preg_replace('/[0-9]+/', '', $start_id);
                    $no_num_end_id = preg_replace('/[0-9]+/', '', $end_id);
                    if (strcasecmp($no_num_start_id, $no_num_end_id) != 0) {
                        $this->error = __('One or more letters in the Start ID does not match the letters in the End ID. Ensure that both ID\'s have similiar formats. (e.g. start/end : 1A4EF0100/1A4EF0200');
                        return false;
                    }
                }
            }
            $batchCultivar = $this->Cultivars->get($harvestBatch->cultivar_id);
            $batchCultivar->batch_count += 1;
            $batchCultivar->dontNotify = true;
            $this->Cultivars->save($batchCultivar);
            $harvestBatch->batch_number = $batchCultivar->batch_count;
        }
    }

    public function createNewTask(
        $harvest_batch_id,
        $zone_id,
        $zone_type_id,
        $status,
        $due_date,
        $type,
        $label = null,
        $assignee = null,
        $batch_recipe_entry_id
    ) {
        if ($assignee == null) {
            $this->Roles = TableRegistry::get('Roles');
            $this->Users = TableRegistry::get('Users');

            $growerRoll = $this->Roles->find('all', ['conditions' => ['label' => 'Grower']])->first();
            $grower = $this->Users->find('all', ['conditions' => ['role_id' => $growerRoll->id]])->first();
            $assignee = $grower->id;
        }
        $taskEntry = $this->Tasks->newEntity();
        $taskEntry->batch_recipe_entry_id = $batch_recipe_entry_id;
        $taskEntry->harvestbatch_id = $harvest_batch_id;
        $taskEntry->zone_id =  $zone_id;
        $taskEntry->zone_type_id = $zone_type_id;
        $taskEntry->status = $status;
        $taskEntry->label = $label;
        $taskEntry->assignee = $assignee;
        $taskEntry->due_date = $due_date;
        $taskEntry->type = $type;
        return $this->Tasks->save($taskEntry);
    }

    function sort($first_entry, $second_entry)
    {
        if ($first_entry->plant_zone_type_id == $second_entry->plant_zone_type_id) {
            if ($first_entry->task_type_id && !$second_entry->task_type_id) {
                return 1;
            } else if (!$first_entry->task_type_id && $second_entry->task_type_id) {
                return -1;
            }
            return 0;
        }

        return ($first_entry->plant_zone_type_id < $second_entry->plant_zone_type_id) ? -1 : 1;
    }

    public function calculatePlannedStartDate($daysPerZone, $recipeEntry)
    {
        $plannedEndDate = 0;
        foreach ($daysPerZone as $key => $value) {
            if ($key == $recipeEntry->plant_zone_type_id) {
                $plannedEndDate += $recipeEntry->days;
            } else {
                $plannedEndDate += $value;
            }
        }
        return $plannedEndDate;
    }

    public function afterSave($event, $harvestBatch, $options)
    {
        if ($harvestBatch->isNew()) {
            $RecipeEntries = TableRegistry::get('recipe_entries');
            $BatchRecipeEntries = TableRegistry::get('batch_recipe_entries');
            $Tasks = TableRegistry::get('Tasks');
            $this->Plants = TableRegistry::get('Plants');
            $this->Cultivars = TableRegistry::get('Cultivars');
            $this->Zones = TableRegistry::get('Zones');
            $this->Plants->generatePlantsForBatch($harvestBatch->id, $options['start_id'], $options['end_id'], $options['plant_list'], $harvestBatch->cultivar_id);

            $roomCount = 0;
            $zones = [];

            //Seperate Rooms and Groups for Batch Zones
            foreach ($options['room_ids'] as $room_id) {
                if ($options['group_ids'][$roomCount] == '' || $options['group_ids'][$roomCount] == 0) {
                    array_push($zones, $room_id);
                } else {
                    array_push($zones, $options['group_ids'][$roomCount]);
                }
                $roomCount++;
            }
            $recipeEntries = $this->recipe_entries->find(
                'all',
                [
                    'conditions' => [
                        'recipe_id' => $harvestBatch->recipe_id
                    ]
                ]
            )->toArray();

            // assign a zone to each entry
            $parentEntryZones = [];
            $zoneEntries = [];
            $subTaskEntries = [];

            foreach ($recipeEntries as $entry) {
                if ($entry->parent_recipe_entry_id == null) {
                    $zoneEntries[] = $entry;
                } else {
                    $subTaskEntries[] = $entry;
                }
            }
            $sortedEntries = array_merge($zoneEntries, $subTaskEntries);
            foreach ($sortedEntries as $index => $entry) {
                if ($entry->parent_recipe_entry_id == null) {
                    $entry['zone_id'] = $zones[$index];
                    $parentEntryZones[$entry->id] = $zones[$index];
                } else {
                    $entry['zone_id'] = $parentEntryZones[$entry->parent_recipe_entry_id];
                }
            }
            $parentRecipeEntries = [];
            foreach ($recipeEntries as $ind => $recipeEntry) {

                if ($recipeEntry->task_type_id) {
                    // sub task
                    $planned_start_date = $harvestBatch->planted_date->addDay($recipeEntry->days);
                    $subBatchRecipeEntry = $BatchRecipeEntries->newEntity([
                        'recipe_entry_id'    => $recipeEntry->id,
                        'planned_start_date' => $planned_start_date,
                        'planned_end_date'   => $planned_start_date->addDay($recipeEntry->days),
                        'batch_id'           => $harvestBatch->id,
                        'recipe_id'          => $harvestBatch->recipe_id,
                        'zone_id'            => $recipeEntry['zone_id'],
                        'task_id'            => NULL
                    ]);
                    $BatchRecipeEntries->save($subBatchRecipeEntry);

                    $subTask = $Tasks->newEntity([
                        'label'                 => $recipeEntry->task_label,
                        'status'                => $Tasks->enumValueToKey('status', 'Incomplete'),
                        'harvestbatch_id'       => $harvestBatch->id,
                        'due_date'              => $planned_start_date,
                        'batch_recipe_entry_id' => $subBatchRecipeEntry->id,
                        'zone_id'               => $recipeEntry['zone_id'],
                        'type'                  => $recipeEntry->task_type_id,
                        'assignee'              => $options['userId']
                    ]);
                    $Tasks->save($subTask);
                    $subBatchRecipeEntry->task_id = $subTask->id;
                    $BatchRecipeEntries->save($subBatchRecipeEntry);
                } else {

                    if ($ind == 0) {
                        // initial Move plants task
                        $planned_start_date = $harvestBatch->planted_date;
                        $planned_end_date = $harvestBatch->planted_date->addDay($recipeEntry->days);
                    } else {
                        // move to Zone task
                        $recentMoveTask = $Tasks->find('all', [
                            'conditions' => [
                                'type' => $Tasks->enumValueToKey('type', 'Move'),
                                'harvestbatch_id' => $harvestBatch->id,
                            ],
                            'order' => [
                                'due_date' => 'desc'
                            ]
                        ])->first();
                        $recentBre = $BatchRecipeEntries->findByTaskId($recentMoveTask->id)->first();
                        $planned_start_date = $recentBre->planned_end_date;
                        $planned_end_date = $recentBre->planned_end_date->addDay($recipeEntry->days);
                    }
                    // move bre
                    $moveBre = $BatchRecipeEntries->newEntity([
                        'recipe_entry_id'    => $recipeEntry->id,
                        'planned_start_date' => $planned_start_date,
                        'planned_end_date'   => $planned_end_date,
                        'batch_id'           => $harvestBatch->id,
                        'recipe_id'          => $harvestBatch->recipe_id,
                        'zone_id'            => $recipeEntry['zone_id'],
                        'task_id'            => NULL,
                        'days'               => $recipeEntry->days
                    ]);
                    $BatchRecipeEntries->save($moveBre);

                    // move task
                    $moveTask = $Tasks->newEntity([
                        'label'                 => 'Move batch to',
                        'status'                => $Tasks->enumValueToKey('status', 'Incomplete'),
                        'harvestbatch_id'       => $harvestBatch->id,
                        'due_date'              => $planned_start_date,
                        'batch_recipe_entry_id' => $moveBre->id,
                        'zone_id'               => $recipeEntry['zone_id'],
                        'type'                  => $Tasks->enumValueToKey('type', 'Move'),
                        'assignee'              => $options['userId']
                    ]);
                    $Tasks->save($moveTask);
                    $moveBre->task_id = $moveTask->id;
                    $BatchRecipeEntries->save($moveBre);

                    array_push($parentRecipeEntries, $recipeEntry);
                }
            }

            $lastMoveTask = $Tasks->find('all', [
                'conditions' => [
                    'type' => $Tasks->enumValueToKey('type', 'Move'),
                    'harvestbatch_id' => $harvestBatch->id,
                ],
                'order' => [
                    'due_date' => 'desc'
                ]
            ])->first();

            // final Harvest Task/BRE
            $harvestBre = $BatchRecipeEntries->newEntity([
                'recipe_entry_id'    => end($parentRecipeEntries)->id,
                'planned_start_date' => $lastMoveTask->due_date->addDay(end($parentRecipeEntries)->days),
                'planned_end_date'   => $lastMoveTask->due_date->addDay(end($parentRecipeEntries)->days),
                'batch_id'           => $harvestBatch->id,
                'recipe_id'          => $harvestBatch->recipe_id,
                'zone_id'            => end($parentRecipeEntries)['zone_id'],
                'task_id'            => NULL,
                'days'               => end($parentRecipeEntries)->days
            ]);
            $BatchRecipeEntries->save($harvestBre);

            $harvestTask = $Tasks->newEntity([
                'label'                 => 'Harvest batch from',
                'status'                => $Tasks->enumValueToKey('status', 'Incomplete'),
                'harvestbatch_id'       => $harvestBatch->id,
                'due_date'              => $lastMoveTask->due_date->addDay(end($parentRecipeEntries)->days),
                'batch_recipe_entry_id' => $harvestBre->id,
                'zone_id'               => end($parentRecipeEntries)['zone_id'],
                'type'                  => $Tasks->enumValueToKey('type', 'Harvest'),
                'assignee'              => $options['userId']
            ]);
            $Tasks->save($harvestTask);
            $harvestBre->task_id = $harvestTask->id;
            $BatchRecipeEntries->save($harvestBre);

            return true;
        }
    }

    public function beforeDelete($event, $harvestBatch, $options)
    {

        $this->Cultivars = TableRegistry::get('Cultivars');
        $batchCultivar = $this->Cultivars->get($harvestBatch->cultivar_id);
        $batchCultivar->batch_count = $batchCultivar->batch_count - 1;
        $this->Cultivars->save($batchCultivar);

        $this->batchRecipeEntries = TableRegistry::get('batch_recipe_entries');
        $batchRecipeEntries = $this->batchRecipeEntries->find(
            'all',
            [
                'conditions' => ['batch_id' => $harvestBatch->id]
            ]
        );
        foreach ($batchRecipeEntries as $batchRecipeEntry) {
            $this->batchRecipeEntries->delete($batchRecipeEntry);
        }

        $this->Tasks = TableRegistry::get('Tasks');
        $batchTasks = $this->Tasks->find(
            'all',
            [
                'conditions' => ['harvestbatch_id' => $harvestBatch->id]
            ]
        );
        foreach ($batchTasks as $task) {
            $this->Tasks->delete($task);
        }

        $this->Plants = TableRegistry::get('Plants');
        $batchPlants = $this->Plants->find(
            'all',
            [
                'conditions' => ['harvest_batch_id' => $harvestBatch->id]
            ]
        );
        foreach ($batchPlants as $plant) {
            $this->Plants->delete($plant);
        }
        return true;
    }

    public function recalculateMoveDates($batch)
    {
        $Tasks = TableRegistry::get('Tasks');
        $BatchRecipeEntries = TableRegistry::get('BatchRecipeEntries');
        $batch_tasks = $Tasks->find('all', [
            'conditions' => [
                'harvestbatch_id' => $batch->id,
                'type IN' => [$Tasks->enumValueToKey('type', 'Move'), $Tasks->enumValueToKey('type', 'Harvest')]
            ],
            'order' => ['batch_recipe_entry_id' => 'asc'],
            'contain' => ['BatchRecipeEntries']
        ])->toArray();
        for ($ii = 0; $ii < sizeof($batch_tasks); $ii++) {
            if (isset($batch_tasks[$ii + 1])) {
                if ($batch_tasks[$ii]->status == $Tasks->enumValueToKey('status', 'Completed')) {
                    $task_bre = $BatchRecipeEntries->get($batch_tasks[$ii]->batch_recipe_entry_id);
                    $days = '+' . $task_bre->days . ' day';
                    $batch_tasks[$ii + 1]->due_date = new Time(date('Y-m-d H:i:s', strtotime($days, strtotime($batch_tasks[$ii]->completed_date))));
                } else {
                    $task_bre = $BatchRecipeEntries->get($batch_tasks[$ii]->batch_recipe_entry_id);
                    $days = '+' . $task_bre->days . ' day';
                    $batch_tasks[$ii + 1]->due_date = new Time(date('Y-m-d H:i:s', strtotime($days, strtotime($batch_tasks[$ii]->due_date))));
                }
            }
        }
        $Tasks->saveMany($batch_tasks);
    }

    public function cloneBatchRecipeEntriesToNewBatch($clone_too_batch_id, $clone_from_bach_id)
    {
        $batch_recipe_entries = $this->BatchRecipeEntries->find('all', ['conditions' => [
            'batch_id' => $this->request->data['batch_id']
        ]]);
        foreach ($batch_recipe_entries as $bre) {
            $newEntry = $this->BatchRecipeEntries->newEntity([
                'recipe_entry_id' => $bre->recipe_entry_id,
                'planned_start_date' => $bre->planned_start_date,
                'planned_end_date' => $bre->planned_end_date,
                'batch_id' => $clone_too_batch_id,
                'recipe_id' => $bre->recipe_id,
                'zone_id' => $bre->zone_id,
                'task_id' => $bre->task_id
            ]);
            $this->BatchRecipeEntries->save($newEntry);
        }
    }
}
