<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Cache\Cache;
use Cake\Log\Log;
use Cake\Validation\Validator;
use SoftDelete\Model\Table\SoftDeleteTrait;
use Cake\ORM\TableRegistry;

/**
 * Sensors Model
 *
 * @property \App\Model\Table\DevicesTable|\Cake\ORM\Association\BelongsTo $Devices
 * @property \App\Model\Table\ZonesTable|\Cake\ORM\Association\BelongsToMany $Zones
 *
 * @method \App\Model\Entity\Sensor get($primaryKey, $options = [])
 * @method \App\Model\Entity\Sensor newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Sensor[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Sensor|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Sensor patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Sensor[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Sensor findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @property \App\Model\Table\SensorTypesTable|\Cake\ORM\Association\BelongsTo $SensorTypes
 * @property \App\Model\Table\MapItemsTable|\Cake\ORM\Association\BelongsTo $MapItems
 * @mixin \App\Model\Behavior\EnumBehavior
 * @mixin \App\Model\Behavior\NotifierBehavior
 */
class SensorsTable extends Table
{
    use SoftDeleteTrait;

     public $enums = array(
            'status' => [
                'Disabled',
                'Enabled',
                'Powered', // Deprecated. 'powered sensors' are now Outputs.
                'Errored'
            ],
            # This is the actual list of Data Types.
            'data_type' => [
                'Unspecified',              # 0
                'Temperature',              # 1
                'Humidity',                 # 2
                'Co2',                      # 3
                'pH',                       # 4
                'DO',                       # 5
                'EC',                       # 6
                'CT',                       # 7
                'Fill Level',               # 8
                'Vapor Pressure Deficit',   # 9
                'PAR',                      # 10
                'Soil Moisture',            # 11
                'Weight',                   # 12
                'Atmospheric Pressure',     # 13
                'Battery Level',            # 14
                'Voltage',                  # 15
                'Dielectric Permittivity',  # 16
                'Light Intensity',          # 17
                'Raw IR',                   # 18
                'RSSI',                     # 19
                'Volumetric Water Content'  # 20

            ],
            # This is the list of different types of sensors our system supports
            'sensor_type' => [
                'Unspecified',              # 0
                'Waterproof Temperature',   # 1 
                'Humidity',                 # 2 HIH3160
                'Air Temperature',          # 3 HIH3160
                'Co2',                      # 4
                'pH',                       # 5 Atlas Scientific pH
                'DO',                       # 6 Atlas Scientific DO
                'EC',                       # 7 Analog
                'CT',                       # 8
                'Fill Level',               # 9
                'Vapor Pressure Deficit',   # 10
                'PAR',                      # 11
                'Atlas Scientific RTD',     # 12
                'Soil Moisture',            # 13
                '4-20ma pH',                # 14
                '4-20ma EC',                # 15
                'SCD30 Co2',                # 16
                'SCD30 Humidity',           # 17
                'SCD30 Air Temperature',    # 18
                'BME280 Humidity',          # 19
                'BME280 Air Temperature',   # 20
                'BME280 Air Pressure',      # 21
                'LoRa barometer_temperature',  # 22
                'LoRa barometric_pressure',    # 23
                'LoRa battery_level',          # 24
                'LoRa capacitor_voltage_1',    # 25
                'LoRa capacitor_voltage_2',    # 26
                'LoRa co2_concentration_lpf',  # 27
                'LoRa co2_concentration',      # 28
                'LoRa co2_sensor_status',      # 29
                'LoRa co2_sensor_temperature', # 30
                'LoRa dielectric_permittivity',# 31
                'LoRa electrical_conductivity',# 32
                'LoRa light_intensity',        # 33
                'LoRa photosynthetically_active_radiation', # 34
                'LoRa raw_ir_reading',         # 35
                'LoRa raw_ir_reading_lpf',     # 36
                'LoRa relative_humidity',      # 37
                'LoRa rssi',                   # 38
                'LoRa soil_temp',              # 39
                'LoRa temp',                   # 40
                'LoRa temperature',            # 41
                'LoRa volumetric_water_content'# 42
            ],
            # This is a lookup table, given the id of the sensor_type above, what is the data_type for it?
            'sensor_data_type' => [
                0, #'Unspecified',              # 0
                1, #'Waterproof Temperature',   # 1
                2, #'Humidity',                 # 2
                1, #'Air Temperature',          # 3
                3, #'Co2',                      # 4
                4, #'pH',                       # 5
                5, #'DO',                       # 6
                6, #'EC',                       # 7
                7, #'CT',                       # 8
                8, #'Fill Level',               # 9
                9, #'Vapor Pressure Deficit',   # 10
                10, #'PAR',                      # 11
                1, #'Atlas Scientific RTD',     # 12
                11, #'Soil Moisture'             # 13
                4, # pH                         #14
                6, # EC                          #15
                3, #co2                         16
                2, #humidity                    17
                1, #air temperature             18
                2, #humidity                    19
                1, #air temperature             20
                13, #air pressure                21
                1,                              #22
                13,                              #23
                14,                              #24
                15,                              #25
                15,                              #26
                3,                              #27
                3,                              #28
                0,                              #29
                1,                              #30
                16,                              #31
                6,                              #32
                17,                              #33
                10,                              #34
                18,                              #35
                18,                              #36
                2,                              #37
                19,                              #38
                1,                              #39
                1,                              #40
                1,                              #41
                20                              #42
            ],
            'sensor_display_class' => [
                '',
                "wi wi-raindrops",
                "wi wi-humidity",
                "wi wi-thermometer",
                "wi wi-barometer",
                "wi wi-raindrop",
                "wi wi-humidity",
                "wi wi-dust",
                "wi wi-lightning",
                "wi wi-flood",
                "wi wi-lightning",
                "wi wi-raindrops",
                "wi wi-thermometer",
                "wi wi-humidity"
            ],
            'sensor_symbol' => [
                '',
                '&#8457;',
                '&#37;',
                '&#8457;',
                'ppm',
                'pH',
                '',
                '&#956;',
                '',
                '',
                'mb',
                'μmol/s',
                '&#8457;',
                '&#37;'
            ],
            'sensor_metric_symbol' => [
                '',
                '&#8451;',
                '',
                '&#8451;',
                'ppm',
                'pH',
                '',
                '',
                '',
                '',
                'mb',
                'μmol/s',
                '&#8451;',
                ''
            ]
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

        $this->setTable('sensors');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Enum');
        $this->addBehavior("Mappable");
        $this->addBehavior('Notifier',[
            'notification_level' => 1
        ]);
        $this->addBehavior('Organization');

        $this->belongsTo('Devices', [
            'foreignKey' => 'device_id',
            'strategy' => 'select'
        ]);
        $this->belongsTo('MapItems', [
            'foreignKey' => 'map_item_id',
        ]);

        $this->belongsToMany('Zones', [
            'joinTable' => 'sensors_zones'
        ]);

        $this->hasOne('Outputs', [
            'foreignKey' => 'sensor_id'
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
            ->integer('sensor_type_id')
            //->requirePresence('sensor_type_id', 'create')
            ->notEmpty('sensor_type');

        $validator
            // ->requirePresence('sensor_pin', 'create')
            ->notEmpty('sensor_pin');

        $validator
            ->allowEmpty('label');

        $validator
            ->integer('status');

        $validator
            ->boolean('deleted')
            ->allowEmpty('deleted');

        $validator
            ->dateTime('deleted_date')
            ->allowEmpty('deleted_date');

        $validator
            ->dateTime('last_good_data_time')
            ->allowEmpty('last_good_data_time');

        $validator
            ->allowEmpty('last_good_data');

        $validator
            ->allowEmpty('calibration');

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
        $rules->add($rules->existsIn(['device_id'], 'Devices'));
        // $rules->add($rules->existsIn(['zone_id'], 'Zones'));

        return $rules;
    }

    // public function afterSave( $event, $entity, $options) {
    //     Cache::delete('floorplan_sensors_json_decoded');
    //     Cache::delete('floorplan_sensors');
    //     $Zones = TableRegistry::get("Zones");

    //     # Generate an updated list of sensors by zone
    //     $sensor = $this->get($entity['id'],[
    //         'contain' =>
    //             [
    //                 'Zones'
    //             ]
    //     ]);
    //     foreach ($sensor['zones'] as $zone) {
    //         # We don't have it in cache, so load the sensors.
    //         $zone = $Zones->get($zone['id'],[
    //             'contain' =>
    //                 [
    //                     'Sensors'
    //                 ]
    //         ]);
    //         $sensorsByType = [];
    //         foreach ($zone['sensors'] as $sensor) {
    //             if (!isset($sensorsByType[$sensor['sensor_type_id']]) || !is_array($sensorsByType[$sensor['sensor_type_id']])) {
    //                 $sensorsByType[$sensor['sensor_type_id']] = [];
    //             }
    //             array_push($sensorsByType[$sensor['sensor_type_id']], $sensor['id']);
    //         }
    //         Cache::write('sensors-by-type-zone-'.$zone['id'],$sensorsByType);
    //     }
    // }

    public function afterDelete( $event, $entity, $options) {
        Cache::delete('floorplan_sensors_json_decoded');
        Cache::delete('floorplan_sensors');
    }

    public function getTempDataSymbol($show_metric) {
        if ($show_metric == false) {
            $tempSymbol = '&#8457;';    //fahrenheit symbol
        } else {
            $tempSymbol = '&#8451;';    //celsius symbol
        }
        return $tempSymbol;
    }



    public function getSensorTypeSymbol($sensor_type){
        $type_symbol = "";
        switch($sensor_type){
            //5/30/18 - sensors that are missing in this list had empty data in the table when migrating from table
            case "Waterproof Temperature Sensor":
                $type_symbol = "&#8457;";
                break;
            case "Humidity Sensor":
                $type_symbol = "&#37;";
                break;
            case "Air Temperature Sensor":
                $type_symbol = "&#8457;";
                break;
            case "Co2 Sensor":
                $type_symbol = "ppm";
                break;
            case "pH Sensor":
                $type_symbol = "pH";
                break;
            case "EC Sensor":
                $type_symbol = "&#956;";
                break;
            case "Fill Level Sensor":
                $type_symbol = "";
                break;
            case "PAR Sensor":
                $type_symbol = "nm";
                break;
            case "Vapor Pressure Deficit Sensor":
                $type_symbol = "mb";
                break;
            case "Atlas Scientific RTD":
                $type_symbol = "&#8457;";
                break;
            case "Soil Moisture":
                $type_symbol = "&#37;";
                break;
        }

        return $type_symbol;
    }

    public function getSensorTypeDisplayClass($sensor_type){
        $display_class = "";
        switch($sensor_type){
            //5/30/18 - sensors that are missing in this list had empty data in the table when migrating from table
            case "Waterproof Temperature Sensor":
                $display_class = "wi wi-raindrops";
                break;
            case "Humidity Sensor":
                $display_class = "wi wi-humidity";
                break;
            case "Air Temperature Sensor":
                $display_class = "wi wi-thermometer";
                break;
            case "Co2 Sensor":
                $display_class = "wi wi-barometer";
                break;
            case "pH Sensor":
                $display_class = "wi wi-raindrop";
                break;
            case "DO Sensor":
                $display_class = "wi wi-humidity";
                break;
            case "EC Sensor":
                $display_class = "wi wi-dust";
                break;
            case "CT Sensor":
                $display_class = "wi wi-lightning";
                break;
            case "Fill Level Sensor":
                $display_class = "wi wi-flood";
                break;
            case "PAR Sensor":
                $display_class = "wi wi-lightning";
                break;
            case "Vapor Pressure Deficit Sensor":
                $display_class = "wi wi-raindrops";
                break;
            case "Atlas Scientific RTD":
                $display_class = "wi wi-thermometer";
                break;
            case "Soil Moisture":
                $display_class = "wi wi-humidity";
                break;
        }
        return $display_class;
    }

    public function getSensorTypeName($sensor_type_id){
        return $this->enumKeyToValue('sensor_type', $sensor_type_id);
    }

    public function getDataTypeFromSensorType($sensor_type_id) {
      Log::write("debug", "Got: ".$sensor_type_id);
      Log::write("debug", "Got: ".$this->enumKeyToValue('sensor_data_type', $sensor_type_id));
      Log::write("debug", "Got: ".$this->enumKeyToValue('data_type', $this->enumKeyToValue('sensor_data_type', $sensor_type_id)));
      return $this->enumKeyToValue('data_type', $this->enumKeyToValue('sensor_data_type', $sensor_type_id));
    }
//    public function beforeSave( $event, $entity, $options) {
//        if ($entity->isNew()) {
//            if (isset($entity->device)) {
//                $entity->latitude = $entity->device->map_item->latitude;
//                $entity->longitude =  $entity->device->map_item->longitude;
//                //$entity->zones = $entity->device->zones;
//                $entity->isDirty('zones',true);
//
//                if (isset($entity->device->zones)) {
//                    if (!isset($this->Zones)) {
//                        $this->Zones = TableRegistry::get("Zones");
//                    }
//                    $zoneEntities = [];
//                    foreach ($entity->device->zones as $zoneLabel) {
//                        $zoneEntity = $this->Zones->find()->where(['label' => $zoneLabel])->first();
//                        if (isset($zoneEntity)) {
//                            array_push($zoneEntities, $zoneEntity);
//                        }
//                    }
//                    $entity->zones = $zoneEntities;
//                    $entity->dirty('zones', true);
//                    Log::write("debug", $entity);
//                }
//            }
//        }
//    }
}
