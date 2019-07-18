<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use SoftDelete\Model\Table\SoftDeleteTrait;

/**
 * RecipeEntries Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Zones
 * @property \App\Model\Table\RecipesTable|\Cake\ORM\Association\BelongsTo $Recipes
 * @property \App\Model\Table\BatchRecipeEntriesTable|\Cake\ORM\Association\HasMany $BatchRecipeEntries
 *
 * @property task_label
 * @property task_type_id
 * @property days
 *
 * @method \App\Model\Entity\RecipeEntry get($primaryKey, $options = [])
 * @method \App\Model\Entity\RecipeEntry newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\RecipeEntry[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\RecipeEntry|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\RecipeEntry patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\RecipeEntry[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\RecipeEntry findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class RecipeEntriesTable extends Table
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

        $this->setTable('recipe_entries');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Organization');

        $this->belongsTo('Recipes', [
            'foreignKey' => 'recipe_id',
            'joinType' => 'INNER'
        ]);
        $this->hasMany('BatchRecipeEntries', [
            'foreignKey' => 'recipe_entry_id'
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
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->integer('days')
            ->requirePresence('days', 'create')
            ->notEmpty('days');

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
        $rules->add($rules->existsIn(['recipe_id'], 'Recipes'));

        return $rules;
    }
}
