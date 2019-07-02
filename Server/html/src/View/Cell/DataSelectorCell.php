<?php
namespace App\View\Cell;

use Cake\View\Cell;

/**
 * DataSelector cell
 */
class DataSelectorCell extends Cell
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
    public function display_large() {
        $this->loadModel("Sensors");
        $this->loadModel("Zones");
        $this->loadModel("Users");

        $this->set('sensors', $this->Sensors->find('all',[
            'contain' => ['MapItems']
        ]));
        $this->set('zones', $this->Zones->find('all'));
        $id = $this->request->session()->read('Auth.User.id');
        $user = $this->Users->get($id);
        $this->set('showMetric', $user->show_metric);
        $this->set('tasks', ['']);
    }

    public function argus_display_large() {
        $this->loadModel("ArgusParameters");
        $this->loadModel("Users");
        $this->set('argus_parameters', $this->ArgusParameters->find('all'));
        $id = $this->request->session()->read('Auth.User.id');
        $user = $this->Users->get($id);
        $this->set('showMetric', $user->show_metric);
    }

    public function display_small() {
        $this->loadModel("Sensors");
        $this->loadModel("Zones");
        $this->set('sensors', $this->Sensors->find('all')->cache('all_sensors'));
        $this->set('zones', $this->Zones->find('all')->cache('all_zones'));
        $this->set('tasks', ['']);
    }

    public function harvest_batch_view($batch_id = null) {
        $this->loadModel("HarvestBatches");
        $this->loadModel("Tasks");

        $tasks = $this->Tasks->find('all', [
            'conditions' => [
                'harvestbatch_id' => $batch_id,
                'Tasks.type IN' => [$this->Tasks->enumValueToKey('type', 'Move'), $this->Tasks->enumValueToKey('type', 'Harvest')]
            ],
            'contain' => [
                'Zones',
                'HarvestBatches'
            ],
            'fields' => [
                'Zones.plant_zone_type_id',
                'Zones.label',
                'Tasks.completed_date',
                'Tasks.due_date'
            ]
        ])->toArray();
        $kk = 0;
        for ($kk; $kk < count($tasks); $kk++) {
            if ($tasks[$kk]->completed_date == null) {
                $tasks[$kk]['guide_date'] = $tasks[$kk]->due_date;
            } else {
                $tasks[$kk]['guide_date'] = $tasks[$kk]->completed_date;
            }
            if (isset($tasks[$kk+1])) {
                if ($tasks[$kk+1]->completed_date == null) {
                    $tasks[$kk]['guide_toDate'] = $tasks[$kk+1]['due_date'];
                } else {
                    $tasks[$kk]['guide_toDate'] = $tasks[$kk+1]['completed_date'];
                }  
            }
        }

        $this->set('harvestBatches', $this->HarvestBatches->find('all', [
            'conditions' => [
                'status IN' => [
                    $this->HarvestBatches->enumValueToKey('status', 'Active'), 
                    $this->HarvestBatches->enumValueToKey('status', 'Harvested')
                ]
            ],
            'contain' => [
                'Cultivars'
            ],
            'order' => [
                'planted_date' => 'ASC'
            ]
        ]));
        $this->set('batch_id', $batch_id);
        $this->set('tasks', $tasks);
    }
}
