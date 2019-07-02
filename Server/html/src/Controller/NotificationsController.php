<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Notifications Controller
 *
 * @property \App\Model\Table\NotificationsTable $Notifications
 */
class NotificationsController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index($notification_level=-1)
    {
        $this->paginate = [
            'contain' => ['Rules'],
            'order' => ['id'=>'DESC'],
        ];
        if ($notification_level > -1) {
            $query = $this->Notifications->find()->where(['Notifications.notification_level =' => $notification_level]);
        } else {
            $query = $this->Notifications;
        }
        $notifications = $this->paginate($query);

        $unsent_notification_count = $this->Notifications->find()->where(['Notifications.status =' => $this->Notifications->enumValueToKey('status','Unsent')])->count();

        $this->set(compact('notifications','notification_level', 'unsent_notification_count'));
        $this->set('_serialize', ['notifications','notification_level', 'unsent_notification_count']);
    }

    /**
     * View method
     *
     * @param string|null $id Notification id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $notification = $this->Notifications->get($id, [
            'contain' => ['Rules']
        ]);

        $this->set('notification', $notification);
        $this->set('_serialize', ['notification']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $notification = $this->Notifications->newEntity();
        if ($this->request->is('post')) {
            $notification = $this->Notifications->patchEntity($notification, $this->request->data);
            $notification->source_type = $this->Notifications->enumValueToKey('source_type','Admin');
            $notification->source_id = $this->request->session()->read('Auth.User.id');
            $notification->status = 0;
            if ($this->Notifications->save($notification)) {
                $this->Flash->success(__('The notification has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The notification could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('notification'));
        $this->set('_serialize', ['notification']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Notification id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $notification = $this->Notifications->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $notification = $this->Notifications->patchEntity($notification, $this->request->data);
            if ($this->Notifications->save($notification)) {
                $this->Flash->success(__('The notification has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The notification could not be saved. Please, try again.'));
            }
        }
        $rules = $this->Notifications->Rules->find('list', ['limit' => 200]);
        $this->set(compact('notification', 'rules'));
        $this->set('_serialize', ['notification']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Notification id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $notification = $this->Notifications->get($id);
        if ($this->Notifications->delete($notification)) {
            $this->Flash->success(__('The notification has been deleted.'));
        } else {
            $this->Flash->error(__('The notification could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function out($id) {
        $notification = $this->Notifications->get($id);
        $this->viewBuilder()->layout('blank');
        $this->response->type('text/xml');
        $this->set('response',$notification['message']);
        $this->set('xml','<?xml version="1.0" encoding="UTF-8"?>');
    }


    public function clearQueue()
    {
        $this->request->allowMethod(['post', 'delete']);
        $this->Notifications->clearQueue();
        $this->Flash->success(__('The notification queue has been cleared.'));
        return $this->redirect(['action' => 'index']);
    }
}
