<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use Cake\I18n\Time;
use Cake\Chronos\Chronos;

/**
 * HarvestBatch Entity
 *
 * @property int $id
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $cultivar_id
 * @property \Cake\I18n\FrozenDate $planted_date
 * @property \Cake\I18n\FrozenDate $harvest_date
 * @property \Cake\I18n\FrozenTime $ship_date
 * @property int $status
 * @property string $short_desc
 * @property string $long_desc
 * @property float $estimated_amount
 * @property float $harvested_amount
 * @property float $available_amount
 * @property float $price
 * @property string $photo
 * @property \Cake\I18n\FrozenTime $deleted
 * @property \Cake\I18n\Time $deleted_date
 * @property int $recipe_id
 *
 * @property \App\Model\Entity\Cultivar $cultivar
 * @property \App\Model\Entity\Recipe $recipe
 * @property \App\Model\Entity\BatchNote[] $batch_notes
 * @property int $batch_number
 */
class HarvestBatch extends Entity
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

    protected function _getPlannedHarvestDate()
    {
        $this->Tasks = TableRegistry::get('Tasks');
        $harvest_date = $this->Tasks->find('all', 
            ['conditions' => ['harvestbatch_id' => $this->_properties['id'], 'batch_recipe_entry_id IS NOT' => 0], 'order' => ['id' => 'DESC']
            ])->first();
        return $harvest_date->due_date;
    }

    protected function _getPlantCount()
    {
        $this->Plants = TableRegistry::get('Plants');
        $plant_count = $this->Plants->find('all', [
            'conditions' => ['Plants.harvest_batch_id'=> $this->_properties['id'], 'status !=' => $this->Plants->enumValueToKey('status', 'Destroyed')]
        ])->count();
        return $plant_count;
    }

    protected function _getCurrentZone()
    {
        $this->Tasks = TableRegistry::get('Tasks');
        $recentCompletedTask = $this->Tasks->find('all', [
            'conditions' => [
                'Tasks.harvestbatch_id'=> $this->_properties['id'],
                'Tasks.status' => $this->Tasks->enumValueToKey('status', 'Completed'),
                'Tasks.type IN' => [$this->Tasks->enumValueToKey('type', 'Move'), $this->Tasks->enumValueToKey('type', 'Harvest')]
            ],
            'order' => ['Tasks.completed_date' => 'desc'],
            'contain' => ['Zones']
        ])->first();
        if ($recentCompletedTask) {
            return $recentCompletedTask->zone;
        } else {
            return false;
        }   
    }

    protected function _getCurrentRoomZone()
    {
        $this->Zones = TableRegistry::get('Zones');
        $currentZone = $this->_getCurrentZone();
        if ( $currentZone ) {
            if ($currentZone->zone_type_id == $this->Zones->enumValueToKey('zone_types', 'Group')) {
                return $this->Zones->get($currentZone->room_zone_id);
            } else {
                return $currentZone;
            }
        } else {
            return false;
        }
    }

    protected function _getNextTask()
    {
        $this->Tasks = TableRegistry::get('Tasks');
        $nextTask = $this->Tasks->find('all', [
            'conditions' => ['harvestbatch_id'=> $this->_properties['id'], 'Tasks.status' => 0],
            'order' => ['Tasks.due_date' => 'ASC'],
            'contain' => ['Zones']
        ])->first();
        return $nextTask;
    }

    protected function _getNextMoveTask()
    {
        $this->Tasks = TableRegistry::get('Tasks');
        $nextTask = $this->Tasks->find('all', [
            'conditions' => ['harvestbatch_id'=> $this->_properties['id'], 'Tasks.status' => $this->Tasks->enumValueToKey('status', 'Incomplete'), 'Tasks.type IN' => [$this->Tasks->enumValueToKey('type', 'Move'), $this->Tasks->enumValueToKey('type', 'Harvest')]],
            'order' => ['Tasks.due_date' => 'ASC'],
            'contain' => ['Zones']
        ])->first();
        return $nextTask;
    }

    protected function _setZoneTypes($zone_types) {
        $this->tmp_zone_types = $zone_types;
    }

    protected function _getZoneTypes() {
        return $this->tmp_zone_types;
    }

    protected function _getOptionsArray() {
        $this->batch_recipe_entries = TableRegistry::get('BatchRecipeEntries');
        $this->zones = TableRegistry::get('Zones');
        $options = array(
            'room_ids' => array(),
            'group_ids' => array(),
            'start_id' => "",
            'end_id' => "",
            'plant_list' => null
        );
        $entries = $this->batch_recipe_entries->find('all', [
            'conditions' => [
                'batch_id' => $this->id
            ],
            'order' => [
                'planned_start_date' => 'asc'
            ]
        ]);

        foreach($entries as $entry) {
            if ($entry->IsSubTaskRelated) {
                continue;
            }
            $zone = $this->zones->get($entry->zone_id);
            if ($zone->zone_type_id == $this->zones->enumValueToKey('zone_types', 'Room')) {
                array_push($options['room_ids'], $zone->id);
                array_push($options['group_ids'], "");
            } else if ($zone->zone_type_id == $this->zones->enumValueToKey('zone_types', 'Group')) {
                array_push($options['group_ids'], $zone->id);
                array_push($options['room_ids'], "");
            }
        }
        return $options;
    }

    protected function _setUpdatedTasksForNewBatch($new_batch_id) {
        $this->tasks = TableRegistry::get('Tasks');
        $old_batches_tasks = $this->tasks->find('all', ['conditions' => [
            'harvestbatch_id' => $this->id
        ]]);

        foreach($old_batches_tasks as $task) {
            if ($task->status == $this->tasks->enumValueToKey('status', 'Completed')) {
                $task_to_update = $this->tasks->find('all', ['conditions' => [
                    'harvestbatch_id' => $new_batch_id,
                    'zone_id' => $task->zone_id,
                    'type' => $task->type
                ]])->first();

                if ($task_to_update != null) {
                    $this->tasks->patchEntity($task_to_update, ['status' => $this->tasks->enumValueToKey('status', 'Completed'), 'completed_date' => $task->completed_date]);
                    $this->tasks->save($task_to_update);
                }
            }
        }
    }
}
