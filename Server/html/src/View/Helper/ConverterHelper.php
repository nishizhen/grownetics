<?php
namespace App\View\Helper;

use Cake\View\Helper;
use Cake\View\View;
use Cake\ORM\TableRegistry;

class ConverterHelper extends Helper
{
    public function displayValue($val, $enum)
    {
    	$this->Users = TableRegistry::get('Users');
    	$this->Sensors = TableRegistry::get('Sensors');
    	$id = $this->request->session()->read('Auth.User.id');
    	$user = $this->Users->get($id);
    	$type = $this->Sensors->enumKeyToValue('sensor_type', $enum);

    	if ($user->show_metric == false && ($type == 'Waterproof Temperature' || $type == 'Air Temperature')) {
            if ($val == 0.00) {
                return;
            } else {
                $val = ($val * 9 / 5) + 32;
                return $val;   
            }
    	} else {
    		return $val;
    	} 
    }

    public function displaySymbol ($enum) {
    	$this->Users = TableRegistry::get('Users');
    	$this->Sensors = TableRegistry::get('Sensors');
    	$id = $this->request->session()->read('Auth.User.id');
    	$user = $this->Users->get($id);
    	$type = $this->Sensors->enumKeyToValue('sensor_type', $enum);
    	
    	if ($user->show_metric == true && ($type == 'Waterproof Temperature' || $type == 'Air Temperature')) {
    	    return $this->Sensors->enumKeyToValue('sensor_metric_symbols', $enum);
    	} else {
    		return $this->Sensors->enumKeyToValue('sensor_symbols', $enum);
    	}
    }
}
?>