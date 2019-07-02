<?php
namespace App\View\Cell;

use Cake\View\Cell;
use Cake\Chronos\Chronos;

/**
 * WorkflowCard cell
 */
class WorkflowCardCell extends Cell
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
    public function display()
    {
        $this->loadModel('Tasks');

        $tasks = $this->Tasks->find('all',
            ['conditions' => [
                'Tasks.status' => $this->Tasks->enumValueToKey('status', 'Incomplete'),
                'harvestbatch_id IS NULL',
                'zone_id' => 0
            ],
            'order' => ['Tasks.due_Date' => 'ASC'],
            'contain' => ['Users']
            ]
        )->toArray();
        $completedTasks = $this->Tasks->find('all',
            ['conditions' => [
                'harvestbatch_id IS NULL',
                'Tasks.status' => $this->Tasks->enumValueToKey('status', 'Completed'),
                'zone_id' => 0
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
        $this->set(compact('tasks', 'today'));
        $this->set('_serialize', ['tasks', 'today']);  
    }
}
