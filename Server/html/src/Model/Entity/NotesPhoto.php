<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * NotesPhoto Entity
 *
 * @property int $id
 * @property int $note_id
 * @property int $photo_id
 * @property int $owner_type
 * @property int $owner_id
 *
 * @property \App\Model\Entity\Note $note
 * @property \App\Model\Entity\Photo $photo
 * @property \App\Model\Entity\Owner $owner
 */
class NotesPhoto extends Entity
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
        'photo_id' => true,
        'owner_type' => true,
        'owner_id' => true,
        'note' => true,
        'photo' => true,
        'owner' => true
    ];
}
