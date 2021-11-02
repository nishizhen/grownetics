<?php
namespace App\View\Cell;

use Cake\View\Cell;
use Cake\Chronos\Chronos;

/**
 * BatchWorkflowCard cell
 */
class BatchWorkflowCardCell extends Cell
{

    /**
     * List of valid options that can be passed into this
     * cell's constructor.
     *
     * @var array
     */
    protected $_validCellOptions = [];

    /**
     * Default display method.
     *
     * @return void
     */
    public function display($batch_id)
    {
        $this->loadModel('Tasks');
        $this->loadModel('harvestBatches');
        $this->loadModel('Users');
        $this->loadModel('Zones');

        $tasks = $this->Tasks->find('all',
            ['conditions' => [
                'harvestbatch_id' => $batch_id,
                // 'Tasks.status' => $this->Tasks->enumValueToKey('status', 'Incomplete')
            ],
            'order' => ['Tasks.due_Date' => 'ASC'],
            // 'limit' => 5,
            'contain' => ['Users']
            ]
        )->toArray();
        // $completedTasks = $this->Tasks->find('all',
        //     ['conditions' => [
        //         'harvestbatch_id' => $batch_id,
        //         'Tasks.status' => $this->Tasks->enumValueToKey('status', 'Completed')
        //     ],
        //     'order' => ['Tasks.completed_date' => 'DESC'],
        //     'contain' => ['Users']
        //     ]
        // );
        // foreach($completedTasks as $task) {
        //     array_push($tasks, $task);
        // }
        $batch = $this->harvestBatches->get($batch_id, ['contain' => ['Cultivars']]);
        $today = Chronos::today();
        $users = $this->Users->find('all', ['fields' => ['name', 'id']])->toArray();
        $zones = $this->Zones->find('all', ['conditions' => ['zone_type_id IN' => [1, 3], 'plant_zone_type_id IS NOT' => 0]])->toArray();
        $this->set(compact('tasks', 'batch', 'users', 'zones', 'today'));
        $this->set('_serialize', ['tasks', 'users', 'zones', 'today']);  
    }
}
