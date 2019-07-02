<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Cultivars Controller
 *
 * @property \App\Model\Table\CultivarsTable $Cultivars
 */
class CultivarsController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $cultivars = $this->paginate($this->Cultivars,['order'=>['label'=>'ASC']]);

        $this->set(compact('cultivars'));
        $this->set('_serialize', ['cultivars']);
    }

    /**
     * View method
     *
     * @param string|null $id Cultivar id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $this->loadModel('Notes');
        $cultivar = $this->Cultivars->get($id, [
        ]);
        $this->loadModel('Tasks');
        $this->loadModel('HarvestBatches');

        $harvestBatches = $this->HarvestBatches->find('all', [
            'order' => [
                'planted_date' => 'asc'
            ],
            'contain' => [
                'Recipes',
                'batchRecipeEntries' => [
                    'sort' => [
                        'batchRecipeEntries.id' => 'asc'
                    ]
                ],
                'Cultivars' => [
                    'conditions' => [
                        'Cultivars.id' => $id
                    ]
                ]
            ]
        ]);

        $this->paginate($harvestBatches);
        $this->set('bodyClass','cultivars view');
        $this->set('cultivar',  $cultivar);
        $this->set('harvestBatches',  $harvestBatches);
        $this->set('_serialize', ['harvestBatches', 'cultivar']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $cultivar = $this->Cultivars->newEntity();
        if ($this->request->is('post')) {
            $cultivar = $this->Cultivars->patchEntity($cultivar, $this->request->data);
            if ($this->Cultivars->save($cultivar)) {
                $this->Flash->success(__('The cultivar has been saved.'));

                return $this->redirect(['action' => 'view',$cultivar->id]);
            } else {
                $this->Flash->error(__('The cultivar could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('cultivar'));
        $this->set('_serialize', ['cultivar']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Cultivar id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $cultivar = $this->Cultivars->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            if (isset($this->request->data['photo'])) {
                $uid=$this->Auth->user('id');
                $filename=basename($this->request->data['photo']['name']);
                $full_filename=date("Y-M-D").$uid."-".$filename;

                if (move_uploaded_file($this->request->data['photo']['tmp_name'], WWW_ROOT . 'photos' . DS . $full_filename)) {
                    chmod(WWW_ROOT . 'photos' . DS . $full_filename, 0755);
                    $this->request->data['photo'] = $full_filename;
                }
            } else {
                unset($this->request->data['photo']);
            }

            $cultivar = $this->Cultivars->patchEntity($cultivar, $this->request->data);
            if ($this->Cultivars->save($cultivar)) {
                $this->Flash->success(__('The cultivar has been saved.'));

                return $this->redirect(['action' => 'view',$cultivar->id]);
            } else {
                $this->Flash->error(__('The cultivar could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('cultivar'));
        $this->set('_serialize', ['cultivar']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Cultivar id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $cultivar = $this->Cultivars->get($id);
        if ($this->Cultivars->delete($cultivar)) {
            $this->Flash->success(__('The cultivar has been deleted.'));
        } else {
            $this->Flash->error(__('The cultivar could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
