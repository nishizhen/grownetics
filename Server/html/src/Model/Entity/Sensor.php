<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Sensor Entity
 *
 * @property int $id
 * @property string $sensor_pin
 * @property int $device_id
 * @property string $label
 * @property int $zone_id
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $status
 * @property \Cake\I18n\FrozenTime $deleted
 * @property \Cake\I18n\Time $deleted_date
 * @property \Cake\I18n\FrozenTime $last_good_data_time
 * @property string $last_good_data
 * @property float $calibration
 *
 * @property \App\Model\Entity\SensorType $sensor_type

 * @property \App\Model\Entity\Device $device
 * @property \App\Model\Entity\Zone $zone
 * @property int $sensor_type_id
 * @property int $map_item_id
 */
class Sensor extends Entity
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

    # Return an end-user friendly version of the sensor name
    protected function _getFriendlyLabel() {
        return $this->_properties['name'] . ' High Co2';
    }
}
