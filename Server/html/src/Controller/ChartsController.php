<?php
/**
 * Analytics Controller
 *
 * @property Batch $Batch
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
namespace App\Controller;

class ChartsController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator', 'RequestHandler');


	public function index() {
	    return $this->redirect(['action'=>'view']);
    }

	public function view() {

	}

	public function grafana() {

    }

	public function argus() {

    }

   	public function harvestBatchView($batch_id = null) {
   		$this->set('batch_id', $batch_id);
	}
}
