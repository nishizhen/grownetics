<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use InfluxDB;
use Cake\Cache\Cache;

/**
 * DataPoints Controller
 *
 * @property \App\Model\Table\DataPointsTable $DataPoints
 * @property \App\Model\Table\UsersTable $Users
 */
class DataPointsController extends AppController
{

    public function recent() {
        $this->loadModel('Users');
        $this->loadModel('Sensors');
        $this->loadModel('Tasks');

        if (isset($this->request->data['data_type']) && $this->request->data['data_type'] != NULL) {
            $data_type = $this->request->data['data_type'];
        } else {
            $data_type = 1;
        }
        # How many seconds to wait before giving up trying to talk to Influx.
        $influxTimeout = 2;

        # How tightly to group the results. Longer times will return less data.
        $groupBy = '2m';

        $database = InfluxDB\Client::fromDSN(sprintf('influxdb://root:root@%s:%s/%s', "influxdb", 8086, "sensor_data"), $influxTimeout);
        if ($this->request->data['source_type'] == $this->DataPoints->enumValueToKey('source_type', 'Sensor')) {
            $query = 'SELECT mean("value") FROM "sensor_data"."autogen"."sensor_data" WHERE "data_type" = \''.$data_type.'\' AND "source_type" = \''.$this->request->data['source_type'].'\' AND "source_id" = \''.$this->request->data['source_id'].'\' AND time > now() - '.$this->request->data['timeframe'].' GROUP BY time('.$groupBy.')';
            $result = $database->query($query);
            $results = $result->getPoints();
        } else if ($this->request->data['source_type'] == $this->DataPoints->enumValueToKey('source_type', 'Argus')) {
            $query = 'SELECT mean("value") FROM "integration_data"."autogen"."argus" WHERE "parameter_id" = \''.$this->request->data['source_id'].'\' AND time > now() - '.$this->request->data['timeframe'].' GROUP BY time('.$groupBy.')';
            $result = $database->query($query);
            $results = $result->getPoints();
        } else {
            $data_type = $this->Sensors->enumValueToKey('data_type','Co2');
            $query = 'SELECT mean("value") FROM "sensor_data"."autogen"."sensor_data" WHERE "data_type" = \''.$data_type.'\' AND "source_type" = \''.$this->request->data['source_type'].'\' AND "source_id" = \''.$this->request->data['source_id'].'\' AND time > now() - '.$this->request->data['timeframe'].' GROUP BY time('.$groupBy.')';
            $result = $database->query($query);
            $results['co2_values'] = $result->getPoints();

            $data_type = $this->Sensors->enumValueToKey('data_type','Vapor Pressure Deficit');
            $query = 'SELECT mean("value") FROM "sensor_data"."autogen"."sensor_data" WHERE "data_type" = \''.$data_type.'\' AND "source_type" = \''.$this->request->data['source_type'].'\' AND "source_id" = \''.$this->request->data['source_id'].'\' AND time > now() - '.$this->request->data['timeframe'].' GROUP BY time('.$groupBy.')';
            $result = $database->query($query);
            $results['vpd_values'] = $result->getPoints();

            $data_type = $this->Sensors->enumValueToKey('data_type','Humidity');
            $query = 'SELECT mean("value") FROM "sensor_data"."autogen"."sensor_data" WHERE "data_type" = \''.$data_type.'\' AND "source_type" = \''.$this->request->data['source_type'].'\' AND "source_id" = \''.$this->request->data['source_id'].'\' AND time > now() - '.$this->request->data['timeframe'].' GROUP BY time('.$groupBy.')';
            $result = $database->query($query);
            $results['hum_values'] = $result->getPoints();

            $data_type = $this->Sensors->enumValueToKey('data_type','Temperature');
            $query = 'SELECT mean("value") FROM "sensor_data"."autogen"."sensor_data" WHERE "data_type" = \''.$data_type.'\' AND "source_type" = \''.$this->request->data['source_type'].'\' AND "source_id" = \''.$this->request->data['source_id'].'\' AND time > now() - '.$this->request->data['timeframe'].' GROUP BY time('.$groupBy.')';
            $result = $database->query($query);
            $results['temp_values'] = $result->getPoints();

        }
        $user = $this->Users->get($this->request->session()->read('Auth.User.id'));
        if ($user->show_metric == false) {
            $dataSymbol = $this->Sensors->enumKeyToValue('sensor_symbols', $data_type);
        } else {
            $dataSymbol = $this->Sensors->enumKeyToValue('sensor_metric_symbols', $data_type);
        }

        $this->set(compact('results'));
        $this->set(compact('dataSymbol'));
        $this->set('_serialize', ['results', 'dataSymbol']);
    }

    public function map($sensor_type_id = 3) {
        $this->loadModel('Sensors');
        $this->loadModel('MapItems');
        $datapoints = [];
        # Load all the sensors of the data type we care about.
        # This should really only load the sensors of the type and floorplan level
        # we care about, for now this works.
        $sensors = $this->Sensors->find('all',['conditions'=>['sensor_type_id'=>$sensor_type_id]]);

        # Cache this query to make it faster next time.
        $sensors->cache('sensors-for-sensor-type-'.$sensor_type_id);
        foreach ($sensors as $sensor) {
            # If we have a sensor value in the cache.
            # This is set in DevicesTable::savePinData.
            if (Cache::read('sensor-value-' . $sensor['id']) !== false) {
                #no need to send data older than 5 minutes
                if (strtotime(Cache::read('sensor-time-' . $sensor['id'])) >  strtotime("-5 minutes")) {
                    array_push($datapoints,(object)[
                        'source_id' => $sensor['id'],
                        'data_type' => $this->Sensors->enumKeyToValue('sensor_data_type',$sensor['sensor_type_id']),
                        'sensor_type' => $sensor['sensor_type_id'],
                        'value' => Cache::read('sensor-value-' . $sensor['id']),
                        'created' => Cache::read('sensor-time-' . $sensor['id']),
                        'offset_height' => $sensor['offset_height']
                    ]);  
                } 
            } 
        }
       
        $this->set([
            'my_response' => $datapoints,
            '_serialize' => 'my_response',
        ]);
        $this->RequestHandler->renderAs($this, 'json');
    }
}
