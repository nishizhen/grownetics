<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Log\Log;
use Cake\Validation\Validator;
use SoftDelete\Model\Table\SoftDeleteTrait;
use Cake\ORM\TableRegistry;
use Cake\Mailer\Email;
use InfluxDB\Database\Exception;

/**
 * Devices Model
 *
 * @property \Cake\ORM\Association\HasMany $Datapoints
 * @property \Cake\ORM\Association\HasMany $Outputs
 * @property \Cake\ORM\Association\HasMany $Raw
 * @property \Cake\ORM\Association\HasMany $Sensors
 *
 * @method \App\Model\Entity\User get($primaryKey, $options = [])
 * @method \App\Model\Entity\User newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\User[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\User|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\User patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\User[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\User findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @property \App\Model\Table\RolesTable|\Cake\ORM\Association\BelongsTo $Roles
 * @mixin \App\Model\Behavior\NotifierBehavior
 */
class UsersTable extends Table
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

    $this->setTable('users');
    $this->setDisplayField('name');
    $this->setPrimaryKey('id');

    $this->addBehavior('Timestamp');
    $this->addBehavior('Notifier');

    $this->belongsToMany('Roles', [
      'through' => 'UsersRoles'
    ]);
  }

  /*
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator 
     */

  public function validationDefault(Validator $validator)
  {
    $validator
      ->requirePresence('name', 'create')
      ->notEmpty('name');

    return $validator;
  }

  public function beforeSave($event, $entity, $options)
  {
    $siteSalt = env('SALT');
    $token = substr(hash('ripemd160', $siteSalt . time() . uniqid() . $entity['email']), 0, 10);
    $entity['token'] = $token;
  }

  public function afterSave($event, $user, $options)
  {
    if ($user->isNew()) {
      # Perform new user registration steps, unless specifically told not to.
      # We tell it not to in our tests primarily.
      if (!$user->skipRegister) {
        $this->Zones = TableRegistry::get('Zones');
        $this->Sensors = TableRegistry::get('Sensors');
        $this->DataPoints = TableRegistry::get('DataPoints');
        $sensorTypeName = 'Humidity';
        $zones = [];
        $zones = $this->Zones->find('all', ['conditions' => ['plant_zone_type_id IN' => [$this->Zones->enumValueToKey('plant_zone_types', 'Veg'), $this->Zones->enumValueToKey('plant_zone_types', 'Bloom')]]])->toArray();

        $configs = [];
        foreach ($zones as $zone) {
          $config = (object) ["data_type" => "", "data_label" => "", "data_symbol" => "", "data_display_class" => "", "source_type" => "", "source_id" => "", "source_label" => "", "lowThreshold" => "", "highThreshold" => ""];
          $config->data_type = $this->Sensors->enumValueToKey('sensor_type', $sensorTypeName);
          $config->data_label = $sensorTypeName;
          $config->data_symbol = $this->Sensors->enumKeyToValue('sensor_symbol', $config->data_type);
          $config->data_display_class = $this->Sensors->enumKeyToValue('sensor_display_class', $config->data_type);
          $config->source_type = $this->DataPoints->enumValueToKey('source_type', 'Zone');
          $config->source_id = $zone->id;
          $config->source_label = $zone->label;
          array_push($configs, $config);
        }
        $user->dashboard_config = json_encode($configs);

        # Sent email invite if needed
        $Email = new Email();

        $Email->viewVars(array(
          'token' => $user->token,
          'user' => $user
        ));

        $Email->template('registration_invite', 'default')
          ->emailFormat('html')
          ->subject('Grownetics Password Reset')
          ->to($user['email'])
          ->from('support@grownetics.co');
        try {
          if ($Email->send()) {
            return true;
          } else {
            return false;
          }
        } catch (\Exception $e) { }
        $this->save($user);
      }
    }
  }


  public function afterDelete($event, $entity, $options)
  {
    $this->UserContactMethods = TableRegistry::get('user_contact_methods');
    $userContactMethod = $this->UserContactMethods->find('all', ['conditions' => ['user_id' => $entity->id]])->toArray();
    foreach ($userContactMethod as $contact) {
      $this->UserContactMethods->delete($contact);
    }
  }
}
