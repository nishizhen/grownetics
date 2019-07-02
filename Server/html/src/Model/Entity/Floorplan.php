<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Floorplan Entity
 *
 * @property int $id
 * @property string $name
 * @property int $facility_id
 * @property int $floor_level
 * @property int $square_footage
 * @property string $description
 * @property string $floorplan_image
 * @property float $latitude
 * @property float $longitude
 * @property int $offsetAngle
 * @property string $geoJSON
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $status
 * @property \Cake\I18n\FrozenTime $deleted
 * @property \Cake\I18n\Time $deleted_date
 *
 * @property \App\Model\Entity\Facility $facility
 * @property string $label
 */
class Floorplan extends Entity
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
