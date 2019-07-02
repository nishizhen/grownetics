<?php
namespace App\Model\Table;

use Cake\Cache\Cache;
use Aura\Intl\Exception;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

use InfluxDB\Point;
use InfluxDB\Client;
use InfluxDB\Database;

/**
 * Datapoints Model
 *
 * @property \App\Model\Table\DevicesTable|\Cake\ORM\Association\BelongsTo $Devices
 * @property \App\Model\Table\ZonesTable|\Cake\ORM\Association\BelongsTo $Zones
 *
 * @method \App\Model\dataPoint\DataPoint get($primaryKey, $options = [])
 * @method \App\Model\dataPoint\DataPoint newdataPoint($data = null, array $options = [])
 * @method \App\Model\dataPoint\DataPoint[] newEntities(array $data, array $options = [])
 * @method \App\Model\dataPoint\DataPoint|bool save(\Cake\Datasource\dataPointInterface $dataPoint, $options = [])
 * @method \App\Model\dataPoint\DataPoint patchdataPoint(\Cake\Datasource\dataPointInterface $dataPoint, array $data, array $options = [])
 * @method \App\Model\dataPoint\DataPoint[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\dataPoint\DataPoint findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @mixin \App\Model\Behavior\EnumBehavior
 */
class DataPointsTable extends Table
{

    public $enums = array(
        'source_type' => array(
            'Sensor',
            'Zone',
            'Weather',
            'Harvest Batch',
            'Argus'
        ),
        'status' => array(
            'Queued',
            'Processed'
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

        $this->setTable('datapoints');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Enum');
        
        $this->belongsTo('Devices', [
            'foreignKey' => 'device_id'
        ]);
        $this->belongsTo('Zones', [
            'foreignKey' => 'zone_id'
        ]);
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->integer('type')
            ->requirePresence('type', 'create')
            ->notEmpty('type');

        $validator
            ->integer('source_type')
            ->requirePresence('source_type', 'create')
            ->notEmpty('source_type');

        $validator
            ->integer('type')
            ->requirePresence('value', 'create')
            ->notEmpty('value');

        return $validator;
    }

    public function getDataPointsForCondition($condition) {
        $this->RuleConditions = TableRegistry::get('RuleConditions');
        $this->Zones = TableRegistry::get('Zones');

        $sensorsByType = [];
        if ($condition->data_source == $this->RuleConditions->enumValueToKey('data_source','Zone')) {
            # Load all the sensors associated with the selected zone
            $zone = $this->Zones->get($condition->data_id, [
                'contain' => 
                [
                    'Sensors'
                ]
            ]);
            if (!empty($zone['sensors'])) {
                foreach($zone['sensors'] as $sensor) {
                    if ($sensor['sensor_type_id'] == $condition->sensor_type) {
                        if (!isset($sensorsByType[$sensor['sensor_type_id']]) || !is_array($sensorsByType[$sensor['sensor_type_id']])) {
                            $sensorsByType[$sensor['sensor_type_id']] = [];
                        }
                        array_push($sensorsByType[$sensor['sensor_type_id']], $sensor['id']);
                    }
                }
            }
        } else {
            if (!isset($sensorsByType[$condition->sensor_type]) || !is_array($sensorsByType[$condition->sensor_type])) {
                $sensorsByType[$condition->sensor_type] = [];
            }
            array_push($sensorsByType[$condition->sensor_type], $condition->data_id);
        }

        $dataPoints = [];

        foreach($sensorsByType as $type => $sensors) {
            foreach ($sensors as $sensor) {
                $value = Cache::read('sensor-value-' . $sensor);
                if ($value) {
                    array_push($dataPoints, $value);
                }
            }
        }

        return $dataPoints;
    }

    # Given a set of DataPoints determine the value that the rule condition should trigger from
    # Or, return the status of an output as a value, rather than an actual datapoint
    public function getValueForRule($dataPoints,$condition) {
        $this->Rules = TableRegistry::get('Rules');
        $this->RuleActions = TableRegistry::get('RuleActions');
        $this->RuleActionTargets = TableRegistry::get('RuleActionTargets');
        $this->Outputs = TableRegistry::get('Outputs');
        $this->RuleConditions = TableRegistry::get('RuleConditions');

        $dataPointValue = 0;

        if ($dataPoints) {
            if ($condition['zone_behavior'] == $this->RuleConditions->enumValueToKey('zone_behavior','Single Sensor'))
            {
                # Get Highest/Lowest value given all sensor data from a zone
                if ($condition['operator'] == $this->RuleConditions->enumValueToKey('operator','>')) {
                    $dataPointValue = max($dataPoints);
                } else {
                    $dataPointValue = min($dataPoints);
                }                
            }
            else if ($condition['zone_behavior'] == $this->RuleConditions->enumValueToKey('zone_behavior','Average Of Sensors')) {
                switch ($condition['averaging_method']) {
                    case $this->RuleConditions->enumValueToKey('averaging_method', 'Average'):
                        $dataPointValue = array_sum($dataPoints) / count($dataPoints);
                        break;
                    case $this->RuleConditions->enumValueToKey('averaging_method', 'Median'):
                        rsort($dataPoints); 
                        $middle = round(count($dataPoints) / 2); 
                        $dataPointValue = $dataPoints[$middle-1];
                        break;
                }
            }
        }
        
        $dataPointValue = round($dataPointValue,2);
        return $dataPointValue;
    }

}
