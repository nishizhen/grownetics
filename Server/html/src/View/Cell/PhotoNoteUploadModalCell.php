<?php
namespace App\View\Cell;

use Cake\View\Cell;

/**
 * PhotoNoteUploadModal cell
 */
class PhotoNoteUploadModalCell extends Cell
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

    public function display($id) {
        $this->loadModel('Notes');
        $plant_id = $batch_id = $cultivar_id = $zone_id = null;

        switch ($this->modelType) {
            case 'Plant':
                $plant_id = $id;
                break;
            case 'Batch':
                $batch_id = $id;
                break;
            case 'Zone':
                $zone_id = $id;
                break;
            case 'Cultivar':
                $cultivar_id = $id;
                break;
        }

        $note = $this->Notes->newEntity();
        $model = $this->modelType;
        $this->set(compact('note', 'cultivar_id', 'batch_id', 'zone_id', 'plant_id', 'model', 'id'));
    }
}
