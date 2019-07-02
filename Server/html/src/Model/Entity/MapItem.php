<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * MapItem Entity
 *
 * @property int $id
 * @property float $latitude
 * @property float $longitude
 * @property string $geoJSON
 * @property float $offsetHeight
 * @property string $label
 * @property int $floorplan_id
 * @property int $mapitemtype_id
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property \Cake\I18n\FrozenTime $deleted
 * @property \Cake\I18n\Time $deleted_date
 * @property \App\Model\Entity\Floorplan $floorplan
 * @property \App\Model\Entity\MapItemType $mapitemtype
 * @property int $map_item_type_id
 * @property int $ordinal
 * @property int $zone_id
 */
class MapItem extends Entity
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
