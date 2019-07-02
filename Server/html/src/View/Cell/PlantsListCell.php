<?php
namespace App\View\Cell;

use Cake\View\Cell;

/**
 * PhotoNoteUploadModal cell
 */
class PlantsListCell extends Cell
{

    /**
     * List of valid options that can be passed into this
     * cell's constructor.
     *
     * @var array
     */
    protected $_validCellOptions = ['modelType'];
    protected $modelType = 'Default';

    /**
     * Initialization logic run at the end of object construction.
     *
     * @return void
     */
    public function initialize()
    {
    }

    public function display($harvest_batch_id) {
        $this->loadModel('Plants');
        $this->loadModel('HarvestBatches');

        $plants = $this->Plants->find('all', [
            'conditions' => ['harvest_batch_id' => $harvest_batch_id],
            'fields' => [
                'zone_id',
                'id',
                'plant_id',
                'short_plant_id',
                'status',
                'harvest_batch_id',
                'Cultivars.label',
                'Cultivars.id'
            ],
            'contain' => ['Notes', 'Cultivars']
        ]);

        $harvestBatches = $this->HarvestBatches->find('all', [
            'contain' => ['Cultivars']
        ])->toArray();

        $this->set(compact('plants','harvest_batch_id','harvestBatches'));
    }
}
