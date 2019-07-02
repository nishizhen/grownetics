<?php
namespace App\Lib;

use Cake\ORM\TableRegistry;
use InfluxDB\Point;
use InfluxDB\Client;
use InfluxDB\Database;
use Cake\Log\Log;

class DataConverter {
    # Converts Hum/AirTemp to VPD (saturated vapor pressure - actual vapor pressure)
    # Source: http://cronklab.wikidot.com/calculation-of-vapour-pressure-deficit
    # @return vapor pressure deficit to 2 decimal places in milibars (mb) hence the 0.01 multiplication
    public function convertToVaporPressureDeficit($humidity, $temperature)
    {
        $svp = 610.7*10**(7.5*$temperature/(237.3+$temperature));
        $vpd = round((((100 - $humidity)/100)*$svp)*0.01, 2);
        return $vpd;
    }

    /** Ensure units are metric before saving to database.
     * @param $value Value to be converted.
     * @param $type Type of measurement were dealing with.
     * @param $metric FLAG for if the incoming value is metric. If it is not we assume it is imperial and convert it to metric.
     * @return Correctly converted value for given FLAG, $metric
     */
    public function convertUnits($value, $type, $metric) {
        $this->Sensors = TableRegistry::get('Sensors');
        $type_text = $this->Sensors->enumKeyToValue('data_type',$type);
        switch($type_text) {
            case 'Temperature':
                return $metric ? $value : number_format(($value - 32) * 5/9, 3);
            case 'Weight':
                return $metric ? $value : $value * 0.45359237;
            default:
                return $value;         
        }
    }

    /**
     * @param $value Value to be displayed
     * @param $type Type of measurement
     * @param $metric FLAG for the unit system needed
     * @return Correct value based on the unit system flag
     */
    public function displayUnits($value, $type, $metric) {
        $this->Sensors = TableRegistry::get('Sensors');
        $type_text = $this->Sensors->enumKeyToValue('data_type',$type);
        switch($type_text) {
            case 'Temperature':
                return ( !$metric ) ? number_format(($value * 9/5) + 32, 2) : round($value, 2);
            case 'Weight':
                return ( !$metric ) ? round($value * 2.204622621848776, 3) : round($value, 3);
            default:
                return $value;
        }
    }
}