<?php
namespace App\View\Cell;

use Cake\View\Cell;

/**
 * SmallCalendar cell
 */
class SmallCalendarCell extends Cell
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
        $this->loadModel("Tasks");
        $id = $this->request->session()->read('Auth.User.id');
        $tasks = $this->Tasks->find('all',[ 
            'conditions' => ['due_date !=' => '0000-00-00 00:00:00', 'status' => $this->Tasks->enumValueToKey('status', 'Incomplete')],
            'order' => ['due_date' => 'asc']
            ])->toArray();
        $taskDates = [];
        foreach ($tasks as $task) {
            $taskObject = (object) [
                'date' => $task->due_date->format('Y-m-d'),
                'title' => $task->harvestbatch_id
            ];
            array_push($taskDates, $taskObject);
        }
        $this->set('taskDates', $taskDates);
    }
}
