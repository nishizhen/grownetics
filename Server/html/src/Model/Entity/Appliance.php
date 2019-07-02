<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Appliance Entity
 *
 * @property int $id
 * @property string $label
 * @property int $output_id
 * @property int $appliance_type_id
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Output $output
 * @property \App\Model\Entity\ApplianceTemplate $appliance_type
 * @property int $appliance_template_id
 * @property \Cake\I18n\FrozenTime $deleted
 */
class Appliance extends Entity
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
