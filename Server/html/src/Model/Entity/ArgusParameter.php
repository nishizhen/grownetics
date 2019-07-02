<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ArgusParameter Entity
 *
 * @property int $id
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property \Cake\I18n\FrozenTime $deleted
 * @property int $status
 * @property int $argus_parameter_id
 * @property string $label
 *
 * @property \App\Model\Entity\ArgusParameter[] $argus_parameters
 */
class ArgusParameter extends Entity
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
        'created' => true,
        'modified' => true,
        'deleted' => true,
        'status' => true,
        'argus_parameter_id' => true,
        'label' => true,
        'argus_parameters' => true
    ];
}
