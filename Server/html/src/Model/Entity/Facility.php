<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Facility Entity
 *
 * @property int $id
 * @property string $name
 * @property string $street_address
 * @property float $latitude
 * @property float $longitude
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $status
 * @property \Cake\I18n\FrozenTime $deleted
 * @property int $owner_type
 * @property int $owner_id
 *
 * @property \App\Model\Entity\Owner $owner
 * @property \App\Model\Entity\Floorplan[] $floorplans
 */
class Facility extends Entity
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
        'name' => true,
        'street_address' => true,
        'latitude' => true,
        'longitude' => true,
        'created' => true,
        'modified' => true,
        'status' => true,
        'deleted' => true,
        'owner_type' => true,
        'owner_id' => true,
        'owner' => true,
        'floorplans' => true
    ];
}
