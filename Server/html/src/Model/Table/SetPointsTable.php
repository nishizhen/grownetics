<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Network\Http\Client;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use App\Lib\DataConverter;

/**
 * SetPoints Model
 *
 * @property \App\Model\Table\TargetsTable|\Cake\ORM\Association\BelongsTo $Targets
 *
 * @method \App\Model\Entity\SetPoint get($primaryKey, $options = [])
 * @method \App\Model\Entity\SetPoint newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\SetPoint[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\SetPoint|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SetPoint patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\SetPoint[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\SetPoint findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @mixin \App\Model\Behavior\EnumBehavior
 */
class SetPointsTable extends Table
{

    public $enums = [
        'status' => [
            'Disabled',
            'Enabled',
            'Set'
        ],
        'target_type' => [
            'Zone',
            'Zone Type'
        ],
        'alert_level' => [
            'None',
            'Warning',
            'Alerting'
        ]
    ];

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('set_points');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Enum');
        $this->addBehavior('Muffin/Footprint.Footprint', [
            'events' => [
                'Model.beforeSave' => [
                    'show_metric' => 'always'
                ]
            ],
            'propertiesMap' => [
                'show_metric' => '_footprint.show_metric',
            ],
        ]);
        $this->addBehavior('Organization');
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
            ->dateTime('deleted')
            ->allowEmpty('deleted');

        $validator
            ->allowEmpty('label');

        $validator
            ->allowEmpty('value');

        $validator
            ->integer('target_type')
            ->allowEmpty('target_type');

        $validator
            ->integer('data_type')
            ->allowEmpty('data_type');

        return $validator;
    }

    public function generateFromDefaultSetPoints() {
        $this->Zones = TableRegistry::get('Zones');
        $this->Sensors = TableRegistry::get('Sensors');

        $zones = $this->Zones->find('all', ['contain' => ['Sensors']]);

        foreach ($zones as $zone) {
            $sensorTypes = [];
            if (!empty($zone['sensors'])) {
                foreach($zone['sensors'] as $sensor) {
                    if (!isset($sensorTypes[$sensor['sensor_type_id']]) || !is_array($sensorTypes[$sensor['sensor_type_id']])) {
                        $sensorTypes[$sensor['sensor_type_id']] = [];
                    }
                    array_push($sensorTypes[$sensor['sensor_type_id']],"source_id = '".$sensor['id']."'");
                }
            }
            
            foreach ($sensorTypes as $type => $value) {
                if ($zone->plant_zone_type_id) {
                    $activeZoneSetPoint = $this->find('all', [
                        'conditions' => [
                            'target_type' => $this->enumValueToKey('target_type', 'Zone'),
                            'target_id' => $zone->id,
                            'data_type' => $type,
                        ]
                    ])->first();
                    if (!$activeZoneSetPoint) {
                        $defaultSetPoint = $this->find('all', [
                            'conditions' => [
                                'target_type' => $this->enumValueToKey('target_type', 'Zone Type'),
                                'target_id' => $zone->plant_zone_type_id,
                                'data_type' => $type
                            ]
                        ])->first();
                        $zoneSetPoint = $this->newEntity([
                            'label' => $zone->label.' - '.$this->Sensors->enumKeyToValue('sensor_type', $type),
                            'status' => $this->enumValueToKey('status', 'Set'),
                            'value' => null,
                            'target_type' => $this->enumValueToKey('target_type', 'Zone'),
                            'target_id' => $zone->id,
                            'data_type' => $type,
                            'default_setpoint_id' => $defaultSetPoint->id
                        ]);
                        $this->save($zoneSetPoint);
                    }
                }
            }
        }

    }

    public function getSetPointForTarget($target_type, $target, $data_type) {
        $setPoint = $this->find('all', [
            'conditions' => [
                'target_type' => $target_type,
                'status' => $this->enumValueToKey('status','Set'),
                'data_type' => $data_type,
                'target_id' => $target->id
                ]
        ])->first();
        // if setPoint hasn't been overridden, use default setPoint value
        if ($setPoint->default_setpoint_id != 0) {
            $defaultSetPoint = $this->get($setPoint->default_setpoint_id);
            $setPoint->value = $defaultSetPoint->value;
            return $setPoint;
        } else {
            return $setPoint;
        }
    }

    public function revertToDefaultSetPoint($setPoint, $show_metric) {
        $this->Zones = TableRegistry::get('Zones');
        $this->Sensors = TableRegistry::get('Sensors');
        $zone = $this->Zones->get($setPoint->target_id);
        $defaultSetPoint = $this->find('all', [
            'conditions' => [
                'target_type' => $this->enumValueToKey('target_type', 'Zone Type'), 
                'status' => $this->enumValueToKey('status','Enabled'),
                'data_type' => $setPoint->data_type,
                'target_id' => $zone->plant_zone_type_id
                ]
        ])->first();
        $setPoint->default_setpoint_id = $defaultSetPoint->id;
        $setPoint['default_value'] = $defaultSetPoint->value;
        $setPoint->value = null;
        if ($show_metric == false && $setPoint->data_type == $this->Sensors->enumValueToKey('sensor_type', 'Air Temperature')) {
            $setPoint['default_value'] = round($setPoint['default_value'] * 9 / 5 + 32, 2);
        }
        return $setPoint;
    }

    public function beforeSave($event, $setPoint, $options) {
        $this->Sensors = TableRegistry::get('Sensors');
        $converter = new DataConverter();
        if (isset($options['_footprint']) && 
            $setPoint->data_type == $this->Sensors->enumValueToKey('sensor_type', 'Air Temperature') &&
            $setPoint->default_setpoint_id == 0) {
            $setPoint->value = $converter->convertUnits($setPoint->value, $this->Sensors->enumKeyToValue('sensor_data_type', $setPoint->data_type), $options['_footprint']['show_metric']);
        }
    }
}
