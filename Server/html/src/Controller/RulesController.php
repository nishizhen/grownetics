<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Rules Controller
 *
 * @property \App\Model\Table\RulesTable $Rules
 */
class RulesController extends AppController
{
    
    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'conditions' => [
                'is_default' => 0
            ],
            'limit' => 100,
            'contain' => [
                'RuleConditions',
                'RuleActions',
                'RuleActions.RuleActionTargets'
            ]
        ];
        $rules = $this->paginate($this->Rules);

        $this->set(compact('rules'));
        $this->set('_serialize', ['rules']);
    }

    /**
     * View method
     *
     * @param string|null $id Rule id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $rule = $this->Rules->get($id, [
            'contain' => []
        ]);

        $this->set('rule', $rule);
        $this->set('_serialize', ['rule']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $rule = $this->Rules->newEntity();
        if ($this->request->is('post')) {
            $this->request->data['status'] = 1;
            $rule = $this->Rules->patchEntity($rule, $this->request->data);
            if ($this->Rules->save($rule)) {
                $this->Flash->success(__('The rule has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The rule could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('rule'));
        $this->set('_serialize', ['rule']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Rule id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $rule = $this->Rules->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $rule = $this->Rules->patchEntity($rule, $this->request->data);
            if ($this->Rules->save($rule)) {
                $this->Flash->success(__('The rule has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The rule could not be saved. Please, try again.'));
            }
        }

        $this->set(compact('rule'));
        $this->set('_serialize', ['rule']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Rule id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $rule = $this->Rules->get($id);
        if ($this->Rules->delete($rule)) {
            $this->Flash->success(__('The rule has been deleted.'));
        } else {
            $this->Flash->error(__('The rule could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
