<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * SetPoint Entity
 *
 * @property int $id
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property \Cake\I18n\FrozenTime $deleted
 * @property string $label
 * @property string $value
 * @property int $target_type
 * @property int $target_id
 * @property int $data_type
 *
 * @property \App\Model\Entity\Target $target
 * @property int $status
 */
class SetPoint extends Entity
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
