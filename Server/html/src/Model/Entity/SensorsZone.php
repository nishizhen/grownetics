<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * SensorsZone Entity
 *
 * @property int $id
 * @property int $sensor_id
 * @property int $zone_id
 *
 * @property \App\Model\Entity\Sensor $sensor
 * @property \App\Model\Entity\Zone $zone
 * @property \Cake\I18n\FrozenTime $deleted
 */
class SensorsZone extends Entity
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
