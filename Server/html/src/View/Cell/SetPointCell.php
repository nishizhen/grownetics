<?php
namespace App\View\Cell;

use Cake\View\Cell;
use App\Lib\DataConverter;

/**
 * HumSetPoint cell
 */
class SetPointCell extends Cell
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
     * @param target_id: Zone or Zone Type
     * @return void
     */
    public function humSetPointView($target_type, $target)
    {
        $this->loadModel('SetPoints');
        $this->loadModel('Sensors');
        $humiditySensorType = $this->Sensors->enumValueToKey('sensor_type', 'Humidity');
        $setPoint = $this->SetPoints->getSetPointForTarget($target_type, $target, $humiditySensorType);
        $this->set(compact('setPoint', 'humiditySensorType'));
    }

    public function tempSetPointView($target_type, $target, $user_id)
    {
        $this->loadModel('SetPoints');
        $this->loadModel('Sensors');
        $this->loadModel('Users');
        $show_metric = $this->Users->get($user_id)->show_metric;
        $temperatureSensorType = $this->Sensors->enumValueToKey('sensor_type', 'Air Temperature');
        $setPoint = $this->SetPoints->getSetPointForTarget($target_type, $target, $temperatureSensorType);
        $converter = new DataConverter();
        if ($setPoint) {
            $setPoint->value = $converter->displayUnits($setPoint->value, $this->Sensors->enumKeyToValue('sensor_data_type', $setPoint->data_type), $show_metric);
        }
        $this->set(compact('setPoint', 'temperatureSensorType', 'show_metric'));
    }

    public function carbonSetPointView($target_type, $target)
    {
        $this->loadModel('SetPoints');
        $this->loadModel('Sensors');
        $co2SensorType = $this->Sensors->enumValueToKey('sensor_type', 'Co2');
        $setPoint = $this->SetPoints->getSetPointForTarget($target_type, $target, $co2SensorType);
        $this->set(compact('setPoint', 'co2SensorType'));
    }


    public function lightsSetPointView($target_type, $target)
    {
        $this->loadModel('SetPoints');
        $this->loadModel('Sensors');
        $parSensorType = $this->Sensors->enumValueToKey('data_type','PAR');
        $setPoint = $this->SetPoints->getSetPointForTarget($target_type, $target, $parSensorType);
        $this->set(compact('setPoint', 'parSensorType'));
    }
}
