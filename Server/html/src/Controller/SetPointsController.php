<?php
namespace App\Controller;

use App\Controller\AppController;


/**
 * SetPoints Controller
 *
 * @property \App\Model\Table\SetPointsTable $SetPoints
 *
 * @method \App\Model\Entity\SetPoint[] paginate($object = null, array $settings = [])
 */
class SetPointsController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $setPoints = $this->paginate($this->SetPoints);

        $this->set(compact('setPoints'));
        $this->set('_serialize', ['setPoints']);
    }

    /**
     * View method
     *
     * @param string|null $id Set Point id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $setPoint = $this->SetPoints->get($id, [
            'contain' => []
        ]);

        $this->set('setPoint', $setPoint);
        $this->set('_serialize', ['setPoint']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $setPoint = $this->SetPoints->newEntity();
        if ($this->request->is('post')) {
            $setPoint = $this->SetPoints->patchEntity($setPoint, $this->request->getData());
            if ($this->SetPoints->save($setPoint)) {
                $this->Flash->success(__('The set point has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The set point could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('setPoint'));
        $this->set('_serialize', ['setPoint']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Set Point id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null, $revertToDefault = null)
    {
        $setPoint = $this->SetPoints->get($id);
        $show_metric = $this->getRequest()->getSession()->read('Auth.User.show_metric'); 
        if ($this->request->is(['patch', 'post', 'put'])) {
            $setPoint = $this->SetPoints->patchEntity($setPoint, $this->request->getData());
            if ($revertToDefault == true) {
                $show_metric = $this->getRequest()->getSession()->read('Auth.User.show_metric');
                $setPoint = $this->SetPoints->revertToDefaultSetPoint($setPoint, $show_metric);
                $value = $setPoint->default_value;
            } else {
                $setPoint->default_setpoint_id = 0;
                $value = $setPoint->value;
            }
            if ($this->SetPoints->save($setPoint)) {
                if ($this->request->is('ajax')) {
                    $this->render(false);  
                    $this->response->body($value);
                    return $this->response;
                } else {
                    $this->Flash->success(__('The set point has been saved.'));
                    return $this->redirect(['action' => 'index']);
                }
            }
            $this->Flash->error(__('The set point could not be saved. Please, try again.'));
        }
        $this->set(compact('setPoint'));
        $this->set('_serialize', ['setPoint']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Set Point id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $setPoint = $this->SetPoints->get($id);
        if ($this->SetPoints->delete($setPoint)) {
            if ($this->request->is('ajax')) {
                $zoneTypeSetPoint = $this->SetPoints->find('all', ['conditions' => ['data_type' => $this->request->data['data_type'], 'target_type' =>  1, 'target_id' => $this->request->data['target_id'] ]])->first();
                $this->render(false);
                $this->response->body($zoneTypeSetPoint->value);
                return $this->response;
            } else {
                $this->Flash->success(__('The set point has been deleted.'));                
            }
        } else {
            $this->Flash->error(__('The set point could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }
}
