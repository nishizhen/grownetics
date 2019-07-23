<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;

/**
 * Organizations Controller
 *
 * @property \App\Model\Table\OrganizationsTable $Organizations
 *
 * @method \App\Model\Entity\Organization[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class OrganizationsController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $orgUserRoles = $this->UsersRoles->find('all', [
            'conditions' => [
                'user_id' => $this->Auth->user('id'),
            ],
            'fields' => [
                'organization_id',
                'role_id'
            ]
        ]);

        $orgIds = [];
        foreach ($orgUserRoles as $role) {
            if ($role['organization_id']) {
                array_push($orgIds, $role['organization_id']);
            }
        }

        $this->paginate = [
            'conditions' => [
                'Organizations.id IN' => $orgIds
            ]
        ];
        try {
            $organizations = $this->paginate($this->Organizations);
        } catch (\Exception $exception) {
            # There was an error paginating, likely no organizations
            # exist for this user yet. Return an empty array.
            $organizations = [];
        }

        $this->Roles = TableRegistry::get("Roles");
        $inviteeRoleId = $this->Roles->findByLabel('Organization Invitee')->first()->id;

        $this->set(compact('organizations', 'orgUserRoles', 'inviteeRoleId'));
    }

    /**
     * View method
     *
     * @param string|null $id Organization id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $organization = $this->Organizations->find('all', [
            'conditions' => ['id' => $id]
        ])->contain([
            'UsersRoles' => ['Users', 'Roles']
        ])->first();

        $adminCount = $this->Organizations->getAdmins($id)->count();

        $this->set(compact('organization', 'adminCount'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $organization = $this->Organizations->newEntity();
        if ($this->request->is('post')) {
            $organization = $this->Organizations->patchEntity($organization, $this->request->getData());
            $organization->user_id = $this->Auth->user('id');
            if ($this->Organizations->save($organization)) {
                $this->Flash->success(__('The organization has been saved.'));

                return $this->redirect(['action' => 'view', $organization->id]);
            }
            $this->Flash->error(__('The organization could not be saved. Please, try again.'));
        }

        $this->set(compact('organization'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Organization id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        if ($this->Organizations->isUserAdmin($this->request->session()->read('Auth.User.id'), $id)) {
            $organization = $this->Organizations->get($id, [
                'contain' => []
            ]);
            if ($this->request->is(['patch', 'post', 'put'])) {
                $organization = $this->Organizations->patchEntity($organization, $this->request->getData());
                if ($this->Organizations->save($organization)) {
                    $this->Flash->success(__('The organization has been saved.'));

                    return $this->redirect(['action' => 'view', $organization->id]);
                }
                $this->Flash->error(__('The organization could not be saved. Please, try again.'));
            }
            $this->set(compact('organization'));
        } else {
            $this->Flash->error(__('You must be an organization admin to edit this organization.'));
            return $this->redirect($this->referer());
        }
    }

    /**
     * Delete method
     *
     * @param string|null $id Organization id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        if ($this->Organizations->isUserAdmin($this->request->session()->read('Auth.User.id'), $id)) {
            $this->request->allowMethod(['post', 'delete']);
            $organization = $this->Organizations->get($id);
            if ($this->Organizations->delete($organization)) {
                $this->Flash->success(__('The organization has been deleted.'));
            } else {
                $this->Flash->error(__('The organization could not be deleted. Please, try again.'));
            }

            return $this->redirect(['action' => 'index']);
        } else {
            $this->Flash->error(__('You must be an organization admin to delete this organization.'));
            return $this->redirect($this->referer());
        }
    }

    public function addUser($organizationId)
    {
        $this->request->allowMethod(['post', 'delete']);
        $email = $this->request->getData('email');
        $this->Organizations->addUserByEmail($organizationId, $email);
        $this->Flash->success(__('The user has been added successfully.'));
        return $this->redirect(['action' => 'view', $organizationId]);
    }

    public function respondToInvite($organizationId, $accept = 1)
    {
        $this->request->allowMethod(['post', 'delete']);
        if ($this->Organizations->respondToInvite($organizationId, $this->Auth->user('id'), $accept)) {
            if ($accept) {
                $this->Flash->success(__('You have successfully joined the organization.'));
                return $this->redirect(['action' => 'view', $organizationId]);
            } else {
                $this->Flash->success(__('You have deleted the invitation.'));
                return $this->redirect(['action' => 'index']);
            }
        } else {
            $this->Flash->success(__('There was a problem responding to the invitation.'));
            return $this->redirect(['action' => 'index']);
        }
    }

    public function setActiveOrganization($organization_id = FALSE)
    {
        $this->loadModel('Users');
        $session = $this->getRequest()->getSession();
        if ($organization_id) {
            $validRoles = [
                $this->Roles->findByLabel('Organization Admin')->first()->id,
                $this->Roles->findByLabel('Organization Member')->first()->id
            ];
            $userRoles = $this->UsersRoles->find('all', [
                'conditions' => [
                    'user_id' => $this->Auth->user('id'),
                    'organization_id' => $organization_id,
                    'role_id IN' => $validRoles
                ],
                'fields' => [
                    'organization_id',
                    'role_id'
                ]
            ]);

            if ($userRoles->count()) {
                $user = $session->read('Auth.User');
                $user->current_organization_id = $organization_id;
                $this->Users->save($user);
                $session->write('Auth.User', $user);
                $this->Flash->success(__('You have switched active Organizations.'));
            } else {
                $this->Flash->error(__('There was an error switching active Organizations.'));
            }
        } else {
            $user = $session->read('Auth.User');
            // dd($user);
            $user->current_organization_id = null;
            // dd($user);
            $this->Users->save($user);
            $session->delete('Auth.User.current_organization_id');
            $this->Flash->success(__('You have logged out of your Organization.'));
        }
        return $this->redirect($this->referer());
    }

    public function setUserRole($organizationId, $userId, $roleId)
    {
        try {
            if (
                $this->Organizations->isUserAdmin($this->request->session()->read('Auth.User.id'), $organizationId)
                &&
                $this->Organizations->setUserRole($organizationId, $userId, $roleId)
            ) {
                $this->Flash->success(__('User role set successfully.'));
            } else {
                $this->Flash->error(__('Error setting user role.'));
            }
        } catch (\Exception $e) {
            $this->Flash->error(__($e->getMessage()));
        }
        return $this->redirect($this->referer());
    }
}
