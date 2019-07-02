<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;

/**
 * Cultivar Entity
 *
 * @property int $id
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property string $name
 * @property string $photo
 * @property string $description
 * @property \Cake\I18n\FrozenTime $deleted
 * @property \Cake\I18n\Time $deleted_date
 *
 * @property \App\Model\Entity\HarvestBatch[] $harvest_batch
 * @property \App\Model\Entity\Vote[] $votes
 * @property string $label
 * @property int $batch_count
 */
class Cultivar extends Entity
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

    public function _getLastRecipeUsed() {
        $this->HarvestBatches = TableRegistry::get('HarvestBatches');
        $this->Recipes = TableRegistry::get('Recipes');
        $recentBatch = $this->HarvestBatches->find('all',[
            'conditions' => ['cultivar_id' => $this->_properties['id']],
            'order' => ['created' => 'desc']
            ])->first();
        if ($recentBatch != '') {
            $lastRecipe = $this->Recipes->get($recentBatch->recipe_id);
            return $lastRecipe;
        }
    }
}
