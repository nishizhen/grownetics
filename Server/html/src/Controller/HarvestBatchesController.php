<?php

namespace App\Controller;

use App\Controller\AppController;
use App\Lib\DataConverter;

/**
 * HarvestBatches Controller
 *
 * @property \App\Model\Table\HarvestBatchesTable $HarvestBatches
 * @property \App\Model\Table\ZonesTable $Zones
 * @property \App\Model\Table\CultivarsTable $Cultivars
 * @property \App\Model\Table\BatchRecipeEntriesTable $BatchRecipeEntries
 * @property \App\Model\Table\NotificationsTable $Notifications
 * @property \App\Model\Table\BatchNotesTable $BatchNotes
 * @property \App\Model\Table\PlantsTable $Plants
 * @property \App\Model\Table\TasksTable $Tasks
 */
class HarvestBatchesController extends AppController
{

  // public $paginate = [
  //   'order' => [
  //     'HarvestBatches.planted_date' => 'asc'
  //   ]
  // ];

  /**
   * Index method
   *
   * @return \Cake\Network\Response|null
   */
  public function index()
  {
    $this->loadModel('Tasks');
    $this->loadModel('Zones');
    $this->loadModel('BatchRecipeEntries');

    $harvestBatches = $this->HarvestBatches->find('all', [
      'conditions' => [
        'status !=' => $this->HarvestBatches->enumValueToKey('status', 'Harvested'),
      ],
      'contain' => [
        'batchRecipeEntries' => [
          'sort' => [
            'batchRecipeEntries.id' => 'asc'
          ]
        ],
        'Cultivars',
        'Tasks' => [
          'conditions' => [
            'Tasks.type in' => [
              $this->Tasks->enumValueToKey('type', 'Move'),
              $this->Tasks->enumValueToKey('type', 'Harvest')
            ]
          ],
          'sort' => [
            'Tasks.due_date' => 'asc'
          ],
          'Zones'
        ]
      ]
    ]);
    $batches = [];
    $allZones = [];
    $batchesByRoom = [];
    $plantZoneTypeColors = ['#90DDF0', '#cbf7a5', '#f77e42', '#B3CAD4', '#A1674A', '#808782', '#AA3939', '#3C5A14'];

    foreach ($harvestBatches as $batch) {
      $cultivarLabel = 'Cultivar: ' . $batch->cultivar->label;
      $plantCount = $batch->plant_count;
      $batchZones = [];
      $batchObject = (object)["category" => $batch->id, "batch_no" => $batch->batch_number, "cultivar" => $cultivarLabel, "url" => "/HarvestBatches" . "/view/" . $batch->id, "segments" => []];
      for ($ii = 0; $ii < count($batch->tasks); $ii++) {
        if ($batch->tasks[$ii]->status == $this->Tasks->enumValueToKey('status', 'Completed')) {
          $planned_start_date = $batch->tasks[$ii]->completed_date;
        } else {
          $planned_start_date = $batch->tasks[$ii]->due_date;
        }
        if (isset($batch->tasks[$ii + 1])) {
          if ($batch->tasks[$ii + 1]->status == $this->Tasks->enumValueToKey('status', 'Completed')) {
            $planned_end_date = $batch->tasks[$ii + 1]->completed_date;
          } else {
            $planned_end_date = $batch->tasks[$ii + 1]->due_date;
          }
        } else {
          if ($batch->tasks[$ii]->status == $this->Tasks->enumValueToKey('status', 'Completed')) {
            $planned_end_date = $batch->tasks[$ii]->completed_date;
          } else {
            $planned_end_date = $batch->tasks[$ii]->due_date;
          }
        }

        $color = $plantZoneTypeColors[$batch->tasks[$ii]->zone->plant_zone_type_id - 1];
        array_push($batchObject->segments, (object)[
          "start" => $planned_start_date,
          "end" => $planned_end_date,
          "color" => $color,
          "plantCount" => $plantCount,
          "task" => $this->Zones->enums['plant_zone_types'][$batch->tasks[$ii]->zone->plant_zone_type_id],
          "duration" => $planned_end_date->diff($planned_start_date)->days,
          "url" => "Zones/view/" . $batch->tasks[$ii]->zone_id
        ]);
        array_push($batchZones, $batch->tasks[$ii]->zone);
      }
      foreach ($batchZones as $zone) {
        if ($zone->room_zone_id) {
          continue;
        }
        $batchesByRoom[$zone->label][] = $batchObject;
        if (!in_array($zone, $allZones)) {
          array_push($allZones, $zone);
        }
      }
      array_push($batches, $batchObject);
    }
    array_multisort(
      array_column($allZones, 'plant_zone_type_id'),
      SORT_ASC,
      array_column($allZones, 'label'),
      SORT_ASC,
      $allZones
    );

    $this->paginate($harvestBatches);
    $this->set(compact('harvestBatches', 'batches', 'batchesByRoom', 'allZones'));
    $this->set('_serialize', ['harvestBatches', 'batches', 'batchesByRoom', 'allZones']);
  }

