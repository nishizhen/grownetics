<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use SoftDelete\Model\Table\SoftDeleteTrait;

/**
 * Notes Model
 *
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\BatchesTable|\Cake\ORM\Association\BelongsTo $Batches
 * @property \App\Model\Table\CultivarsTable|\Cake\ORM\Association\BelongsTo $Cultivars
 * @property \App\Model\Table\ZonesTable|\Cake\ORM\Association\BelongsTo $Zones
 * @property \App\Model\Table\PhotosTable|\Cake\ORM\Association\HasMany $Photos
 * @property \App\Model\Table\PlantsTable|\Cake\ORM\Association\BelongsToMany $Plants
 *
 * @method \App\Model\Entity\Note get($primaryKey, $options = [])
 * @method \App\Model\Entity\Note newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Note[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Note|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Note|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Note patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Note[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Note findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class NotesTable extends Table
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

        $this->setTable('notes');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id', 
            'strategy' => 'select'
        ]);
        $this->belongsTo('HarvestBatches', [
            'foreignKey' => 'batch_id', 
            'strategy' => 'select'
        ]);
        $this->belongsTo('Cultivars', [
            'foreignKey' => 'cultivar_id', 
            'strategy' => 'select'
        ]);
        $this->belongsTo('Zones', [
            'foreignKey' => 'zone_id', 
            'strategy' => 'select'
        ]);
        $this->belongsToMany('Plants', [
            'foreignKey' => 'note_id',
            'targetForeignKey' => 'plant_id',
            'joinTable' => 'notes_plants'
        ]);
        $this->belongsToMany('Photos', [
            'foreignKey' => 'note_id',
            'targetForeignKey' => 'photo_id',
            'joinTable' => 'notes_photos'
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
            ->allowEmpty('cultivar_id');

        //$validator->requirePresence('cultivar_id', 'false');

        return $validator;
    }
}
