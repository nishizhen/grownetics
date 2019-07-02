<?php
namespace App\View\Cell;

use Cake\View\Cell;
use Cake\Chronos\Chronos;

/**
 * ZoneWorkflowCard cell
 */
class ZoneWorkflowCardCell extends Cell
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
    public function display($zone_id)
        {
        $this->loadModel('Tasks');
        $this->loadModel('Zones');
        $tasks = $this->Tasks->find('all',
            ['conditions' => [
                'Tasks.status' => $this->Tasks->enumValueToKey('status', 'Incomplete'),
                'Tasks.harvestbatch_id IS NULL',
                'zone_id' => $zone_id
            ],
            'order' => ['Tasks.due_Date' => 'ASC'],
            'contain' => ['Users']
            ]
        )->toArray();
        $completedTasks = $this->Tasks->find('all',
            ['conditions' => [
                'harvestbatch_id IS NULL',
                'Tasks.status' => $this->Tasks->enumValueToKey('status', 'Completed'),
                'zone_id' => $zone_id
            ],
            'order' => ['Tasks.completed_date' => 'DESC'],
            'contain' => ['Users'],
            'limit' => 3
            ]
        );
        foreach($completedTasks as $task) {
            array_push($tasks, $task);
        }
        $today = Chronos::today();
        $zone = $this->Zones->get($zone_id);
        $this->set(compact('tasks', 'zone', 'today'));
        $this->set('_serialize', ['tasks', 'zone', 'today']);  
    }
}