  public function archive()
  {
    $this->paginate = [
      'contain' => ['Cultivars', 'Recipes'],
      'conditions' => ['status' => $this->HarvestBatches->enumValueToKey('status', 'Harvested')]
    ];
    $harvestBatches = $this->paginate($this->HarvestBatches);

    $this->set(compact('harvestBatches'));
    $this->set('_serialize', ['harvestBatches']);
  }

  /**
   * View method
   *
   * @param string|null $id Harvest Batch id.
   * @return \Cake\Network\Response|null
   * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
   */
  public function view($id = null)
  {
    $this->loadModel('BatchRecipeEntries');
    $this->loadModel('Notifications');
    $this->loadModel('Recipes');
    $this->loadModel('BatchNotes');
    $this->loadModel('Plants');
    $this->loadModel('Tasks');
    $this->loadModel('Zones');
    $this->loadModel('Sensors');

    $plant = $this->Plants->newEntity();

    $this->set('bodyClass', 'batches view');

    $this->paginate = [
      'conditions' => ['batch_id' => $id],
      'contain' => ['Zones']
    ];


    $batchRecipeEntries = $this->paginate($this->BatchRecipeEntries);

    $this->set(compact('batchRecipeEntries'));
    $this->set('_serialize', ['batchRecipeEntries']);

    $notifications = $this->Notifications->find('all', [
      'conditions' => [
        'source_type' => $this->Notifications->enumValueToKey('source_type', 'HarvestBatch'),
        'source_id' => $id
      ],
      'contain' => ['Users'],
      'order' => 'Notifications.id desc'
    ])->toArray();

    $activity = array_merge($notifications);
    foreach ($activity as $key => $row) {
      $created[$key]  = $row->created;
    }
    if (isset($created)) {
      array_multisort($created, SORT_DESC, $activity);
    } else {
      $activity = [];
    }
    $this->set('activity', $activity);


    $harvestBatch = $this->HarvestBatches->get($id, [
      'contain' => ['Cultivars', 'Recipes']
    ]);

    $show_metric = $this->getRequest()->getSession()->read('Auth.User.show_metric');
    if ($show_metric == true) {
      $weightUnit = 'kg';
    } else {
      $weightUnit = 'lbs';
    }

    $converter = new DataConverter();
    $harvestBatch->dry_whole_weight = $converter->displayUnits(
      $harvestBatch->dry_whole_weight,
      $this->Sensors->enumValueToKey('data_type', 'Weight'),
      $show_metric
    );
    $harvestBatch->dry_waste_weight = $converter->displayUnits(
      $harvestBatch->dry_waste_weight,
      $this->Sensors->enumValueToKey('data_type', 'Weight'),
      $show_metric
    );
    $harvestBatch->dry_whole_trimmed_weight = $converter->displayUnits(
      $harvestBatch->dry_whole_trimmed_weight,
      $this->Sensors->enumValueToKey('data_type', 'Weight'),
      $show_metric
    );

    $cultivars = $this->HarvestBatches->Cultivars->find('list', ['limit' => 200]);
    $recipes = $this->HarvestBatches->Recipes->find('list', ['limit' => 200]);
    $batchRecipe = $this->Recipes->get($harvestBatch->recipe->id, [
      'contain' => ['RecipeEntries']
    ]);
    foreach ($batchRecipe->recipe_entries as $entry) {
      if (isset($entry->task_type_id)) {
        continue;
      }
      $room_zone = $this->Zones->find('all', [
        'conditions' => [
          'zone_type_id =' => $this->Zones->enumValueToKey('zone_types', 'Room'),
          'plant_zone_type_id !=' => 0,
          'plant_zone_type_id =' => $entry->plant_zone_type_id
        ]
      ]);
      $entry->room_data = $room_zone->toArray();
    }

    $this->set(compact('weightUnit', 'harvestBatch', 'cultivars', 'recipes', 'plants', 'plant', 'batchRecipe'));
    $this->set('_serialize', ['harvestBatch', 'plant', 'batchRecipe']);
  }

