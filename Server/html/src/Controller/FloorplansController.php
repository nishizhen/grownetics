<?php
namespace App\Controller;

use App\Controller\AppController;

use Cake\Cache\Cache;
use Cake\Event\Event;
use Cake\I18n\Number;
use Cake\ORM\TableRegistry;
use Migrations\Migrations;

use Cake\Log\Log;


/**
 * Floorplans Controller
 *
 * @property \App\Model\Table\FloorplansTable $Floorplans
 */
class FloorplansController extends AppController
{

  public function beforeFilter(Event $event)
  {
      $this->getEventManager()->off($this->Csrf);
  }

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $this->paginate = [];
        $floorplans = $this->paginate($this->Floorplans);

        $this->set(compact('floorplans'));
        $this->set('_serialize', ['floorplans']);
    }

    /**
     * View method
     *
     * @param string|null $id Floorplan id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $this->loadModel("Plants");
        $this->loadModel("MapItems");
        // $this->loadModel("Tasks");
        $this->loadModel("MapItemTypes");

        $activePlants = $this->Plants->find(
            'all',
            [
                'conditions' => ['Plants.map_item_id >' => 0, 'Plants.status' => 1],
                'contain' => ['MapItems', 'Zones']
            ]
        )->toArray();
        $this->set('plantsData', $activePlants);

        $floorplan = $this->Floorplans->get($id, [
            'contain' => ['MapItems.MapItemTypes']
        ]);

        $this->set(compact('floorplan'));
        //$this->set('_serialize', ['floorplan']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $floorplan = $this->Floorplans->newEntity();
        if ($this->request->is('post')) {
            $data = $this->request->data;
            $floorplan = $this->Floorplans->patchEntity($floorplan, $data);
            $floorplan->latitude = Number::format($data['latitude'], ['precision' => 16]);
            $floorplan->longitude = Number::format($data['longitude'], ['precision' => 16]);

            if ($this->Floorplans->save($floorplan)) {
                $this->Flash->success(__('The floorplan has been saved.'));

                $table = TableRegistry::get("Zones");
                $zoneData =  $this->rollupGeoJSON('zones', $floorplan);
                $zoneEntities = $table->newEntities($zoneData, ['validate' => false]);
                foreach ($zoneEntities as $entity) {
                    $entity->dontMap = true;
                }
                if (!$table->saveMany($zoneEntities)) {
                    $this->log("Failed to save Zones", "debug");
                    $this->log($table->validator("default")->errors($zoneEntities), "debug");
                }
            } else {
                $this->Flash->error(__('The floorplan could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('floorplan'));
        $this->set('_serialize', ['floorplan']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Floorplan id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $floorplan = $this->Floorplans->get($id, [
            'contain' => ['MapItems.MapItemTypes']
        ]);

        $this->loadModel("MapItemTypes");
        $mapItemTypes = $this->MapItemTypes->find('all');

        if ($this->request->is(['patch', 'post', 'put'])) {
            $floorplan = $this->Floorplans->patchEntity($floorplan, $this->request->data);
            if ($this->Floorplans->save($floorplan)) {
                $this->Flash->success(__('The floorplan has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The floorplan could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('floorplan', 'mapItemTypes'));
        //$this->set('_serialize', ['floorplan']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Floorplan id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $floorplan = $this->Floorplans->get($id);
        if ($this->Floorplans->delete($floorplan)) {
            $this->Flash->success(__('The floorplan has been deleted.'));
        } else {
            $this->Flash->error(__('The floorplan could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function layers($layer = null, $id = null)
    {
        if ($this->request->is('post')) {
            $table = null;
            $floorplan = null;

            //            if ($id == 0) {
            $floorplan = $this->Floorplans->find()->last();
            //            }


            if ($layer == 'appliances') {
                $this->log("Saving Appliances...", "debug");
                $table = TableRegistry::get("Appliances");
            } elseif ($layer == "devices") {
                $this->log("Saving Devices...", "debug");
                $table = TableRegistry::get("Devices");
            } elseif ($layer == "map_items") {
                $this->log("Saving Map Items...", "debug");
                $table = TableRegistry::get("MapItems");
            } elseif ($layer == "plant_zones") {
                $this->log("Saving Plant Zones", "debug");
                $table = TableRegistry::get("Zones");
            }

            if ($layer != "plant_placeholders") {
                $entityData = $this->rollupGeoJSON($layer, $floorplan);
            } elseif ($layer == "plant_placeholders") {
                $this->log("Saving Plant Placeholders...", "debug");
                $this->loadModel("MapItemTypes");

                $mapItemType = $this->MapItemTypes->find()->where(['label' => 'Plant Placeholder'])->first();

                if (!isset($mapItemType)) {
                    $mapItemType = $this->MapItemTypes->newEntity([
                        'label' => 'Plant Placeholder',
                        'opacity' => 1
                    ]);
                    if (!$this->MapItemTypes->save($mapItemType)) {
                        //     $this->log('creating new map item type => '.$mapItem->type, 'debug');
                        // } else {
                        $this->log($mapItemType->errors(), 'debug');
                    }
                }

                $plantPlaceholder_map = function ($entity, $geoJSON) use ($floorplan) {
                    $entity['geoJSON'] = json_encode($geoJSON);
                    $entity['floorplan_id'] = $floorplan->id;
                    $entity['type'] = 'Plant Placeholder';
                    return $entity;
                };

                $plantPlaceholders = [];
                $plantPlaceholderGeoJSON = [];
                $pZones = array_values(json_decode($this->request->getData('plant_placeholders'), true));
                $pGeoZones = array_values(json_decode($this->request->getData('plant_placeholders_geoJSON'), true));

                for ($i = 0; $i < count($pZones); $i++) {
                    $plantPlaceholders = array_merge($plantPlaceholders, $pZones[$i]);
                    $plantPlaceholderGeoJSON = array_merge($plantPlaceholderGeoJSON, $pGeoZones[$i]);
                }

                $entityData = array_map(
                    $plantPlaceholder_map,
                    $plantPlaceholders,
                    $plantPlaceholderGeoJSON
                );

                $table = TableRegistry::get("MapItems");
                $Zones = TableRegistry::get("Zones");

                # Attach zone_ids
                $formattedEntites = [];
                foreach($entityData as $entity) {
                    $entity['zone_id'] = preg_replace('/_/', ' ', $entity['zone_id']);
                    if (isset($entity['zone_id']) && is_string($entity['zone_id'])) {
                        $zone_name = str_replace("-"," ",$entity['zone_id']);
                        $zoneEntity = $Zones->find()->where(['label' => $zone_name])->first();
                        if (isset($zoneEntity)) {
                            $entity['zone_id'] = $zoneEntity->id;
                        }
                    }
                    $formattedEntites[] = $entity;
                }
                $entityData = $formattedEntites;
            }
            //FIXME: re-enable validation for zones
            $entities = $table->newEntities($entityData,  ['validate' => false]);
            if ($layer == 'plant_zones') {
                foreach ($entities as $entity) {
                    $entity->dontMap = true;
                }
            }
            if (!$table->saveMany($entities)) {
                $this->log("Failed to save entities for: " . $layer, "debug");
                $this->log($table->validator("default")->errors($entities), "debug");
            } else {
              $this->log("Saved entities for: " . $layer, "debug");
            }
            if ($layer == "devices") {
                $this->loadModel("Rules");
                $this->loadModel("SetPoints");
                $this->Rules->generateFromDefaultRules();
                // $this->SetPoints->generateFromDefaultSetPoints();
            }

            Cache::clear(false);

            //            $this->set('_serialize', compact('entities'));

        } else { // Request is not a POST
            $this->floorplan_id = $id;

            if ($layer == "walls") {
                if (($walls = Cache::read('floorplan_walls_json_decoded')) === false) {
                    $walls = json_decode($this->Floorplans->get($id)->geoJSON);
                    Cache::write('floorplan_walls_json_decoded', $walls);
                }
                $entities = $walls;
            } else if ($layer == "plant_placeholders") {
                $this->loadModel("MapItems");
                $this->loadModel("MapItemTypes");
                $plant_placeholder_map_item_type = $this->MapItemTypes->findByLabel('Plant Placeholder')->first();
                //https://www.geekality.net/2011/10/31/php-simple-compression-of-json-data/
                // Compress the sent JSON object
                ob_start('ob_gzhandler');

                $query = $this->MapItems->find('all', [
                    'contain' => [
                        'MapItemTypes'
                    ],
                    'conditions' => [
                        'MapItems.floorplan_id' => $this->floorplan_id,
                        'MapItems.map_item_type_id' => $plant_placeholder_map_item_type->id
                    ],
                    'fields' => [
                        'MapItemTypes.label',
                        'MapItems.latitude',
                        'MapItems.longitude',
                        'MapItems.label',
                        'MapItems.geoJSON'
                    ]
                ])->cache('floorplan_plant_placeholders');
                if (($plant_placeholders = Cache::read('floorplan_plant_placeholders_json_decoded')) === false) {
                    $plant_placeholders = $query->toArray();
                    foreach ($plant_placeholders as $plant_placeholder) {
                        $plant_placeholder->geoJSON = json_decode($plant_placeholder->geoJSON);
                    }
                    Cache::write('floorplan_plant_placeholders_json_decoded', $plant_placeholders);
                }
                $entities = $plant_placeholders;
            } else if ($layer == "map_items") {
                $this->loadModel("MapItems");
                $this->loadModel("MapItemTypes");
                $plant_placeholder_map_item_type = $this->MapItemTypes->findByLabel('Plant Placeholder')->first();
                $sensor_map_item_type = $this->MapItemTypes->findByLabel('Sensor')->first();
                $device_map_item_type = $this->MapItemTypes->findByLabel('Device')->first();
                $query = $this->MapItems->find('all', [
                    'contain' => [
                        'MapItemTypes' => function ($q) {
                            return $q->select([
                                'MapItemTypes.label',
                                'MapItems.latitude',
                                'MapItems.longitude',
                                'MapItems.label',
                                'MapItems.geoJSON'
                            ]);
                        },
                        'Zones' => function ($q) {
                            return $q->select([
                                'Zones.zone_type_id'
                            ]);
                        }
                    ],
                    'conditions' => [
                        'MapItems.floorplan_id' => $this->floorplan_id,
                        // Only load Zones, Appliances (server switches, power panels), Doors, and Room Names
                        'MapItems.map_item_type_id NOT IN' => [$plant_placeholder_map_item_type->id,  $sensor_map_item_type->id,  $device_map_item_type->id]
                    ]
                ])->cache('floorplan_map_items');
                if (($map_items = Cache::read('floorplan_map_items_json_decoded')) === false) {
                    $map_items = $query->toArray();
                    foreach ($map_items as $map_item) {
                        $map_item->geoJSON = json_decode($map_item->geoJSON);
                    }
                    Cache::write('floorplan_map_items_json_decoded', $map_items);
                }
                $entities = $map_items;
            } else if ($layer == "sensors") {
                $this->loadModel("Sensors");

                $query = $this->Sensors->find('all', [
                    'contain' => [
                        'MapItems'
                    ],
                    'fields' => [
                        'Sensors.id',
                        'Sensors.sensor_type_id',
                        'MapItems.label',
                        'MapItems.offsetHeight',
                        'MapItems.longitude',
                        'MapItems.latitude',
                        'MapItems.geoJSON'
                    ],
                    'conditions' => [
                        'MapItems.floorplan_id' => $this->floorplan_id
                    ]
                ]);

                if (($sensorData = Cache::read('floorplan_sensors_json_decoded')) === false) {
                    $sensorData = $query->toArray();
                    $processed = [];
                    foreach ($sensorData as $sensor) {
                        $sensor->map_item->geoJSON = json_decode($sensor->map_item->geoJSON);
                        $sensor->data_type = $this->Sensors->getDataTypeFromSensorType($sensor->sensor_type_id);
                        $sensor->sensor_type_label = $this->Sensors->enumKeyToValue('sensor_type', $sensor->sensor_type_id);
                        $sensor->sensor_type_symbol = $this->Sensors->enumKeyToValue('sensor_symbols', $sensor->sensor_type_id);
                        $sensor->sensor_type_metric_symbol = $this->Sensors->enumKeyToValue('sensor_metric_symbols', $sensor->sensor_type_id);
                        Log::write("debug", $sensor);
                        // unset($sensor->_matchingData);
                        array_push($processed,$sensor);
                    }
                    Cache::write('floorplan_sensors_json_decoded', $sensorData);
                }
                $entities = $processed;
            } else if ($layer == "plants") {
                $this->loadModel("Plants");

                $query = $this->Plants->find('all', [
                    'conditions' => ['Plants.status' => $this->Plants->enumValueToKey('status', 'Planted')],
                    'contain' => ['HarvestBatches', 'HarvestBatches.Cultivars', 'MapItems', 'Zones'],
                    'fields' => [
                        'Plants.plant_id',
                        'Plants.status',
                        'MapItems.geoJSON',
                        'MapItems.latitude',
                        'MapItems.longitude',
                        'MapItems.label',
                        'Plants.harvest_batch_id',
                        'HarvestBatches.batch_number',
                        'Cultivars.label',
                        'Zones.label',
                        'Zones.id'
                    ]
                ])->cache('floorplan_plants');

                if (($activePlants = Cache::read('floorplan_plants_json_decoded')) === false) {
                    $activePlants = $query->toArray();
                    foreach ($activePlants as $activePlant) {
                        $activePlant->map_item->geoJSON = json_decode($activePlant->map_item->geoJSON);
                    }
                    Cache::write('floorplan_plants_json_decoded', $activePlants);
                }
                $entities = $activePlants;
            }

            $this->set(compact('entities'));
            $this->set('_serialize', ['entities']);
        }
    }

    private function rollupGeoJSON($dataAttr, $floorplan)
    {
        $geoJSON_map = function ($entity, $geoJSON) use ($floorplan) {
            $entity['geoJSON'] = json_encode($geoJSON);
            $entity['floorplan_id'] = $floorplan->id;
            return $entity;
        };

        $entities = array_map(
            $geoJSON_map,
            json_decode($this->request->getData($dataAttr), true),
            json_decode($this->request->getData($dataAttr . "_geoJSON"), true)
        );

        return $entities;
    }

    public function clearImport()
    {
        $this->request->allowMethod(['post']);
        # Delete everything created in the add function above to allow for easy testing and re-imports/fixes
        $this->Floorplans->clearImport();

        $this->Flash->success(__('The floorplan has been deleted.'));
        return $this->redirect(['controller' => 'Dash', 'action' => 'index']);
    }

    public function importDemo()
    {
        $this->request->allowMethod(['post']);

        $migrations = new Migrations();
        $seeded = $migrations->seed(['source' => 'DemoSeeds']);

        $this->Flash->success(__('Demo floorplan imported successfully.'));
        return $this->redirect(['controller' => 'Dash', 'action' => 'index']);
    }
}
