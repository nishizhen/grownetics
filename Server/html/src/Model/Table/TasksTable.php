<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\TableRegistry;
use SoftDelete\Model\Table\SoftDeleteTrait;
use Cake\Chronos\Chronos;

/**
 * Tasks Model
 *
 * @property \App\Model\Table\HarvestbatchesTable|\Cake\ORM\Association\BelongsTo $Harvestbatches
 *
 * @method \App\Model\Entity\Task get($primaryKey, $options = [])
 * @method \App\Model\Entity\Task newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Task[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Task|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Task patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Task[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Task findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\ZonesTable|\Cake\ORM\Association\BelongsTo $Zones
 * @property \App\Model\Table\batchRecipeEntriesTable|\Cake\ORM\Association\BelongsTo $batchRecipeEntries
 * @mixin \App\Model\Behavior\NotifierBehavior
 * @mixin \App\Model\Behavior\EnumBehavior
 */
class TasksTable extends Table
{
    use SoftDeleteTrait;

    public $enums = array(
        'status' => array(
            'Incomplete',
            'Completed'
        ),
        'type' => array(
            'Move',
            'Tag',
            'Harvest',
            'Generic',
            'Defoliate',
            'Trim',
            'Package',
            'Inspect',
            'Up-pot',
            'Weigh',
            'Clean',
            'Water',
            'Mix-nutrients'
        )
    );

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('tasks');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Notifier');
        $this->addBehavior('Enum');

        $this->belongsTo('Harvestbatches', [
            'foreignKey' => 'harvestbatch_id'
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'assignee',
            'joinType' => 'inner'
        ]);
        $this->belongsTo('Zones', [
            'foreignKey' => 'zone_id',
            'joinType' => 'inner'
        ]);
        $this->belongsTo('batchRecipeEntries', [
            'foreignKey' => 'batch_recipe_entry_id',
            'joinType' => 'inner'
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
            ->integer('deleted')
            ->allowEmpty('deleted');

        $validator
            ->dateTime('deleted_date')
            ->allowEmpty('deleted_date');

        $validator
            ->allowEmpty('taskLabel');

        $validator
            ->integer('status')
            ->allowEmpty('status');

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

        return $rules;
    }

    public function afterSave($event, $task, $options) { 

        $this->batch_recipe_entries = TableRegistry::get('batch_recipe_entries');
        $entries = $this->batch_recipe_entries->find('all', ['conditions' => ['batch_id' => $task->harvestbatch_id], 
            'order' => ['planned_start_date' => 'asc']
        ])->toArray();
        reset($entries);

        if (in_array($task->type, [$this->enumValueToKey('type', 'Move'), $this->enumValueToKey('type', 'Harvest')])) {
            $taskBre = $this->batch_recipe_entries->get($task->batch_recipe_entry_id);        
        }
        if ($task->status == $this->enumValueToKey('status', 'Completed') && ($task->type == $this->enumValueToKey('type', 'Move') || $task->type == $this->enumValueToKey('type', 'Harvest'))) {
            $taskBre->planned_start_date = $task->completed_date->startOfDay();

            if ($task->type == $this->enumValueToKey('type', 'Harvest')) {
                $taskBre->planned_end_date = $task->completed_date->startOfDay();
            }
            $this->batch_recipe_entries->save($taskBre);
            foreach ($entries as $entry) {
                if ($taskBre->id == $entry->id) {
                    $prevEntry = prev($entries);
                    if ($prevEntry) {
                        $prevEntry->planned_end_date = $task->completed_date->startOfDay();
                        $this->batch_recipe_entries->save($prevEntry);
                    }
                }
                next($entries);
            }
        }
        if ( ($task->type == $this->enumValueToKey('type', 'Move') || $task->type == $this->enumValueToKey('type', 'Harvest')) && $task->status == $this->enumValueToKey('status', 'Incomplete')) {
            if ($task->type == $this->enumValueToKey('type', 'Harvest')) {
                $taskBre->planned_end_date = $task->due_date->startOfDay();
                $this->batch_recipe_entries->save($taskBre);
            }
            foreach($entries as $entry) {
                $nextEntry = next($entries);
                if ($nextEntry !== false) {
                    if (strtotime($nextEntry->planned_start_date) != strtotime($entry->planned_end_date)) {
                        $entry->planned_end_date = $nextEntry->planned_start_date;
                        $this->batch_recipe_entries->save($entry);
                    }
                }
            }
        }

        return true;
    }


    public function afterDelete($event, $task, $options) { 

        if ($task->type == $this->enumValueToKey('type', 'Move')) { 
            $this->batch_recipe_entries = TableRegistry::get('batch_recipe_entries');
            $entries = $this->batch_recipe_entries->find('all', ['conditions' => ['batch_id' => $task->harvestbatch_id], 
                'order' => ['planned_end_date' => 'asc']
            ])->toArray();
            reset($entries);
            foreach($entries as $entry) {
                $nextEntry = next($entries);
                if ($nextEntry !== false) {
                    if (strtotime($nextEntry->planned_start_date) != strtotime($entry->planned_end_date)) {
                        $entry->planned_end_date = $nextEntry->planned_start_date;
                        $this->batch_recipe_entries->save($entry);
                    }
                }
            }
            return true;
        }
    }
}