  /*
     * Updates the batch_id on plants being moved.  Delegates to PlantEntity
     *
     */
  public function updateOldBatchPlantList($new_batch_id, $plant_list, $current_batch_id)
  {
    $this->loadModel('Plants');
    $batch = $this->HarvestBatches->get($current_batch_id);
    $this->Plants->find('all', ['conditions' =>
    ['harvest_batch_id' => $batch->id]]);

    if (!is_array($plant_list)) {
      $plant_list = explode(",", $plant_list);
    }

    foreach ($plant_list as $plant_id) {
      $plant = $this->Plants->get($plant_id);
      if (!$plant->changeBatchId($new_batch_id)) {
        return false;
      }
    }
    return true;
  }

  /*
     * Move selected plants to an already existing batch
     */
  public function movePlantsToExistingBatch()
  {
    $this->loadModel('Tasks');
    $this->loadModel('BatchRecipeEntries');

    $move_task = $this->Tasks->find('all', [
      'conditions' => [
        'harvestbatch_id' => $this->request->data['newBatchId'],
        'type' => $this->Tasks->enumValueToKey('type', 'Move')
      ],
      'order' => ['due_date' => 'desc']

    ])->first();

    $new_move_task = $this->Tasks->newEntity();
    $new_move_task = $this->Tasks->patchEntity($new_move_task, $move_task->toArray());

    $new_move_task->message = 'Moving individual plants from batch #' . $this->request->data['currentBatchId'] . ' to Batch #' . $this->request->data['newBatchId'];
    $new_move_task->harvestbatch_id = $this->request->data['newBatchId'];

    if ($this->Tasks->save($new_move_task)) {
      $this->updateOldBatchPlantList($this->request->data['newBatchId'], $this->request->data['plants'], $this->request->data['currentBatchId']);

      $data = $new_move_task->markCompleted($this->Auth->user('id'), $this->Auth->user('name'));

      if ($data['responseCode'] == 200) {
        $this->Flash->success(__('The plants have been successfully moved'));
        return $this->redirect(['action' => 'view', $this->request->data['newBatchId']]);
      } else {
        $this->updateOldBatchPlantList($this->request->data['currentBatchId'], $this->request->data['plants'], $this->request->data['newBatchId']);
        $this->Tasks->delete($new_move_task);
        $this->Flash->error(__($data['responseBody']));
        return $this->redirect(['action' => 'view', $this->request->data['currentBatchId']]);
      }
    } else {
      $this->Flash->error(__("Error moving these plants, please try again"));
      return $this->redirect(['action' => 'view', $this->request->data['currentBatchId']]);
    }
  }

