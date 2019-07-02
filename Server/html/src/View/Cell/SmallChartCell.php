<?php
namespace App\View\Cell;

use Cake\View\Cell;

/**
 * SmallChart cell
 */
class SmallChartCell extends Cell
{

    /**
     * List of valid options that can be passed into this
     * cell's constructor.
     *
     * @var array
     */
    protected $_validCellOptions = [];

    /**
     * Default display method.
     *
     * @return void
     */
    public function small_chart($config, $count)
    {
        $DataPoints = $this->loadModel("DataPoints");
        $Sensors = $this->loadModel("Sensors");
        $Zones = $this->loadModel("Zones");
        $this->loadModel("Users");
        $user = $this->Users->get($this->request->session()->read('Auth.User.id'));

                $chart = [];

        if (isset($config->data_type)) {
                $sensor_type_name = $Sensors->enumKeyToValue('sensor_type',$config->data_type);
                    
                if ($user->show_metric == true && ($config->data_type == 1 || $config->data_type == 3)) {
                    $symbol = $Sensors->enumKeyToValue('sensor_metric_symbols', $config->data_type);
                } else {
                    $symbol = $Sensors->enumKeyToValue('sensor_symbols', $config->data_type);
                }
                $chart['sensor_type_label'] = $sensor_type_name; //$row['label'];
                $chart['sensor_type_id'] = $Sensors->enumValueToKey('sensor_type',$sensor_type_name);

                $chart['sensor_type_symbol'] = $symbol;
                $chart['id'] = $count;
                if (isset($config->source_type)) {
                    $chart['source_type'] = $DataPoints->enumKeyToValue('source_type',$config->source_type);

                    if ($config->source_type == $DataPoints->enumValueToKey('source_type','Sensor')) {

                        $query = $Sensors->find('all', [
                            'conditions' => [
                                'id' => $config->source_id
                            ],
                            'fields' => ['label', 'id']
                        ]);

                        $row = $query->first();
                        $chart['source_label'] = $row['label'];
                        $chart['source_id'] = $row['id'];
                    }
                    else if ($config->source_type == $DataPoints->enumValueToKey('source_type','Zone')) {
                        $query = $Zones->find('all', [
                            'conditions' => [
                                'id' => $config->source_id
                            ],
                            'fields' => ['label', 'id']
                        ]);
                        $row = $query->first();
                        $chart['source_label'] = $row['label'];
                        $chart['source_id'] = $row['id'];
                    }

                }
            }
            else {
                $chart['sensor_type_symbol'] = '';
                $chart['sensor_type_id'] = '';
                $chart['sensor_type_label'] = '';
                $chart['source_label'] = '';
                $chart['source_id'] = '';
                $chart['source_type'] = '';
            }   
        $this->set('chart', $chart);

        $this->set('count', $count);
        $zones = $Zones->find('all');
        $sensors = $Sensors->find();
        $this->set('zones', $zones);
        $this->set('sensors', $sensors);

        $sensor_type = $this->Sensors->enums['sensor_type'];
        $this->set('sensor_type', $sensor_type);
    }
}
