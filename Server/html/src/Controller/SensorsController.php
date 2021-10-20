<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;

/**
 * Sensors Controller
 *
 * @property \App\Model\Table\SensorsTable $Sensors
 * @property \App\Model\Table\UsersTable $Users
 * @property this->Sensors->enum['sensor_type'] $SensorTypes
 */
class SensorsController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $this->loadModel('Users');
        $sensors = $this->paginate($this->Sensors, ['contain' => ['Devices', 'Zones']]);
        $id = $this->request->session()->read('Auth.User.id');
        $user = $this->Users->get($id);
        $this->set('showMetric', $user->show_metric);

        $this->set(compact('sensors'));
        $this->set('_serialize', ['sensors']);
    }

    /**
     * View method
     *
     * @param string|null $id Sensor id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $sensor = $this->Sensors->get($id, [
            'contain' => ['Devices']
        ]);

        $sensor_type_name = $this->Sensors->enumKeyToValue('sensor_type', $sensor->sensor_type_id);
        $this->set(compact('sensor','sensor_type_name'));
        $this->set('_serialize', ['sensor']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        // get the first floorplan, there will probably only be one anyway.
        try {
            $this->loadModel("Floorplans");
            $this->floorplan = $this->Floorplans->find()->last();
        } catch (RecordNotFoundException $e) {
            $this->log($e, 'debug');
            $this->floorplan = NULL;
        }

        $sensor = $this->Sensors->newEntity();
        $sensor->floorplan_id = $this->floorplan->id;

        if ($this->request->is('post')) {
            $this->loadModel("Devices");
            $this->loadModel("MapItems");

            $sensor->sensor_type_id = $this->request->data['sensor_type_id'];
            $device = $this->Devices->get($this->request->getData("device_id"),
                [ 'contain' => 'MapItems']
            );
            $map_item = $this->MapItems->get($device->map_item_id);

            $sensor = $this->Sensors->patchEntity($sensor, $this->request->data);
            // set lat/lon and geoJSON for mappable behavior
            $sensor->latitude = $map_item->latitude;
            $sensor->longitude = $map_item->longitude;
            $sensor->geoJSON = $map_item->geoJSON;

            if ($this->Sensors->save($sensor)) {
                $this->Flash->success(__('The sensor has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The sensor could not be saved. Please, try again.'));
            }
        }


        $this->loadModel('Sensors');
        $sensor_type = $this->Sensors->enums['sensor_type'];
        
        $devices = $this->Sensors->Devices->find('list');
        $zones = $this->Sensors->Zones->find('list', ['limit' => 200]);
        $this->set(compact('sensor', 'devices', 'zones', 'sensor_type'));
        $this->set('_serialize', ['sensor']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Sensor id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $sensor = $this->Sensors->get($id, [
            'contain' => []
        ]);

        if ($this->request->is(['patch', 'post', 'put'])) {

            $sensor['sensor_type_id'] = $this->request->data['sensor_type'];
            $sensor = $this->Sensors->patchEntity($sensor, $this->request->data);

            if($this->request->getParam("device_id")) {
                $this->loadModel("Devices");

                $device = $this->Devices->get($this->request->getData("device_id"),
                    [ 'contain' => 'MapItems']
                );
                $map_item = $this->MapItems->get($device->map_item_id);
                $sensor->latitude = $map_item->latitude;
                $sensor->longitude = $map_item>longitude;
                $sensor->geoJSON = $map_item->geoJSON;
            }
            if ($this->Sensors->save($sensor)) {
                $this->Flash->success(__('The sensor has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The sensor could not be saved. Please, try again.'));
            }
        }

        
        $sensor_type = $this->Sensors->enums['sensor_type'];


        $devices = $this->Sensors->Devices->find('list', ['limit' => 200]);
        $zones = $this->Sensors->Zones->find('list', ['limit' => 200]);
        //dd($sensor);
        $this->set(compact('sensor', 'devices', 'zones', 'sensor_type'));
        $this->set('_serialize', ['sensor']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Sensor id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $sensor = $this->Sensors->get($id);
        if ($this->Sensors->delete($sensor)) {
            $this->Flash->success(__('The sensor has been deleted.'));
        } else {
            $this->Flash->error(__('The sensor could not be deleted. Please, try again.'));
        }

        return $this->redirect($this->referer());
    }

	public function toggleActive($id = null) {
		$sensor = $this->Sensors->get($id);

		if ($sensor->status == 1) {
			$sensor->status = 0;
		} else {
			$sensor->status = 1;
		}
		$this->Sensors->save($sensor);

    	$this->redirect('/devices/reboot/'.$sensor->device_id);
	}

	public function reset($id = null) {
		$options = array('conditions' => array('Sensor.' . $this->Sensor->primaryKey => $id));
		$sensor = $this->Sensor->find('first', $options);
		if (!$sensor) {
			debug($sensor);
			dbgd($options);
			$this->Session->setFlash(__('Invalid sensor.'));
			return $this->redirect(array('action' => 'index'));
		}

		$sensor['status'] = 1;
		$this->Sensor->save($sensor);

		return $this->redirect(array('action' => 'index'));
	}

    

}
