<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * NotesPhotos Model
 *
 * @property \App\Model\Table\NotesTable|\Cake\ORM\Association\BelongsTo $Notes
 * @property \App\Model\Table\PhotosTable|\Cake\ORM\Association\BelongsTo $Photos
 * @property \App\Model\Table\OwnersTable|\Cake\ORM\Association\BelongsTo $Owners
 *
 * @method \App\Model\Entity\NotesPhoto get($primaryKey, $options = [])
 * @method \App\Model\Entity\NotesPhoto newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\NotesPhoto[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\NotesPhoto|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\NotesPhoto|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\NotesPhoto patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\NotesPhoto[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\NotesPhoto findOrCreate($search, callable $callback = null, $options = [])
 */
class NotesPhotosTable extends Table
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

        $this->setTable('notes_photos');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Organization');

        $this->belongsTo('Notes', [
            'foreignKey' => 'note_id'
        ]);
        $this->belongsTo('Photos', [
            'foreignKey' => 'photo_id'
        ]);
        // $this->belongsTo('Owners', [
        //     'foreignKey' => 'owner_id'
        // ]);
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
            ->integer('owner_type')
            ->allowEmpty('owner_type');

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
        $rules->add($rules->existsIn(['note_id'], 'Notes'));
        $rules->add($rules->existsIn(['photo_id'], 'Photos'));
        // $rules->add($rules->existsIn(['owner_id'], 'Owners'));

        return $rules;
    }
}
