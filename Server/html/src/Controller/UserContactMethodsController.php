<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * UserContactMethods Controller
 *
 * @property \App\Model\Table\UserContactMethodsTable $UserContactMethods
 */
class UserContactMethodsController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $this->loadModel('UserContactMethods');
        $this->paginate = [
            'contain' => ['Users']
        ];
        $userContactMethods = $this->paginate($this->UserContactMethods);

        $this->set(compact('userContactMethods'));
        $this->set('_serialize', ['userContactMethods']);
    }

    /**
     * View method
     *
     * @param string|null $id User Contact Method id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $this->loadModel('UserContactMethods');
        $userContactMethod = $this->UserContactMethods->get($id, [
            'contain' => ['Users']
        ]);

        $this->set('userContactMethod', $userContactMethod);
        $this->set('_serialize', ['userContactMethod']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $this->loadModel('UserContactMethods');
        $userContactMethod = $this->UserContactMethods->newEntity();   
        if ($this->request->is('post')) {
            if ($this->request->data['type'] == 2) {
                $userContactMethod = $this->UserContactMethods->patchEntity($userContactMethod, $this->request->data, ['validate' => 'email']);
                $userContactMethod->value = $this->request->data['email'];
            }
            if ($this->request->data['type'] <= 1) {
                $userContactMethod = $this->UserContactMethods->patchEntity($userContactMethod, $this->request->data, ['validate' => 'phone']);
                $userContactMethod->value = $this->request->data['phone'];
            }
            $userContactMethod->user_id = $this->request->session()->read('Auth.User.id');
            if ($this->UserContactMethods->save($userContactMethod)) {
                $this->Flash->success(__('The user contact method has been saved.'));

                return $this->redirect(['controller' => 'users', 'action' => 'account']);
            } else {
                $this->Flash->error(__('The user contact method could not be saved. Please, check for formatting errors.'));
            }
        }
        $users = $this->UserContactMethods->Users->find('list', ['limit' => 200]);
        $this->set(compact('userContactMethod', 'users'));
        $this->set('_serialize', ['userContactMethod']);
    }

    /**
     * Edit method
     *
     * @param string|null $id User Contact Method id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $this->loadModel('UserContactMethods');
        $userContactMethod = $this->UserContactMethods->get($id);
        $prevValue = $userContactMethod->value;
        if ($this->request->is(['patch', 'post', 'put'])) {
            if (isset($this->request->data['email'])) {
                $userContactMethod = $this->UserContactMethods->patchEntity($userContactMethod, $this->request->data, ['validate' =>'email']);
                $userContactMethod->value = $this->request->data['email'];
            }
            if (isset($this->request->data['phone'])) {
                $userContactMethod = $this->UserContactMethods->patchEntity($userContactMethod, $this->request->data, ['validate' =>'phone']);
                $userContactMethod->value = $this->request->data['phone'];
            }
            $userContactMethod->user_id = $this->request->session()->read('Auth.User.id');
            if ($this->UserContactMethods->save($userContactMethod)) {
                $this->Flash->success(__('The user contact method has been saved.'));

                return $this->redirect(['controller' => 'users', 'action' => 'account']);
            } else {
                $this->Flash->error(__('Please make sure this is a valid U.S. phone number.'));
            }
        }
        $this->set(compact('userContactMethod', 'users', 'prevValue'));
        $this->set('_serialize', ['userContactMethod']);
    }

    /**
     * Delete method
     *
     * @param string|null $id User Contact Method id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->loadModel('UserContactMethods');
        $this->request->allowMethod(['post', 'delete']);
        $userContactMethod = $this->UserContactMethods->get($id);
        if ($this->UserContactMethods->delete($userContactMethod)) {
            $this->Flash->success(__('The user contact method has been deleted.'));
        } else {
            $this->Flash->error(__('The user contact method could not be deleted. Please, try again.'));
        }

        return $this->redirect(['controller' => 'users', 'action' => 'account']);
    }
}
