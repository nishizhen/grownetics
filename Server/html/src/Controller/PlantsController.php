<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Lib\DataConverter;
use Cake\Cache\Cache;
use function GuzzleHttp\json_decode;

/**
 * Plants Controller
 *
 * @property \App\Model\Table\PlantsTable $Plants
 *
 * @method \App\Model\Entity\Plant[] paginate($object = null, array $settings = [])
 */
class PlantsController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['MapItems', 'HarvestBatches', 'Recipes', 'Tasks', 'Zones']
        ];
        $plants = $this->paginate($this->Plants);

        $this->set(compact('plants'));
        $this->set('_serialize', ['plants']);
    }

    /**
     * View method
     *
     * @param string|null $id Plant id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $this->loadModel('Sensors');
        $plant = $this->Plants->get($id);
        $show_metric = $this->getRequest()->getSession()->read('Auth.User.show_metric');
        $converter = new DataConverter();
        $plant['wet_whole_weight'] = $converter->displayUnits($plant->wet_whole_weight, $this->Sensors->enumValueToKey('data_type', 'Weight'), $show_metric);
        $plant['wet_waste_weight'] = $converter->displayUnits($plant->wet_waste_weight, $this->Sensors->enumValueToKey('data_type', 'Weight'), $show_metric);
        $plant['wet_whole_defoliated_weight'] = $converter->displayUnits($plant->wet_whole_defoliated_weight, $this->Sensors->enumValueToKey('data_type', 'Weight'), $show_metric);
        $plant['dry_whole_weight'] = $converter->displayUnits($plant->dry_whole_weight, $this->Sensors->enumValueToKey('data_type', 'Weight'), $show_metric);
        $plant['dry_waste_weight'] = $converter->displayUnits($plant->dry_waste_weight, $this->Sensors->enumValueToKey('data_type', 'Weight'), $show_metric);
        $plant['dry_whole_trimmed_weight'] = $converter->displayUnits($plant->dry_whole_trimmed_weight, $this->Sensors->enumValueToKey('data_type', 'Weight'), $show_metric);
        if (!$show_metric) {
            $plant['weightUnit'] = 'lbs';
        } else {
            $plant['weightUnit'] = 'kg';
        }
        $this->set('plant', $plant);
        $this->set('_serialize', ['plant']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $this->autoRender = false;
        if ($this->request->is('post')) {
            if ($this->Plants->generatePlantsForBatch(
                $this->request->data['batch_id'],
                $this->request->data['plant_start_id'],
                $this->request->data['plant_end_id'],
                $this->request->data['plant_list'],
                $this->request->data['cultivar_id']
            )) {
                $this->Flash->success(__('The plants have been saved.'));

                return $this->redirect('/harvest-batches/view/' . $this->request->data['batch_id']);
            } else {
                $this->Flash->error(__($this->Plants->error));
            }
        }
        $mapItems = $this->Plants->MapItems->find('list', ['limit' => 200]);
        $harvestBatches = $this->Plants->HarvestBatches->find('list', ['limit' => 200]);
        $recipes = $this->Plants->Recipes->find('list', ['limit' => 200]);
        $tasks = $this->Plants->Tasks->find('list', ['limit' => 200]);
        $zones = $this->Plants->Zones->find('list', ['limit' => 200]);
        $this->set(compact('plant', 'mapItems', 'harvestBatches', 'recipes', 'tasks', 'zones'));
        $this->set('_serialize', ['plant']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Plant id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null, $weightField = null)
    {
        $plant = $this->Plants->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            if ($this->request->is('ajax')) {
                $this->render(false);
                $weightField = preg_replace('/\d/', '', $weightField);
                $plant[$weightField] = $this->request->getData($weightField);
            } else {
                $this->Plants->patchEntity($plant, $this->request->getData());
            }
            if ($this->Plants->save($plant, ['weightField' => $weightField])) {
                if ($this->request->is('ajax')) {
                    return;
                } else {
                    $this->Flash->success(__('The plant has been saved.'));
                    return $this->redirect(['action' => 'index']);
                }
            }
            $this->Flash->error(__('The plant could not be saved. Please, try again.'));
        }
        $mapItems = $this->Plants->MapItems->find('list', ['limit' => 200]);
        $harvestBatches = $this->Plants->HarvestBatches->find('list', ['limit' => 200]);
        $recipes = $this->Plants->Recipes->find('list', ['limit' => 200]);
        $tasks = $this->Plants->Tasks->find('list', ['limit' => 200]);
        $zones = $this->Plants->Zones->find('list', ['limit' => 200]);
        $this->set(compact('plant', 'mapItems', 'harvestBatches', 'recipes', 'tasks', 'zones'));
        $this->set('_serialize', ['plant']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Plant id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $plants = [];
        if ($id) {
            array_push($plants, $this->Plants->get($id));
        } else {
            $plants = $this->request->data('plants');
        }

        foreach ($plants as $plant_id) {
            $plant = $this->Plants->get($plant_id);
            if (!$this->Plants->delete($plant)) {
                $this->Flash->error(__('The plant could not be deleted. Please, try again.'));
                return $this->redirect($this->referer());
            }
        }
        $this->Flash->success(__('The plant has been deleted.'));
        return $this->redirect($this->referer());
    }

    public function moveToExistingBatch($plants = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        //        dd( $this->request->data );
        //        echo 'hello world';
        //        dd($plants);
    }

    public function moveToNewBatch($plants = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $this->render('HarvestBatches/add');
        $plants = $this->request->data['plants'];
        $this->set(compact('plants'));
        $this->set('_serialize', ['plants']);
    }
}