  /**
   *  MovePlants Method. Creates a new batch
   *
   */
  public function movePlantsToNewBatch()
  {
    $this->disableAutoRender();
    $harvestBatch = $this->HarvestBatches->newEntity();

    if ($this->request->is('post') && !isset($this->request->data['newBatch'])) {
      if (isset($this->request->data['isNewBatch'])) {
        $plant_list = $this->request->data['plants'];
        $batch = $this->HarvestBatches->get($this->request->data['batch_id']);
        $harvestBatch = $this->HarvestBatches->patchEntity($harvestBatch, $batch->toArray());
        $options = $batch->OptionsArray;
        $options['userId'] = $this->Auth->user('id');
        $harvestBatch = $this->HarvestBatches->patchEntity($harvestBatch, $options);

        if ($this->HarvestBatches->save($harvestBatch, $options)) {
          if ($this->updateOldBatchPlantList($harvestBatch->id, $plant_list, $this->request->data['batch_id'])) {
            $batch->UpdatedTasksForNewBatch = $harvestBatch->id;
            $this->Flash->success(__('Plants have been successfully moved to new batch'));
            return $this->redirect(['action' => 'view', $harvestBatch->id]);
          } else {
            $this->Flash->error(__('The harvest batch saved but plants could not be added to batch'));
            return $this->redirect(['action' => 'view', $harvestBatch->id]);
          }
        }
      }
    }
  }

  /**
   * Add method
   *
   * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
   */
  public function add()
  {
    $this->loadModel('Notifications');
    $this->loadModel('Recipes');
    $this->loadModel('RecipeEntries');
    $this->loadModel('Zones');

    $userId = $this->Auth->user('id');
    $harvestBatch = $this->HarvestBatches->newEntity();
    $harvestBatch->notifier_source_type = $this->Notifications->enumValueToKey('source_type', 'HarvestBatch');
    if ($this->request->is('post')) {
      $options = [];
      foreach ($this->request->data as $key => $value) {
        // Find out how to reference all properties on the HarvestBatches table and use this
        // If to check that $key IS NOT in that list of properties
        if ($key == 'room_ids' || $key == 'group_ids' || $key == 'plant_list') {
          $options[$key] = $value;
          unset($this->request->data[$key]);
        }
      }
      $harvestBatch = $this->HarvestBatches->patchEntity($harvestBatch, $this->request->data);

      $options['userId'] = $userId;
      $options['start_id'] = $this->request->data['start_id'];
      $options['end_id'] = $this->request->data['end_id'];

      if ($this->HarvestBatches->save($harvestBatch, $options)) {
        $this->Flash->success(__('The harvest batch has been saved.'));
        return $this->redirect(['action' => 'view', $harvestBatch->id]);
      } else {
        $this->Flash->error($this->HarvestBatches->error);
      }
    }
    $this->loadModel('Notifications');
    $this->loadModel('Recipes');
    $this->loadModel('RecipeEntries');
    $this->loadModel('Zones');

    $userId = $this->Auth->user('id');
    $harvestBatch = $this->HarvestBatches->newEntity();
    $harvestBatch->notifier_source_type = $this->Notifications->enumValueToKey('source_type', 'HarvestBatch');
    if ($this->request->is('post')) {
      $options = [];
      foreach ($this->request->data as $key => $value) {
        // Find out how to reference all properties on the HarvestBatches table and use this
        // If to check that $key IS NOT in that list of properties
        if ($key == 'room_ids' || $key == 'group_ids' || $key == 'plant_list') {
          $options[$key] = $value;
          unset($this->request->data[$key]);
        }
      }
      $harvestBatch = $this->HarvestBatches->patchEntity($harvestBatch, $this->request->data);
      $options['userId'] = $userId;
      $options['start_id'] = $this->request->data['start_id'];
      $options['end_id'] = $this->request->data['end_id'];
      if ($this->HarvestBatches->save($harvestBatch, $options)) {
        $this->Flash->success(__('The harvest batch has been saved.'));
        return $this->redirect(['action' => 'view', $harvestBatch->id]);
      } else {
        $this->Flash->error($this->HarvestBatches->error);
      }
    }
    $cultivars = $this->HarvestBatches->Cultivars->find('list');
    if (sizeof($cultivars->toArray()) == 0) {
      $this->Flash->error(__("Please create a cultivar first. Example: Cherry Tomatoes"));
      return $this->redirect(
        ['controller' => 'Cultivars', 'action' => 'add']
      );
    }
    $recipes = $this->HarvestBatches->Recipes->find('list');
    $zones = $this->Zones->find('all');
    $Recipe = $this->Recipes->find('all')->first();
    if (is_null($Recipe)) {
      $this->Flash->error(__('Please create a recipe before creating a harvest batch.'));
      return $this->redirect(
        ['controller' => 'Recipes', 'action' => 'add']
      );
    }

    $entries = $this->RecipeEntries->find('all', ['conditions' => ['recipe_id' => $Recipe->id], [['conditions' => ['Zones.zone_type_id' => $this->Zones->enumValueToKey('zone_types', 'Room'), 'Zones.plant_zone_type_id IS NOT' => 0]]]]);
    $recipeEntries = [];
    foreach ($entries as $entry) {
      if (isset($entry->task_type_id)) {
        continue;
      }
      $room_zone = $this->Zones->find('all', [
        'conditions' => [
          'zone_type_id =' => $this->Zones->enumValueToKey('zone_types', 'Room'),
          'plant_zone_type_id !=' => 0,
          'plant_zone_type_id =' => $entry->plant_zone_type_id
        ]
      ]);
      $entry->room_data = $room_zone->toArray();
      $recipeEntries[] = $entry;
    }

    $this->set(compact('harvestBatch', 'cultivars', 'recipes', 'recipeEntries', 'zones'));
    $this->set('_serialize', ['harvestBatch']);
  }

