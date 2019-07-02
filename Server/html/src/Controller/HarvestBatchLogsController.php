<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * HarvestBatchLogs Controller
 *
 * @property \App\Model\Table\HarvestBatchLogsTable $HarvestBatchLogs
 */
class HarvestBatchLogsController extends AppController
{

    public function initialize()
    {
        parent::initialize();
        $this->loadModel('HarvestBatchLogs');
    }
    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $harvestBatchLogs = $this->paginate($this->HarvestBatchLogs);

        $this->set(compact('harvestBatchLogs'));
        $this->set('_serialize', ['harvestBatchLogs']);
    }

    /**
     * View method
     *
     * @param string|null $id Harvest Batch Log id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $harvestBatchLog = $this->HarvestBatchLogs->get($id, [
            'contain' => ['Zones', 'Batches']
        ]);

        $this->set('harvestBatchLog', $harvestBatchLog);
        $this->set('_serialize', ['harvestBatchLog']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $harvestBatchLog = $this->HarvestBatchLogs->newEntity();
        if ($this->request->is('post')) {
            $harvestBatchLog = $this->HarvestBatchLogs->patchEntity($harvestBatchLog, $this->request->data);
            if ($this->HarvestBatchLogs->save($harvestBatchLog)) {
                $this->Flash->success(__('The harvest batch log has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The harvest batch log could not be saved. Please, try again.'));
            }
        }
        $zones = $this->HarvestBatchLogs->Zones->find('list', [
            'limit' => 200,
            'valueField' => function ($e) {
                return $e->id .' - '. $e->label;
            }
        ]);
        // debug($zones->toArray()); die();
        $harvestbatches = $this->HarvestBatchLogs->Harvestbatches->find('list', [
            'limit' => 200,
            'valueField' => function ($e) {
                return $e->id . ' - ' . $e->planted_date . ' - ' . $e->cultivar->label;
            }


        ])->contain(['Cultivars']);
        $this->set(compact('harvestBatchLog', 'zones', 'harvestbatches'));
        $this->set('_serialize', ['harvestBatchLog']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Harvest Batch Log id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $harvestBatchLog = $this->HarvestBatchLogs->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $harvestBatchLog = $this->HarvestBatchLogs->patchEntity($harvestBatchLog, $this->request->data);
            if ($this->HarvestBatchLogs->save($harvestBatchLog)) {
                $this->Flash->success(__('The harvest batch log has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The harvest batch log could not be saved. Please, try again.'));
            }
        }
        $zones = $this->HarvestBatchLogs->Zones->find('list', ['limit' => 200]);
        $batches = $this->HarvestBatchLogs->Batches->find('list', ['limit' => 200]);
        $this->set(compact('harvestBatchLog', 'zones', 'batches'));
        $this->set('_serialize', ['harvestBatchLog']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Harvest Batch Log id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $harvestBatchLog = $this->HarvestBatchLogs->get($id);
        if ($this->HarvestBatchLogs->delete($harvestBatchLog)) {
            $this->Flash->success(__('The harvest batch log has been deleted.'));
        } else {
            $this->Flash->error(__('The harvest batch log could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
