<?php
namespace App\View\Cell;

use Cake\View\Cell;

/**
 * PhotoNoteModalDisplay cell
 */
class PhotoNoteModalDisplayCell extends Cell
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

    /**
     * Default display method.
     *
     * @return void
     */
    public function display($id)
    {
        $model = $this->modelType;
        $this->set(compact('id', 'model'));
    }
}
