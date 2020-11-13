<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Lib\Integrations\ArgusApi;

/**
 * Devices Controller
 *
 * @property \App\Model\Table\DevicesTable $Devices
 */
class DevicesController extends AppController
{

    public function isAuthorized($user)
    {
        // All users can edit their own account, and logout.
        if (in_array($this->request->action, ['count'])) {
            return true;
        }

        return parent::isAuthorized($user);
    }

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $devices = $this->paginate($this->Devices);
        $this->set(compact('devices'));
        $this->set('_serialize', ['devices']);
    }

    public function count()
    {
        $this->viewBuilder()->layout('blank');
        $this->response->type('text/plain');
        $this->set('response', $this->Devices->find('all')->count());
    }

    /**
     * View method
     *
     * @param string|null $id Device id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $device = $this->Devices->get($id, [
            'contain' => ['Sensors','Outputs']
        ]);

        $this->set('device', $device);
        $this->set('_serialize', ['device']);
    }

    public function setMode($device_id, $mode)
    {
        $sf = new \SensioLabs\Consul\ServiceFactory(['base_uri' => 'http://consul:8500']);
        $kv = $sf->get('kv');
        $kv->put('faker/devices/'.$device_id.'/mode', $mode);
        return $this->redirect('/devices');
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $device = $this->Devices->newEntity();
        if ($this->request->is('post')) {
            $device = $this->Devices->patchEntity($device, $this->request->data);
            $device->dontMap = true;
            if ($this->Devices->save($device)) {
                $this->Flash->success(__('The device has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The device could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('device'));
        $this->set('_serialize', ['device']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Device id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $device = $this->Devices->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $device = $this->Devices->patchEntity($device, $this->request->data);
            $device->dontMap = true;
            if ($this->Devices->save($device)) {
                $this->Flash->success(__('The device has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The device could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('device'));
        $this->set('_serialize', ['device']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Device id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $device = $this->Devices->get($id);
        if ($this->Devices->delete($device)) {
            $this->Flash->success(__('The device has been deleted.'));
        } else {
            $this->Flash->error(__('The device could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function reboot($id = null) {

        $device = $this->Devices->get($id, [
            'contain' => []
        ]);
        AppController::notification('Dashboard Notification',$this->request->session()->read('Auth.User.name')." rebooted device ".$device['id'].".");

        $device['status'] = $this->Devices->enumValueToKey('status','Rebooting');
        $this->Devices->save($device);

        $this->redirect('/devices/view/'.$device['id']);
    }
}
