<?php

namespace App\Controller;

use App\Controller\AppController;
use App\Lib\SystemEventRecorder;

/**
 * Outputs Controller
 *
 * @property \App\Model\Table\OutputsTable $Outputs
 */
class OutputsController extends AppController
{

  /**
   * Index method
   *
   * @return \Cake\Network\Response|null
   */
  public function index()
  {
    $RuleActions = $this->loadModel('RuleActions');
    $Rules = $this->loadModel('Rules');
    $RuleConditions = $this->loadModel('RuleConditions');

    $this->paginate = [
      'contain' => ['RuleActionTargets.RuleActions.Rules']
    ];
    $outputs = $this->paginate($this->Outputs);

    # Get lighting rules for schedule select box.
    $lightingRules = $Rules->find('all', [
      'conditions' => [
        'type' => $Rules->enumValueToKey('type', 'Lighting Schedule'),
      ],
      'contain' => [
        'RuleActions.RuleActionTargets',
        'RuleConditions'
      ]
    ]);

    foreach ($outputs as $output) {
      foreach ($output['rule_action_targets'] as $rat) {
        # Ignore unused RATs
        if ($rat['rule_action']) {
          $rule = $Rules->get($rat['rule_action']->rule_id);
          if (
            $rule['type'] == $this->Rules->enumValueToKey('type', 'Lighting Schedule')
            &&
            $output['hardware_type'] == $this->Outputs->enumValueToKey('hardware_type', 'Light')
          ) {
            $output['scheduleRule'] = $rule;
            $output['scheduleRat'] = $rat;
          } else if (
            $rule['type'] == $this->Rules->enumValueToKey('type', 'Co2 Control')
            &&
            $output['hardware_type'] == $this->Outputs->enumValueToKey('hardware_type', 'Co2 Doser')
          ) {
            $rule = $Rules->get($rat['rule_action']->rule_id);
            $ruleCondition = $RuleConditions->find('all', ['conditions' => ['rule_id' => $rule->id]])->first();
            $output['co2Rule'] = $rule;
            $output['co2Rat'] = $rat;
            $output['co2RuleCondition'] = $ruleCondition;
          }
        }
      }
    }
    $this->set(compact('outputs', 'lightingRules'));
  }

  /**
   * View method
   *
   * @param string|null $id Output id.
   * @return \Cake\Network\Response|null
   * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
   */
  public function view($id = null)
  {
    $output = $this->Outputs->get($id, [
      'contain' => ['Devices', 'Zones', 'Rules']
    ]);

    $this->set('output', $output);
    $this->set('_serialize', ['output']);
  }

  /**
   * Add method
   *
   * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
   */
  public function add()
  {
    $output = $this->Outputs->newEntity();
    if ($this->request->is('post')) {
      $output = $this->Outputs->patchEntity($output, $this->request->data);
      if ($this->Outputs->save($output)) {
        $this->Flash->success(__('The output has been saved.'));

        return $this->redirect(['action' => 'index']);
      } else {
        $this->Flash->error(__('The output could not be saved. Please, try again.'));
      }
    }
    $devices = $this->Outputs->Devices->find('list', ['limit' => 200]);
    $zones = $this->Outputs->Zones->find('list', ['limit' => 200]);
    $rules = $this->Outputs->Rules->find('list', ['limit' => 200]);
    $this->set(compact('output', 'devices', 'zones', 'rules'));
    $this->set('_serialize', ['output']);
  }

  /**
   * Edit method
   *
   * @param string|null $id Output id.
   * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
   * @throws \Cake\Network\Exception\NotFoundException When record not found.
   */
  public function edit($id = null)
  {
    $output = $this->Outputs->get($id);
    if ($this->request->is(['patch', 'post', 'put'])) {
      $output = $this->Outputs->patchEntity($output, $this->request->data);
      if ($this->Outputs->save($output)) {
        $this->Flash->success(__('The output has been saved.'));

        return $this->redirect(['action' => 'index']);
      } else {
        $this->Flash->error(__('The output could not be saved. Please, try again.'));
      }
    }
    $devices = $this->Outputs->Devices->find('list', ['limit' => 200]);
    $this->set(compact('output', 'devices'));
    $this->set('_serialize', ['output']);
  }

  /**
   * Delete method
   *
   * @param string|null $id Output id.
   * @return \Cake\Network\Response|null Redirects to index.
   * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
   */
  public function delete($id = null)
  {
    $this->request->allowMethod(['post', 'delete']);
    $output = $this->Outputs->get($id);
    if ($this->Outputs->delete($output)) {
      $this->Flash->success(__('The output has been deleted.'));
    } else {
      $this->Flash->error(__('The output could not be deleted. Please, try again.'));
    }

    return $this->redirect(['action' => 'index']);
  }

  public function togglePower($id = null, $toggle_state = null)
  {
    if (!$this->Outputs->exists($id)) {
      throw new NotFoundException(__('Invalid output'));
    }
    $recorder = new SystemEventRecorder();
    $output = $this->Outputs->get($id);
    if ($output->status == $toggle_state) {
      return $this->redirect($this->request->env('HTTP_REFERER'));
    }

    AppController::notification([
      'notification_level' => 'Text Message',
      'message' => $this->request->session()->read('Auth.User.name') . " toggled the power of output " . $output->id . " - " . $output->label . " from " . $this->Outputs->enumKeyToValue('status', $output->status) . " to " . $this->Outputs->enumKeyToValue('status', $toggle_state) . ".",
      'user_id' => $this->request->session()->read('Auth.User.id'),
      'source_type' => $output->output_type,
      'source_id' => $output->id
    ]);

    if ($toggle_state == $this->Outputs->enumValueToKey('status', 'Off')) {
      $recorder->recordEvent('user_actions', 'toggle_power', 1, ['user_id' => $this->request->session()->read('Auth.User.id'), 'name' => $this->Auth->user('name'), 'output_id' => $output->id, 'pre_toggle_status' => $output->status, 'post_toggle_status' => $this->Outputs->enumValueToKey('status', 'Off')]);
      $output->status = $toggle_state;
    } else {
      $recorder->recordEvent('user_actions', 'toggle_power', 1, ['user_id' => $this->request->session()->read('Auth.User.id'), 'name' => $this->Auth->user('name'), 'output_id' => $output->id, 'pre_toggle_status' => $output->status, 'post_toggle_status' => $this->Outputs->enumValueToKey('status', 'On')]);
      $output->status = $toggle_state;
    }
    $this->Outputs->save($output);
    $this->redirect($this->request->env('HTTP_REFERER'));
  }

  public function setSchedule($ruleActionTargetId, $ruleActionId)
  {
    $RuleActionTargets = $this->loadModel('RuleActionTargets');
    $rat = $RuleActionTargets->get($ruleActionTargetId);
    $rat->rule_action_id = $ruleActionId;
    $rat->status = $RuleActionTargets->enumValueToKey('status', 'Enabled');
    $RuleActionTargets->save($rat);

    $recorder = new SystemEventRecorder();
    $recorder->recordEvent('user_actions', 'set_schedule', 1, [
      'user_id' => $this->request->session()->read('Auth.User.id'),
      'name' => $this->Auth->user('name'),
      'rule_action_target_id' => $ruleActionTargetId,
      'rule_action_id' => $ruleActionId
    ]);

    $this->Flash->success(__('The output schedule has been updated.'));

    $this->redirect($this->request->env('HTTP_REFERER'));
  }
}
