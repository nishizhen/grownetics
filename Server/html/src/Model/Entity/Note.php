<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Note Entity
 *
 * @property int $id
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $user_id
 * @property int $batch_id
 * @property string $note
 * @property \Cake\I18n\FrozenTime $deleted
 * @property int $cultivar_id
 * @property int $zone_id
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Batch $batch
 * @property \App\Model\Entity\Cultivar $cultivar
 * @property \App\Model\Entity\Zone $zone
 * @property \App\Model\Entity\Photo[] $photos
 * @property \App\Model\Entity\Plant[] $plants
 */
class Note extends Entity
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
