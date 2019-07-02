<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\I18n\Time;
use Cake\Chronos\Chronos;
use Cake\ORM\TableRegistry;
use App\Lib\SystemEventRecorder;

/**
 * Task Entity
 *
 * @property int $id
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property \Cake\I18n\FrozenTime $deleted
 * @property \Cake\I18n\Time $deleted_date
 * @property string $label
 * @property int $status
 * @property int $harvestbatch_id
 *
 * @property \App\Model\Entity\Harvestbatch $harvestbatch
 * @property \Cake\I18n\FrozenTime $due_date
 * @property \Cake\I18n\FrozenTime $completed_date
 * @property string $assignee
 * @property int $batch_recipe_entry_id
 * @property int $zone_id
 * @property int $type
 * @property int $zone_type_id
 */
class Task extends Entity
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

    protected function _getDaysUntilDue()
    {
        $dueDate = $this->_properties['due_date'];
        $today = Chronos::today();
        if (!isset($dueDate)) {
            return false;
        }
        $diffDate = $dueDate->diff($today);
        if (strtotime($dueDate) < strtotime($today)) {
            return false;
        }
        return $diffDate->days;
    }

    public function getZone($zone_id)
    {
        $this->Zones = TableRegistry::get('Zones');
        $zone = $this->Zones->get($zone_id);
        return $zone;
    }

    public function markCompleted($userId, $userName)
    {
        $this->harvestBatches = TableRegistry::get('HarvestBatches');
        $this->Plants = TableRegistry::get('Plants');
        $this->Zones = TableRegistry::get('Zones');
        $this->MapItems = TableRegistry::get('MapItems');
        $this->MapItemTypes = TableRegistry::get('MapItemTypes');
        $this->BatchRecipeEntries = TableRegistry::get('BatchRecipeEntries');
        $this->notifications = TableRegistry::get('Notifications');
        $this->RuleActions = TableRegistry::get('RuleActions');
        $this->Tasks = TableRegistry::get('Tasks');

        if ($this->zone_id != 0) {
            $taskZone = $this->Zones->get($this->zone_id);
        }

        # Get the batch info so we can move the plants
        $batch = $this->harvestBatches->get($this->harvestbatch_id, ['contain' => 'Cultivars']);
        if ($taskZone) {
            $batch->zone_id = $taskZone->id;
        }
        if ($batch->plant_count == 0) {
            throw new Exception('Create at least 1 Plant for this Batch before trying to plant or move it.');
        }
        # Get all the plants for this batch                
        $plants = $this->Plants->find('notDestroyed', ['batchId' => $batch->id])->toArray();

        # Get the previously completed move task, so that batches can be flagged active
        # from a planned state, on the first move action.
        $removeTask = $this->Tasks->find('all', ['conditions' => ['harvestbatch_id' => $this->harvestbatch_id, 'type' => $this->Tasks->enumValueToKey('type', 'Harvest')]])->first();
        $previousCompletedMoveTask = $this->Tasks->find('all', [
            'conditions' => [
                'status' => $this->Tasks->enumValueToKey('status', 'Completed'),
                'type' => $this->Tasks->enumValueToKey('type', 'Move'),
                'harvestbatch_id' => $this->harvestbatch_id
            ],
        ])->first();
        $ii = 0;
        if ($this->id == $removeTask->id) {
            $batch->harvest_date = Chronos::now();
            $batch->status = $this->harvestBatches->enumValueToKey('status', 'Harvested');
            $batch->zone_id = NULL;
            $this->harvestBatches->save($batch);
            while ($ii < sizeof($plants)) {
                $plants[$ii]->map_item_id = 0;
                $plants[$ii]->zone_id = 0;
                $plants[$ii]->status = $this->Plants->enumValueToKey('status', 'Harvested');
                $this->Plants->save($plants[$ii]);
                $ii++;
            }
        } else {
            if (!$previousCompletedMoveTask) {
                $batch->status = $this->harvestBatches->enumValueToKey('status', 'Active');
                $batch->planted_date = Chronos::now();
                $this->harvestBatches->save($batch);
            }

            if ($this->type == $this->Tasks->enumValueToKey('type', 'Move') || $this->type == $this->Tasks->enumValueToKey('type', 'Harvest')) {
                $this->Plants->movePlantsToZone($plants, $taskZone);
            }
        }

        $this->completed_date = new Time();
        $this->assignee = $userId;
        $this->status = $this->Tasks->enumValueToKey('status', 'Completed');
        $this->Tasks->save($this);

        if ($this->type == $this->Tasks->enumValueToKey('type', 'Move')) {
            $batch->zone_id = $taskZone->id;
            $this->harvestBatches->save($batch);
        }

        if (isset($this->batch_recipe_entry_id) && $this->batch_recipe_entry_id != 0) {
            $task_batch_recipe_entry = $this->BatchRecipeEntries->get($this->batch_recipe_entry_id);
            $task_batch_recipe_entry->start_date = $this->completed_date;
            $this->BatchRecipeEntries->save($task_batch_recipe_entry);
        } else {
            // Create BRE for manually created Task
            $breData = $this->BatchRecipeEntries->newEntity();
            $breData->created = new Time();
            $breData->batch_id = $this->harvestbatch_id;
            $breData->task_id = $this->id;
            $breData->planned_start_date = $this->due_date;
            $breData->planned_end_date = $this->due_date;
            $breData->zone_id = $batch->zone_id;
            $this->BatchRecipeEntries->save($breData);
        }

        $recorder = new SystemEventRecorder();
        $recorder->recordEvent('user_actions', 'task_completed', $this->assignee, ['user_id' => $this->assignee, 'name' => $userName, 'source_type' => $this->notifications->enumValueToKey('source_type', 'Admin'), 'source_id' => $userId]);

        $notification = $this->notifications->newEntity([
            'message' => env('FACILITY_ID') . '-' . env('FACILITY_NAME') . ' ' . "Task: '" . $this->label . "' completed by: " . $userName,
            'status' => 0,
            'template' => '',
            'user_id' => $userId,
            'source_type' => $this->notifications->enumValueToKey('source_type', 'Admin'),
            'source_id' => $this->harvestbatch_id,
            'notification_level' => $this->RuleActions->enumValueToKey('notification_level', 'Dashboard Notification')
        ]);
        $this->notifications->save($notification);
    }
}
