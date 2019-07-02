<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * HarvestBatchLog Entity
 *
 * @property int $id
 * @property string $label
 * @property string $description
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property string $zone_id
 * @property string $batch_id
 * @property string $entry_date
 *
 * @property \App\Model\Entity\Zone $zone
 * @property \App\Model\Entity\Batch $batch
 * @property \Cake\I18n\FrozenTime $deleted
 */
class HarvestBatchLog extends Entity
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
}
