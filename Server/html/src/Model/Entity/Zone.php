<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;

/**
 * Zone Entity
 *
 * @property int $id
 * @property string $label
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $status
 * @property \Cake\I18n\FrozenTime $deleted
 * @property \Cake\I18n\Time $deleted_date
 *
 * @property \App\Model\Entity\Datapoint[] $datapoints
 * @property \App\Model\Entity\Output[] $outputs
 * @property \App\Model\Entity\Sensor[] $sensors
 * @property int $map_item_id
 * @property int $zone_type_id
 * @property int $plant_zone_type_id
 */
class Zone extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false
    ];

    protected function _getZoneTasks() {
        $this->Tasks = TableRegistry::get('Tasks');
        $tasks = $this->Tasks->find('all', ['conditions' => ['zone_id' => $this->_properties['id'], 'harvestbatch_id IS NULL']])->toArray();
        if ($tasks != []) {
            return true;
        } else {
            return false;
        }

    }

    public function getActiveBatches() {
        $this->HarvestBatches = TableRegistry::get('HarvestBatches');
        $batches = $this->HarvestBatches->find('all', ['conditions' => ['zone_id' => $this->_properties['id'], 'status' => $this->HarvestBatches->enumValueToKey('status', 'Active')]])->toArray();
        return $batches;
    }

    protected function _getAvailablePlantPlaceholders()
    {
        $this->Zones = TableRegistry::get('Zones');
        $this->Plants = TableRegistry::get('Plants');
        $this->MapItems = TableRegistry::get('MapItems');
        $this->MapItemTypes = TableRegistry::get('MapItemTypes');
       
        $zone = $this->_properties;
        $benchIds = [];

        $roomHasBenches = true;

        if ($zone['room_zone_id'] == 0) {
            $roomBenchIds = $this->Zones->find('all', 
            [
                'conditions' => 
                [
                    'room_zone_id' => $zone['id'], 
                    'zone_type_id' => $this->Zones->enumValueToKey('zone_types', 'Group')
                ], 
                'fields' => ['id']
            ]
            )->toArray();
        } else {
            $roomBenchIds = $this->Zones->find('all', ['conditions' => ['id' => $zone['id']]])->toArray();
        }


        foreach($roomBenchIds as $bench) {
            array_push($benchIds, $bench->id);
        }
        //if no benches in room, use Room's zone_id
        if ($benchIds == []) {
            $roomHasBenches = false;
            array_push($benchIds, $zone['id']);
        }

        $currPlantsInZone = $this->Plants->find('all',
        [
            'conditions' => ['zone_id IN' => $benchIds],
            'fields' => ['map_item_id']
        ])->toArray();
        
        $plantedPlants = [];
        //check against ALL plant place holders in room.
        if ($currPlantsInZone) {
            foreach ($currPlantsInZone as $currPlantInZone) {
                array_push($plantedPlants, $currPlantInZone->map_item_id);
            }
            $plant_placeholders = $this->MapItems->find('all', 
            ['conditions' => 
                ['id NOT IN' => $plantedPlants, 'map_item_type_id' => $this->MapItemTypes->find()->select('id')->where(['label' => 'Plant Placeholder']), 'zone_id IN' => $benchIds],
                'order' => ['id' => 'ASC']
            ])->toArray();
        } else {
            $plant_placeholders = $this->MapItems->find('all', 
            ['conditions' => 
                ['map_item_type_id' => $this->MapItemTypes->find()->select('id')->where(['label' => 'Plant Placeholder']), 'zone_id IN' => $benchIds],
                'order' => ['id' => 'ASC']
            ])->toArray();
        }
        return ['plant_placeholders' => $plant_placeholders, 'roomHasBenches' => $roomHasBenches];   
    }
}
