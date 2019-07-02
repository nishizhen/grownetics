<?php
namespace App\View\Cell;

use Cake\View\Cell;

/**
 * PhotoNoteDisplay cell
 */
class PhotoNoteDisplayCell extends Cell
{

    /**
     * List of valid options that can be passed into this
     * cell's constructor.
     *
     * @var array
     */
    protected $_validCellOptions = ['modelType', 'limit'];
    protected $modelType = 'Default';
    protected $limit = 0;
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
        $this->loadModel('NotesPlants');
        $path = WWW_ROOT . 'photos' . DS;

        switch ($this->modelType) {
            case 'Plant':
                $notesPlants = $this->NotesPlants->find('all', ['conditions' => [
                    'plant_id' => $id
                ]]);
                foreach ($notesPlants as $entry) {
                    $data[] = $this->Notes->get($entry->note_id, ['contain' => ['Photos', 'Users', 'HarvestBatches' => ['Cultivars'], 'Cultivars', 'Plants', 'Zones']]);
                }
                break;
            case 'Batch':
                $data = $this->Notes->find('all', ['conditions' => [
                    'batch_id' => $id
                ],
                'order' => ['created' => 'desc'],
                'contain' => ['Photos', 'Users', 'HarvestBatches' => ['Cultivars'], 'Cultivars', 'Plants', 'Zones']
            ]);
                break;
            case 'Zone':
                $data = $this->Notes->find('all', ['conditions' => ['zone_id' => $id],
                'order' => ['created' => 'desc'],
                'contain' => ['Photos', 'Users', 'HarvestBatches'  => ['Cultivars'], 'Cultivars', 'Plants', 'Zones']]);
                break;
            case 'Cultivar':
                $data = $this->Notes->find('all', ['conditions' => ['cultivar_id' => $id],
                'order' => ['created' => 'desc'],
                'contain' => ['Photos', 'Users', 'HarvestBatches' => ['Cultivars'], 'Cultivars', 'Plants', 'Zones']]);
                break;
        }

        if ($this->limit > 0) {
            $data = array_slice($data->toArray(), 0, $this->limit);
        }
        $this->set(compact('data', 'path'));
    }
}
