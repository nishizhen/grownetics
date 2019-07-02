<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;

/**
 * BatchRecipeEntry Entity
 *
 * @property int $id
 * @property int $zone_id
 * @property int $recipe_entry_id
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property \Cake\I18n\FrozenTime $deleted
 * @property \Cake\I18n\Time $deleted_date
 * @property \Cake\I18n\FrozenTime $planned_start_date
 * @property \Cake\I18n\FrozenTime $planned_end_date
 * @property \Cake\I18n\FrozenTime $start_date
 * @property \Cake\I18n\FrozenTime $end_date
 *
 * @property \App\Model\Entity\Zone $zone
 * @property \App\Model\Entity\RecipeEntry $recipe_entry
 * @property int $batch_id
 * @property int $recipe_id
 * @property int $zone_type_id
 */
class BatchRecipeEntry extends Entity
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

    protected function _getIsSubTaskRelated() {
        $this->recipe_entries = TableRegistry::get('RecipeEntries');
        $recipeEntry = $this->recipe_entries->get($this->recipe_entry_id);
        return $recipeEntry->parent_recipe_entry_id == null ?  false :  true;
    }
}
