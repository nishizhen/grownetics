jQuery().ready(function() {
	$('#RuleDataSource').on('change',function(e) {
		$('#reset_threshold_wrap').slideUp();
		$('#reset_threshold_time_wrap').slideUp();
		// $('#general_rule_settings').slideUp();
		switch (e.currentTarget.value) {
			// Data Point
			case "0":
			// Zone
			case "1":
				// Show Data Type, Trigger Threshold
				$('#data_type_wrap').slideDown();
				// Hide Trigger Threshold time
				$('#trigger_threshold_time_wrap').slideUp();
				$('#trigger_threshold_wrap').slideDown();
				// If it's a data point, hide 'rule type'
				if (e.currentTarget.value == "0") {
					$('#rule_type_wrap').slideUp();	
				} else {
				// It's a zone, so show rule type (which is basically 'zone rule sensor style')
					$('#rule_type_wrap').slideDown();
				}
			break;
			// Time
			case "3":
				// Show Trigger Threshold as Time Picker
				$('#trigger_threshold_time_wrap').slideDown();
				$('#general_rule_settings').slideDown();
				// Hide data type, non-time trigger threshold
				$('#data_type_wrap').slideUp();
				$('#trigger_threshold_wrap').slideUp();
			break;
			// Interval	
			case "4":
				// Show Trigger Threshold as Time Picker
				$('#trigger_threshold_wrap').slideDown();				
				$('#general_rule_settings').slideDown();
				// Hide data type, non-time trigger threshold
				$('#data_type_wrap').slideUp();
				$('#trigger_threshold_time_wrap').slideUp();
			break;

		}
	});
	$('#RuleDataType').on('change',function(e) {
		if ($('#RuleDataType').val() > 0) {
			$('#numerical_data_wrap').slideDown();
			$('#trigger_threshold_wrap').slideDown();
			$('#general_rule_settings').slideDown();
		
			switch (e.currentTarget.value) {
				// Water 
				case "1":
					$('#trigger_threshold_wrap').find('i').prop('class','wi wi-raindrops');
					$('#reset_threshold_wrap').find('i').prop('class','wi wi-raindrops');
				break;
				// Humidity
				case "2":
					$('#trigger_threshold_wrap').find('i').prop('class','wi wi-humidity');
					$('#reset_threshold_wrap').find('i').prop('class','wi wi-humidity');
				break;
				// Air Temp
				case "3":
					$('#trigger_threshold_wrap').find('i').prop('class','wi wi-thermometer');
					$('#reset_threshold_wrap').find('i').prop('class','wi wi-thermometer');
				break;
					// '4' => 'Co2 Sensor',
				case "4":
					$('#trigger_threshold_wrap').find('i').prop('class','fa fa-percent');
					$('#reset_threshold_wrap').find('i').prop('class','fa fa-percent');
				break;
					// '5' => 'pH Sensor',
				case "5":
					$('#trigger_threshold_wrap').find('i').prop('class','wi wi-raindrop');
					$('#reset_threshold_wrap').find('i').prop('class','wi wi-raindrop');
				break;
					// '6' => 'DO Sensor',
				case "6":
					$('#trigger_threshold_wrap').find('i').prop('class','wi wi-humidity');
					$('#reset_threshold_wrap').find('i').prop('class','wi wi-humidity');
				break;
					// '7' => 'EC Sensor',
				case "7":
					$('#trigger_threshold_wrap').find('i').prop('class','wi wi-dust');
					$('#reset_threshold_wrap').find('i').prop('class','wi wi-dust');
				break;
					// '8' => 'CT Sensor',
				case "8":
					$('#trigger_threshold_wrap').find('i').prop('class','wi wi-lightning');
					$('#reset_threshold_wrap').find('i').prop('class','wi wi-lightning');
				break;	
					// '9' => 'Fill Level Sensor',
				case "9":
					$('#trigger_threshold_wrap').find('i').prop('class','wi wi-flood');
					$('#reset_threshold_wrap').find('i').prop('class','wi wi-flood');
				break;
					// '10' => 'Relay Output',
				case "10":
					$('#trigger_threshold_wrap').find('i').prop('class','wi wi-lightning');
					$('#reset_threshold_wrap').find('i').prop('class','wi wi-lightning');
				break;
					// '11' => 'PAR Sensor'
				case "11":
					$('#trigger_threshold_wrap').find('i').prop('class','wi wi-day-sunny');
					$('#reset_threshold_wrap').find('i').prop('class','wi wi-day-sunny');
				break;
				default:
				console.log("99")
					$('#numerical_data_wrap').slideUp();
					$('#trigger_threshold_wrap').slideUp();
					$('#general_rule_settings').slideUp();
			
					$('#trigger_threshold_wrap').find('i').prop('class','fa fa-question');
					$('#reset_threshold_wrap').find('i').prop('class','fa fa-question');
				break;
			}
		}
	});
	$('#RuleAutoreset').on('change',function(e) {
		if ($('#RuleAutoreset:checked').length > 0) {
			if ($('#RuleDataSource').val()=="3"||$('#RuleDataSource').val()=="4") {
				$('#reset_threshold_time_wrap').slideDown();
				$('#reset_threshold_wrap').slideUp();
			} else {
				$('#reset_threshold_time_wrap').slideUp();
				$('#reset_threshold_wrap').slideDown();
			}
		} else {
			$('#reset_threshold_wrap').slideUp();
			$('#reset_threshold_time_wrap').slideUp();
		}
	});
	$('#RuleActionType').on('change',function(e) {
		switch (e.currentTarget.value) {
			// Turn on, off, or toggle
			case "2":
			case "3":
			case "4":
				$('#output_id_wrap').slideDown();
			break;
			default:
				$('#output_id_wrap').slideUp();
			break;
		}
		
	});
	$('#RuleActionType,#RuleDataType,#RuleAutoreset,#RuleDataSource').trigger('change');
});


// $('#timepicker').timepicker({
//     showPeriod: true,
//     showLeadingZero: true
// });
