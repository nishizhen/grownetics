<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * NotesPlants Model
 *
 * @property \App\Model\Table\NotesTable|\Cake\ORM\Association\BelongsTo $Notes
 * @property \App\Model\Table\PlantsTable|\Cake\ORM\Association\BelongsTo $Plants
 *
 * @method \App\Model\Entity\NotesPlant get($primaryKey, $options = [])
 * @method \App\Model\Entity\NotesPlant newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\NotesPlant[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\NotesPlant|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\NotesPlant|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\NotesPlant patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\NotesPlant[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\NotesPlant findOrCreate($search, callable $callback = null, $options = [])
 */
class NotesPlantsTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('notes_plants');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsToMany('Notes', [
            'foreignKey' => 'note_id',
            'targetForeignKey' => 'plant_id',
            'joinTable' => 'notes_plants'
        ]);
        $this->belongsToMany('Plants', [
            'foreignKey' => 'plant_id',
            'targetForeignKey' => 'note_id',
            'joinTable' => 'notes_plants'
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

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
//    public function buildRules(RulesChecker $rules)
//    {
//        $rules->add($rules->existsIn(['note_id'], 'Notes'));
//        $rules->add($rules->existsIn(['plant_id'], 'Plants'));
//
//        return $rules;
//    }
}
