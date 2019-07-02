<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Device Entity
 *
 * @property int $id
 * @property string $label
 * @property string $mac
 * @property string $ip
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property \Cake\I18n\FrozenTime $last_message
 * @property \Cake\I18n\FrozenTime $deleted
 * @property \Cake\I18n\Time $deleted_date
 * @property int $refresh_rate
 *
 * @property \App\Model\Entity\Datapoint[] $datapoints
 * @property \App\Model\Entity\Output[] $outputs
 * @property \App\Model\Entity\Raw[] $raw
 * @property \App\Model\Entity\Sensor[] $sensors
 * @property int $map_item_id
 * @property string $version
 * @property int $api_id
 */
class Device extends Entity
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
