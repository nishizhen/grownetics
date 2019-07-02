<?php
/**
 * @var \App\View\AppView $this
 */
?>
<?php
    $className = '';
    switch($data_type) {
	// Water 
	case "1":
		$className = 'wi wi-raindrops';
	break;
	// Humidity
	case "2":
		$className = 'wi wi-humidity';
	break;
	// Air Temp
	case "3":
		$className = 'wi wi-thermometer';
	break;
		// '4' => 'Co2 Sensor',
	case "4":
		$className = 'fa fa-percent';
	break;
		// '5' => 'pH Sensor',
	case "5":
		$className = 'wi wi-raindrop';
	break;
		// '6' => 'DO Sensor',
	case "6":
		$className = 'wi wi-humidity';
	break;
		// '7' => 'EC Sensor',
	case "7":
		$className = 'wi wi-dust';
	break;
		// '8' => 'CT Sensor',
	case "8":
		$className = 'wi wi-lightning';
	break;	
		// '9' => 'Fill Level Sensor',
	case "9":
		$className = 'wi wi-flood';
	break;
		// '10' => 'Relay Output',
	case "10":
		$className = 'wi wi-lightning';
	break;
		// '11' => 'PAR Sensor'
	case "11":
		$className = 'wi wi-day-sunny';
	break;
}
$data_type_text = $this->Enum->enumKeyToValue('Rules','data_type',$data_type);
?>
<button class="btn btn-theme04" type="button" data-toggle="tooltip" data-placement="bottom" title="<?=$data_type_text?>"><i class="<?=$className?>"></i></button>