  /**
   * Edit method
   *
   * @param string|null $id Harvest Batch id.
   * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
   * @throws \Cake\Network\Exception\NotFoundException When record not found.
   */
  public function edit($id = null)
  {
    $harvestBatch = $this->HarvestBatches->get($id, [
      'contain' => []
    ]);
    if ($this->request->is(['patch', 'post', 'put'])) {
      $harvestBatch = $this->HarvestBatches->patchEntity($harvestBatch, $this->request->data);
      if ($this->HarvestBatches->save($harvestBatch)) {
        if ($this->request->is('ajax')) {

          $this->render(false);
          return;
        } else {
          $this->Flash->success(__('The harvest batch has been saved.'));
          return $this->redirect(['action' => 'view', $harvestBatch->id]);
        }
      } else {
        $this->Flash->error(__('The harvest batch could not be saved. Please, try again.'));
        return $this->redirect(['action' => 'view', $harvestBatch->id]);
      }
    }
    $cultivars = $this->HarvestBatches->Cultivars->find('list', ['limit' => 200]);
    $recipes = $this->HarvestBatches->Recipes->find('list', ['limit' => 200]);
    $this->set(compact('harvestBatch', 'cultivars', 'recipes'));
    $this->set('_serialize', ['harvestBatch']);
  }

  /**
   * Delete method
   *
   * @param string|null $id Harvest Batch id.
   * @return \Cake\Network\Response|null Redirects to index.
   * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
   */
  public function delete($id = null)
  {
    $this->request->allowMethod(['post', 'delete']);
    $harvestBatch = $this->HarvestBatches->get($id);
    if ($this->HarvestBatches->delete($harvestBatch)) {
      $this->Flash->success(__('The harvest batch has been deleted.'));
    } else {
      $this->Flash->error(__('The harvest batch could not be deleted. Please, try again.'));
    }

    return $this->redirect(['action' => 'index']);
  }

  public function markPlantDestroyed($plant_id, $batch_id)
  {
    $this->loadModel('Plants');
    $plant = $this->Plants->get($plant_id);
    $plant->markPlantDestroyed($plant, $this->Auth->user('id'), $batch_id);
    return $this->redirect(['action' => 'view', $batch_id]);
  }
}
