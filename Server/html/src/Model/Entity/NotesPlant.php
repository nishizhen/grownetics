<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * NotesPlant Entity
 *
 * @property int $id
 * @property int $note_id
 * @property int $plant_id
 *
 * @property \App\Model\Entity\Note $note
 * @property \App\Model\Entity\Plant $plant
 */
class NotesPlant extends Entity
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
        'note_id' => true,
        'plant_id' => true,
        'note' => true,
        'plant' => true
    ];
}
