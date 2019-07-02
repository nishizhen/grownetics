<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\I18n\Time;
use Cake\Chronos\Chronos;
use App\Lib\SystemEventRecorder;

/**
 * Tasks Controller
 *
 * @property \App\Model\Table\TasksTable $Tasks
 * @property \App\Model\Table\harvestBatchesTable $harvestBatches
 * @property \App\Model\Table\ZonesTable $Zones
 * @property \App\Model\Table\NotificationsTable $Notifications
 * @property \App\Model\Table\MapItemsTable $MapItems
 * @property \App\Model\Table\PlantsTable $Plants
 * @property \App\Model\Table\MapItemTypesTable $MapItemTypes
 * @property \App\Model\Table\RuleActionsTable $RuleActions
 * @property \App\Model\Table\HarvestBatchesTable $HarvestBatches
 * @property \App\Model\Table\UsersTable $Users
 * @property \App\Model\Table\batchRecipeEntriesTable $batchRecipeEntries
 */
class TasksController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $this->loadModel('harvestBatches');
        $this->loadModel('Zones');
        $this->loadModel('Users');

        $this->paginate = [
            'contain' => ['Harvestbatches']
        ];

        $tasks = $this->paginate($this->Tasks, [
            'conditions' => [
                'harvestbatch_id' => 0,
                'Tasks.zone_id' => 0
            ],
            'order' => ['due_date' => 'ASC']
        ]);
        $batches = $this->harvestBatches->find('all', ['conditions' => ['status !=' => $this->harvestBatches->enumValueToKey('status', 'Harvested')], 'order' => ['planted_date' => 'desc']]);
        $activeZones = [];
        foreach ($batches as $batch) {
            if ($batch->current_room_zone) {
                if (!in_array($batch->current_room_zone, $activeZones)) {
                    array_push($activeZones, $batch->current_room_zone);
                }
            }
        }
        $users = $this->Users->find('all', ['fields' => ['name', 'id']])->toArray();
        $zones = $this->Zones->find('all', ['conditions' => ['zone_type_id IN' => [$this->Zones->enumValueToKey('zone_types', 'Room'), $this->Zones->enumValueToKey('zone_types', 'Group')]]])->toArray();
        $today = Chronos::today();

        $this->set(compact(
            'tasks',
            'batches',
            'activeZones',
            'zones',
            'users',
            'zones',
            'today'
        ));
        $this->set('_serialize', ['tasks', 'batches', 'activeZones', 'zones', 'users', 'zones', 'today']);
    }

    public function archive()
    {
        $this->paginate = [
            'contain' => ['Harvestbatches', 'Users', 'Zones', 'batchRecipeEntries'],
            'conditions' => ['Tasks.status' => $this->Tasks->enumValueToKey('status', 'Completed')],
            'order' => ['completed_date' => 'DESC'],
        ];
        $tasks = $this->paginate($this->Tasks);

        $this->set(compact('tasks'));
    }

    public function markCompleted()
    {
        $this->autoRender = false;
        $this->viewBuilder()->layout('ajax');

        $this->loadModel('Notifications');
        $this->loadModel('MapItems');
        $this->loadModel('Plants');
        $this->loadModel('harvestBatches');
        $this->loadModel('MapItemTypes');
        $this->loadModel('Zones');
        $this->loadModel('RuleActions');
        $this->loadModel('BatchRecipeEntries');
        $task = $this->Tasks->get($this->request->data['task_id']);

        try {
            $task->markCompleted($this->Auth->user('id'), $this->Auth->user('name'));

            $this->Flash->success(__('Task completed!'));
            return $this->redirect(['controller' => 'tasks', 'action' => 'index']);
        } catch (\Exception $e) {
            $this->response->statusCode(500);
            $this->response->body($e->getMessage());
        }
    }

    public function completeNextBatchTask()
    {
        $this->loadModel('Tasks');
        $this->loadModel('HarvestBatches');
        $batch_ids = $this->request->data['batch_ids'];
        foreach ($batch_ids as $batch_id) {
            $batch = $this->HarvestBatches->get($batch_id);
            $task = $this->Tasks->get($batch->next_task->id);
            $success = $this->setAction('markCompleted', $task->id);
            if ($success == false) {
                break;
            }
        }
    }

    public function markUserTaskCompleted($batch_id, $task_id)
    {
        $task = $this->Tasks->get($task_id);
        $task->completed_date = date("c");
        $task->assignee = $this->Auth->user('id');
        $this->Tasks->save($task);
        return $this->redirect(['controller' => 'harvestBatches', 'action' => 'view', $batch_id]);
    }

    public function updateBatchTask()
    {
        $this->loadModel('batch_recipe_entries');
        $this->autoRender = false;
        $this->viewBuilder()->layout('ajax');
        $task = $this->Tasks->get($this->request->data['task_id']);

        //if move/plant/harvest - can't be before/after prevTask/nextTask
        $statusCode = 200;
        if ($task->type == $this->Tasks->enumValueToKey('type', 'Move') || $task->type == $this->Tasks->enumValueToKey('type', 'Harvest')) {
            $remainingTasks = $this->Tasks->find('all', ['conditions' => ['harvestbatch_id' => $task->harvestbatch_id, 'status' => $this->Tasks->enumValueToKey('status', 'Incomplete'), 'type IN' => [$this->Tasks->enumValueToKey('type', 'Harvest'), $this->Tasks->enumValueToKey('type', 'Move')]], 'order' => ['due_date' => 'asc']])->toArray();

            $ind = array_search($task, $remainingTasks);
            $new_date = new Time($this->request->data['due_date']);

            if (isset($remainingTasks[$ind - 1])) {

                if (strtotime($remainingTasks[$ind - 1]['due_date']) >= strtotime($new_date)) {
                    $statusCode = 500;
                    //editedTaskDueDate value before previousTaskDueDate
                }
            }
            if (isset($remainingTasks[$ind + 1])) {
                if (strtotime($remainingTasks[$ind + 1]['due_date']) <= strtotime($new_date)) {
                    $statusCode = 500;
                    //editedTaskDueDate value after nextTaskDueDate
                }
            }
        }

        if ($statusCode == 200) {

            if (in_array($task->type, [$this->Tasks->enumValueToKey('type', 'Harvest'), $this->Tasks->enumValueToKey('type', 'Move')])) {
                $bre = $this->batch_recipe_entries->get($task->batch_recipe_entry_id);
                $bre->planned_start_date = new Time($this->request->data['due_date']);
                $this->batch_recipe_entries->save($bre);
            }

            $task->due_date = new Time($this->request->data['due_date']);
            $task->assignee = $this->request->data['assignee_id'];
            $task->zone_id = $this->request->data['zone_id'];
            $this->Tasks->save($task);
        } else {
            $this->response->statusCode(500);
        }
        $this->set(compact('task'));
        $this->set('_serialize', array('task'));
    }

    public function updateFacilityTask()
    {
        $this->autoRender = false;
        $this->viewBuilder()->layout('ajax');
        $task = $this->Tasks->get($this->request->data['task_id']);
        if ($this->request->data['due_date'] != NULL) {
            $task->due_date = new Time($this->request->data['due_date']);
        }
        $task->assignee = $this->request->data['assignee_id'];
        if ($this->request->data['zone_id'] != NULL) {
            $task->zone_id = $this->request->data['zone_id'];
        }
        $this->Tasks->save($task);
        $this->set(compact('task'));
        $this->set('_serialize', array('task'));
    }

    /**
     * View method
     *
     * @param string|null $id Task id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $task = $this->Tasks->get($id, [
            'contain' => ['Harvestbatches', 'Users']
        ]);

        $this->set('task', $task);
        $this->set('_serialize', ['task']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add($batch_id = null)
    {
        $this->loadModel('Zones');
        $this->loadModel('Users');
        $this->loadModel('HarvestBatches');
        $task = $this->Tasks->newEntity();

        $bre = $this->batchRecipeEntries->newEntity();
        $taskEnums = $this->Tasks->enumValues();
        if ($batch_id == null) {
            unset($taskEnums['types'][2]);
            unset($taskEnums['types'][1]);
        } else {
            $batch = $this->harvestBatches->get($batch_id, ['contain' => ['Cultivars']]);
            unset($taskEnums['types'][2]);
        }

        if ($this->request->is('post')) {
            $status_code = 200;
            $error = 'The task could not be saved.';
            $task = $this->Tasks->patchEntity($task, $this->request->data);
            if ($this->request->data['group_id'] != NULL) {
                $task->zone_id = $this->request->data['group_id'];
            } else if ($this->request->data['room_id']  != NULL) {
                $task->zone_id = $this->request->data['room_id'];
            } else {
                $task->zone_id = 0;
            }
            // Move Tasks need a BRE, zone_id, and due_date
            if ($task->type == $this->Tasks->enumValueToKey('type', 'Move')) {
                if ($task->zone_id == NULL) {
                    $error = 'Move tasks require a Room or Bench to be selected.';
                    $status_code = 500;
                }
                $harvest_task = $this->Tasks->find('all', [
                    'conditions' => [
                        'harvestbatch_id' => $batch_id,
                        'type' => $this->Tasks->enumValueToKey('type', 'Harvest')
                    ]
                ])->first();
                if (strtotime($task->due_date) > strtotime($harvest_task->due_date)) {
                    $error = 'The move task cannot have a due date past the Harvest Batch date: ' . date('M jS, Y', strtotime($harvest_task->due_date)) . '.';
                    $status_code = 500;
                }
                if ($status_code != 500) {
                    $this->loadModel('BatchRecipeEntries');
                    $task_bre = $this->BatchRecipeEntries->newEntity([
                        'recipe_entry_id' => NULL,
                        'planned_start_date' => $task->due_date,
                        'batch_id' => $batch_id,
                        'recipe_id' => NULL,
                        'zone_id' => $task->zone_id
                    ]);
                    $this->BatchRecipeEntries->save($task_bre);
                    $task['batch_recipe_entry_id'] = $task_bre->id;
                }
            }
            if ($status_code != 500) {
                if ($this->Tasks->save($task)) {
                    if ($task->type == $this->Tasks->enumValueToKey('type', 'Move')) {
                        $task_bre['task_id'] = $task->id;
                        $this->BatchRecipeEntries->save($task_bre);
                    }
                    $this->Flash->success(__('The task has been saved.'));
                    if (isset($this->request->params['?']['returnUrl'])) {
                        if ($this->request->params['?']['returnUrl'] == 'Tasks') {
                            return $this->redirect(['controller' => $this->request->params['?']['returnUrl'], 'action' => 'index']);
                        } else {
                            return $this->redirect(['controller' => $this->request->params['?']['returnUrl'], 'action' => 'view', $batch_id]);
                        }
                    } else {
                        return $this->redirect(['controller' => 'Tasks', 'action' => 'index']);
                    }
                }
            } else {
                $this->Flash->error(__($error));
            }
        }
        if (isset($batch_id)) {
            $batch = $this->HarvestBatches->get($batch_id, [
                'contain' => ['Cultivars']
            ]);
        } else {
            $batch = 0;
        }
        $rooms = $this->Zones->find('all', ['conditions' => ['zone_type_id' => $this->Zones->enumValueToKey('zone_types', 'Room'), 'plant_zone_type_id !=' => 0]]);
        $users = $this->Users->find('list', ['limit' => 200]);
        $this->set(compact('task', 'batch', 'users', 'rooms'));
        $this->set('_serialize', ['task']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Task id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null, $batch_id = null, $recipe_id = null, $assignee_id = null)
    {
        if ($assignee_id) {
            $tasks = $this->Tasks->find()->where([
                'harvestbatch_id' => $batch_id,
                'batch_recipe_entry_id' => $recipe_id
            ]);
            foreach ($tasks as $task) {
                $task->assignee = $assignee_id;

                $this->Tasks->save($task);
            }
            return $this->redirect(['controller' => 'harvestBatches', 'action' => 'view', $batch_id]);
        } else {

            $task = $this->Tasks->get($id, [
                'contain' => []
            ]);
            if ($this->request->is(['patch', 'post', 'put'])) {
                $task = $this->Tasks->patchEntity($task, $this->request->data);
                if ($this->Tasks->save($task)) {
                    $this->Flash->success(__('The task has been saved.'));

                    return $this->redirect(['action' => 'index']);
                } else {
                    $this->Flash->error(__('The task could not be saved. Please, try again.'));
                }
            }
            $harvestbatches = $this->Tasks->Harvestbatches->find('list', ['limit' => 200]);
            $assignees = $this->Tasks->Users->find('list', ['limit' => 200]);
            $this->set(compact('task', 'harvestbatches', 'assignees'));
            $this->set('_serialize', ['task']);
        }
    }

    public function get()
    {
        $enums = $this->Tasks->enumValues();
        unset($enums['types'][$this->Tasks->enumValueToKey('type', 'Move')]);
        unset($enums['types'][$this->Tasks->enumValueToKey('type', 'Harvest')]);
        $this->set(compact('enums'));
        $this->set('_serialize', ['enums']);
    }

    public function deleteBatchProcess($task_id = null, $batch_recipe_entry_id = null)
    {
        $this->autoRender = false;
        $this->viewBuilder()->layout('ajax');
        $this->loadModel('batchRecipeEntries');
        $task = $this->Tasks->get($this->request->data['task_id']);
        if (($task->type == $this->Tasks->enumValueToKey('type', 'Move') || $task->type == $this->Tasks->enumValueToKey('type', 'Harvest')) && $task->status != $this->Tasks->enumValueToKey('status', 'Completed')) {
            $bre = $this->batchRecipeEntries->get($task->batch_recipe_entry_id);
            $this->batchRecipeEntries->delete($bre);
        }
        $this->Tasks->delete($task);
        $this->set(compact('task'));
        $this->set('_serialize', ['task']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Task id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null, $batch_id = null)
    {
        $task = $this->Tasks->get($id);
        if ($this->Tasks->delete($task)) {
            $this->Flash->success(__('The task has been deleted.'));
            return $this->redirect(['controller' => 'harvestBatches', 'action' => 'view', $batch_id]);
        } else {
            $this->Flash->error(__('The task could not be deleted. Please, try again.'));
        }

        return $this->redirect(['controller' => 'harvestBatches', 'action' => 'index']);
    }
}
