<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Output Entity
 *
 * @property int $id
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $status
 * @property string $label
 * @property string $output_target
 * @property int $output_type
 * @property int $device_id
 * @property int $zone_id
 * @property \Cake\I18n\FrozenTime $deleted
 * @property \Cake\I18n\Time $deleted_date
 *
 * @property \App\Model\Entity\Device $device
 * @property \App\Model\Entity\Zone $zone
 * @property \App\Model\Entity\Rule[] $rules
 */
class Output extends Entity
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
