<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * MapItemsZone Entity
 *
 * @property int $id
 * @property int $map_item_id
 * @property int $zone_id
 * @property int $owner_type
 * @property int $owner_id
 *
 * @property \App\Model\Entity\MapItem $map_item
 * @property \App\Model\Entity\Zone $zone
 * @property \App\Model\Entity\Owner $owner
 */
class MapItemsZone extends Entity
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
        'map_item_id' => true,
        'zone_id' => true,
        'owner_type' => true,
        'owner_id' => true,
        'map_item' => true,
        'zone' => true,
        'owner' => true
    ];
}
