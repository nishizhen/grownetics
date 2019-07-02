<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * RecipeEntry Entity
 *
 * @property int $id
 * @property int $zone_id
 * @property int $recipe_id
 * @property int $days
 * @property \Cake\I18n\FrozenTime $deleted
 * @property \Cake\I18n\Time $deleted_date
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Zone $zone
 * @property \App\Model\Entity\Recipe $recipe
 * @property \App\Model\Entity\BatchRecipeEntry[] $batch_recipe_entries
 * @property int $zone_type_id
 */
class RecipeEntry extends Entity
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
