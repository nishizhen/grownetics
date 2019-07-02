<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * BatchRecipeEntries Controller
 *
 * @property \App\Model\Table\BatchRecipeEntriesTable $BatchRecipeEntries
 */
class BatchRecipeEntriesController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Zones', 'RecipeEntries']
        ];
        $batchRecipeEntries = $this->paginate($this->BatchRecipeEntries);

        $this->set(compact('batchRecipeEntries'));
        $this->set('_serialize', ['batchRecipeEntries']);
    }

    /**
     * View method
     *
     * @param string|null $id Batch Recipe Entry id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $batchRecipeEntry = $this->BatchRecipeEntries->get($id, [
            'contain' => 'Zones'
        ]);

        $this->set('batchRecipeEntry', $batchRecipeEntry);
        $this->set('_serialize', ['batchRecipeEntry']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $batchRecipeEntry = $this->BatchRecipeEntries->newEntity();
        if ($this->request->is('post')) {
            $batchRecipeEntry = $this->BatchRecipeEntries->patchEntity($batchRecipeEntry, $this->request->data);
            if ($this->BatchRecipeEntries->save($batchRecipeEntry)) {
                $this->Flash->success(__('The batch recipe entry has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The batch recipe entry could not be saved. Please, try again.'));
            }
        }
        $zones = $this->BatchRecipeEntries->Zones->find('list', ['limit' => 200]);
        $recipeEntries = $this->BatchRecipeEntries->RecipeEntries->find('list', ['limit' => 200]);
        $this->set(compact('batchRecipeEntry', 'zones', 'recipeEntries'));
        $this->set('_serialize', ['batchRecipeEntry']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Batch Recipe Entry id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $batchRecipeEntry = $this->BatchRecipeEntries->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $batchRecipeEntry = $this->BatchRecipeEntries->patchEntity($batchRecipeEntry, $this->request->data);
            if ($this->BatchRecipeEntries->save($batchRecipeEntry)) {
                $this->Flash->success(__('The batch recipe entry has been saved.'));

                return $this->redirect(['controller' => 'harvestBatches', 'action' => 'view', $batchRecipeEntry->batch_id]);
            } else {
                $this->Flash->error(__('The batch recipe entry could not be saved. Please, try again.'));
            }
        }
        $zones = $this->BatchRecipeEntries->Zones->find('list', ['limit' => 200]);
        $recipeEntries = $this->BatchRecipeEntries->RecipeEntries->find('list', ['limit' => 200]);
        $this->set(compact('batchRecipeEntry', 'zones', 'recipeEntries'));
        $this->set('_serialize', ['batchRecipeEntry']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Batch Recipe Entry id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $batchRecipeEntry = $this->BatchRecipeEntries->get($id);
        if ($this->BatchRecipeEntries->delete($batchRecipeEntry)) {
            $this->Flash->success(__('The batch recipe entry has been deleted.'));
        } else {
            $this->Flash->error(__('The batch recipe entry could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
