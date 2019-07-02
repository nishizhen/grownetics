<?php
/**
 * @var \App\View\AppView $this
 */
	use Cake\Core\Configure;
	$colors = Configure::read('Colors');

	$this->Html->scriptStart(['block' => 'scriptBottom']);
	echo '
		var GrowServer = GrowServer || {};
		(function() {
			GrowServer.Color = {
				getColorForSensorType: function( sensor_type) {
					switch (sensor_type) {';
	foreach ($colors['sensor_types'] as $sensor_type => $color) {
		echo 'case "'.$sensor_type.'": return "'.$color.'";';
	}
	echo '
					}
				},

				getColorForMapItemType: function( map_item_type) {
					switch (map_item_type) {';
	foreach ($colors['map_item_types'] as $map_item_type => $color) {
		echo 'case "'.$map_item_type.'": return "'.$color.'";';
	}
	echo '			}
				},

				getColorForSensorTypeAndValue: function( sensor_type, value) {
					switch (sensor_type) {';
	foreach ($colors['sensor_type_values'] as $sensor_type => $values) {
		echo 'case "'.$sensor_type.'":';
			foreach($values as $value => $color) {
				echo 'if (value > '.$value.') { return "'.$color.'";}';
			}
            echo 'return "'.$color.'";';
		echo '    break;';
	}
	echo '
					}
					
				},
			};
		})();
	';
	$this->Html->scriptEnd();
?>