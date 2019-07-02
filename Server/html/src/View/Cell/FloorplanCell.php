<?php
namespace App\View\Cell;

use Cake\View\Cell;

/**
 * Floorplan cell
 */
class FloorplanCell extends Cell
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
    public function display($id)
    {
        $this->loadModel("Floorplans");
        $this->loadModel("MapItemTypes");

        $this->floorplan = $this->Floorplans->get($id);

        $mapItemTypes = $this->MapItemTypes->find('all');
        
        $this->set('mapItemTypes', $mapItemTypes);
        $this->set('floorplan', $this->floorplan);
    }
}
