<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Lib\SystemEventRecorder;

/**
 * RuleConditions Controller
 *
 * @property \App\Model\Table\RuleConditionsTable $RuleConditions
 *
 * @method \App\Model\Entity\RuleCondition[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class RuleConditionsController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Rules']
        ];
        $ruleConditions = $this->paginate($this->RuleConditions);

        $this->set(compact('ruleConditions'));
    }

    /**
     * View method
     *
     * @param string|null $id Rule Condition id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $ruleCondition = $this->RuleConditions->get($id, [
            'contain' => ['Rules']
        ]);

        $this->set('ruleCondition', $ruleCondition);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $ruleCondition = $this->RuleConditions->newEntity();
        if ($this->request->is('post')) {
            $ruleCondition = $this->RuleConditions->patchEntity($ruleCondition, $this->request->getData());
            if ($this->RuleConditions->save($ruleCondition)) {
                $this->Flash->success(__('The rule condition has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The rule condition could not be saved. Please, try again.'));
        }
        $rules = $this->RuleConditions->Rules->find('list', ['limit' => 200]);
        $this->set(compact('ruleCondition', 'rules'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Rule Condition id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null, $thresholdType = null)
    {
        $ruleCondition = $this->RuleConditions->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $oldTrigger = $ruleCondition->trigger_threshold;
            $oldReset = $ruleCondition->reset_threshold;
            if ($thresholdType=="trigger") {
                $ruleCondition->trigger_threshold = $this->request->getData('value');
            } else if ($thresholdType=="reset") {
                $ruleCondition->reset_threshold = $this->request->getData('value');
            }
            if ($this->RuleConditions->save($ruleCondition)) {
                $recorder = new SystemEventRecorder();
                $recorder->recordEvent('user_actions', 'update_rule_condition', 1, [
                    'user_id' => $this->request->session()->read('Auth.User.id'), 
                    'name' => $this->Auth->user('name'), 
                    'old_trigger_threshold' => $oldTrigger,
                    'old_reset_threshold' => $oldReset,
                    'new_trigger_threshold' => $ruleCondition->trigger_threshold,
                    'new_reset_threshold' => $ruleCondition->reset_threshold,
                ]);

                if ($this->request->is('ajax')) {
                    $this->render(false);  
                    $this->response->body($this->request->getData('value'));
                    return $this->response;
                } else {
                    $this->Flash->success(__('The set point has been saved.'));
                    return $this->redirect(['action' => 'index']);
                }            }
            $this->Flash->error(__('The rule condition could not be saved. Please, try again.'));
        }
        $rules = $this->RuleConditions->Rules->find('list', ['limit' => 200]);
        $this->set(compact('ruleCondition', 'rules'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Rule Condition id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $ruleCondition = $this->RuleConditions->get($id);
        if ($this->RuleConditions->delete($ruleCondition)) {
            $this->Flash->success(__('The rule condition has been deleted.'));
        } else {
            $this->Flash->error(__('The rule condition could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
