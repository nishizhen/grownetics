<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * SensorType Entity
 *
 * @property int $id
 * @property int $sensor_type
 * @property string $description
 * @property string $calibration_operator
 * @property float $calibration_operand
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\Time $deleted_date
 * @property string $label
 * @property \Cake\I18n\FrozenTime $deleted
 * @property string $symbol
 * @property string $display_class
 * @property string $metric_symbol
 */
class SensorType extends Entity
{
    public $enums = [
        'calibration_operator' => [
            'add',
            'multiply'
        ]
    ];

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
