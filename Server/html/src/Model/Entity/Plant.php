<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;

/**
 * Plant Entity
 *
 * @property int $id
 * @property string $plant_id
 * @property int $short_plant_id
 * @property int $zone_id
 * @property int $map_item_id
 * @property int $status
 * @property int $harvest_batch_id
 * @property int $recipe_id
 * @property \Cake\I18n\FrozenTime $deleted
 * @property float $wet_whole_weight
 * @property float $wet_waste_weight
 * @property float $wet_whole_defoliated_weight
 * @property int $cultivar_id
 *
 * @property \App\Model\Entity\MapItem $map_item
 * @property \App\Model\Entity\HarvestBatch $harvest_batch
 * @property \App\Model\Entity\Recipe $recipe
 * @property \App\Model\Entity\Task $task
 * @property \App\Model\Entity\Zone $zone
 * @property \App\Model\Entity\Note[] $notes
 * @property \App\Model\Entity\Cultivar $cultivar
 */
class Plant extends Entity
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
        'plant_id' => true,
        'short_plant_id' => true,
        'zone_id' => true,
        'map_item_id' => true,
        'status' => true,
        'harvest_batch_id' => true,
        'recipe_id' => true,
        'deleted' => true,
        'wet_whole_weight' => true,
        'wet_waste_weight' => true,
        'wet_whole_defoliated_weight' => true,
        'cultivar_id' => true,
        'map_item' => true,
        'harvest_batch' => true,
        'recipe' => true,
        'task' => true,
        'zone' => true,
        'notes' => true,
        'cultivar' => true
    ];

    public function markPlantDestroyed($plant, $user_id, $batch_id) 
    {
        $this->Plants = TableRegistry::get('Plants');
        $plant->status = $this->Plants->enumValueToKey('status', 'Destroyed');
        $plant->map_item_id = 0;
        $this->Plants->save($plant);

        $this->Notifications = TableRegistry::get('Notifications');
        $notif = $this->Notifications->newEntity(); 
        $notif->message = "Destroyed plant #".$plant->short_plant_id.".";
        $notif->notification_level = 0;
        $notif->source_id = $plant->id;
        $notif->user_id = $user_id;
        $this->Notifications->save($notif);
        $this->Plants->updateShortPlantIds($batch_id);

        return true;
    }

    public function getZone($zone_id) {
        $this->Zones = TableRegistry::get('Zones');
        $zone = $this->Zones->get($zone_id);
        return $zone;
    }

    public function changeBatchId($new_batch_id) {
        $this->Plants = TableRegistry::get('Plants');
        $this->harvest_batch_id = (int)$new_batch_id;
        if ($this->Plants->save($this)) {
            return true;
        } else {
            return false;
        }
    }
}
