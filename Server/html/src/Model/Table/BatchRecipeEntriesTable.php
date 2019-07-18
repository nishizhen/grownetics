<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use SoftDelete\Model\Table\SoftDeleteTrait;


/**
 * BatchRecipeEntries Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Zones
 * @property \App\Model\Table\RecipeEntriesTable|\Cake\ORM\Association\BelongsTo $RecipeEntries
 *
 * @method \App\Model\Entity\BatchRecipeEntry get($primaryKey, $options = [])
 * @method \App\Model\Entity\BatchRecipeEntry newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\BatchRecipeEntry[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\BatchRecipeEntry|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\BatchRecipeEntry patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\BatchRecipeEntry[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\BatchRecipeEntry findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @property \App\Model\Table\HarvestBatchesTable|\Cake\ORM\Association\BelongsTo $HarvestBatches
 */
class BatchRecipeEntriesTable extends Table
{
    use SoftDeleteTrait;

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('batch_recipe_entries');
        $this->setDisplayField('label');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Organization');

        $this->belongsTo('Zones', [
            'foreignKey' => 'zone_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('RecipeEntries', [
            'foreignKey' => 'recipe_entry_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('HarvestBatches', [
            'foreignKey' => 'id',
            'joinType' => 'INNER'
        ]);
       
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        //$rules->add($rules->existsIn(['zone_type_id'], 'ZoneTypes'));
        //$rules->add($rules->existsIn(['recipe_entry_id'], 'RecipeEntries'));

        return $rules;
    }
}
