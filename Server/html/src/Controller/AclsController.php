<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Acls Controller
 *
 * @property \App\Model\Table\AclsTable $Acls
 */
class AclsController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Roles']
        ];
        $acls = $this->paginate($this->Acls);

        $this->set(compact('acls'));
        $this->set('_serialize', ['acls']);
    }

    /**
     * View method
     *
     * @param string|null $id Acl id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $acl = $this->Acls->get($id, [
            'contain' => ['Roles']
        ]);

        $this->set('acl', $acl);
        $this->set('_serialize', ['acl']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $acl = $this->Acls->newEntity();
        if ($this->request->is('post')) {
            $acl = $this->Acls->patchEntity($acl, $this->request->data);
            if ($this->Acls->save($acl)) {
                $this->Flash->success(__('The acl has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                debug($acl); die();
                $this->Flash->error(__('The acl could not be saved. Please, try again.'));
            }
        }
        $users = $this->Acls->Users->find('list', ['limit' => 200]);
        $roles = $this->Acls->Roles->find('list', ['limit' => 200]);
        $this->set(compact('acl', 'users', 'roles'));
        $this->set('_serialize', ['acl']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Acl id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $acl = $this->Acls->get($id, [
            'contain' => ['Roles']
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $acl = $this->Acls->patchEntity($acl, $this->request->data);
            if ($this->Acls->save($acl)) {
                $this->Flash->success(__('The acl has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The acl could not be saved. Please, try again.'));
            }
        }
        $users = $this->Acls->Users->find('list', ['limit' => 200]);
        $roles = $this->Acls->Roles->find('list', ['limit' => 200]);
        $this->set(compact('acl', 'users', 'roles'));
        $this->set('_serialize', ['acl']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Acl id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $acl = $this->Acls->get($id);
        if ($this->Acls->delete($acl)) {
            $this->Flash->success(__('The acl has been deleted.'));
        } else {
            $this->Flash->error(__('The acl could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
