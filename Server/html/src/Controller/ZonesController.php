<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Core\Exception\Exception;
use Cake\Datasource\Exception\RecordNotFoundException;

/**
 * Zones Controller
 *
 * @property \App\Model\Table\ZonesTable $Zones
 */
class ZonesController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $this->loadModel('Sensors');
        $this->loadModel('Users');
        $user = $this->Users->get($this->Auth->user('id'));
        $zones = $this->paginate($this->Zones, ['limit' => 10]);
        $tempSymbol = $this->Sensors->getTempDataSymbol($user->show_metric);

        $this->set(compact('zones', 'tempSymbol'));
        $this->set('_serialize', ['zones', 'tempSymbol']);
    }

    /**
     * View method
     *
     * @param string|null $id Zone id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        try {
            $zone = $this->Zones->get($id, [
                'contain' => ['Outputs', 'Sensors']
            ]);
        } catch (RecordNotFoundException $e) {
            $this->Flash->error(__('Zone '.$id.' could not be found.'));
            return $this->redirect('/');
        }

        $this->set('zone', $zone);
        $this->set('_serialize', ['zone']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $zone = $this->Zones->newEntity();
        if ($this->request->is('post')) {
            $zone = $this->Zones->patchEntity($zone, $this->request->data);
            $zone->status = 1;
            if ($this->Zones->save($zone)) {
                $this->Flash->success(__('The zone has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The zone could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('zone'));
        $this->set('_serialize', ['zone']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Zone id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {

        $zone = $this->Zones->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $zone = $this->Zones->patchEntity($zone, $this->request->data);
            if ($this->Zones->save($zone)) {
                $this->Flash->success(__('The zone has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The zone could not be saved. Please, try again.'));
            }
        }
        $plant_zone_types = $this->Zones->enums['plant_zone_types'];
        $this->set(compact('zone', 'plant_zone_types'));
        $this->set('_serialize', ['zone', 'plant_zone_types']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Zone id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $zone = $this->Zones->get($id);
        if ($this->Zones->delete($zone)) {
            $this->Flash->success(__('The zone has been deleted.'));
        } else {
            $this->Flash->error(__('The zone could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * getGroupsForRoom method
     *
     * @param string|null $id Zone id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function getGroupsForRoom($room_id = null)
    {
        try {
            $groups = $this->Zones->find('list', [
                'contain' => ['Outputs', 'Sensors'],
                'conditions' => [
                    'Zones.room_zone_id' => $room_id,
                    'Zones.zone_type_id' => $this->Zones->enumValueToKey('zone_types', 'Group')
                ]
            ]);
        } catch (RecordNotFoundException $e) {
            $this->Flash->error(__('Groups in Room '.$id.' could not be found.'));
            return $this->redirect('/');
        }
        $this->set('groups', $groups);
        $this->set('_serialize', ['groups']);
    }

    /**
     * getGroupsForRoom method
     *
     * @param string|null $id Zone id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function getRoomsbyGroup()
    {
        try {
            $rooms = $this->Zones->find('all', [
                'conditions' => ['Zones.room_zone_id' => 0, 'Zones.zone_type_id' => $this->Zones->enumValueToKey('zone_types', 'Room')]
            ])->toArray();
            foreach ($rooms as $room) {
                $this->Zones->find('all', [
                    'conditions' => ['Zones.room_zone_id' => $room->id, 'Zones.zone_type_id' => $this->Zones->enumValueToKey('zone_types', 'Group')]
                ])->toArray();
                $rooms = [];
            }
        } catch (RecordNotFoundException $e) {
            $this->Flash->error(__('Groups in Room '.$id.' could not be found.'));
            return $this->redirect('/');
        }
        $this->set('groups', $groups);
        $this->set('_serialize', ['groups']);
    }
}
