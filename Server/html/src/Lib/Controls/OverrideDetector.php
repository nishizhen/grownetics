<?php
namespace App\Lib\Controls;

use Cake\ORM\TableRegistry;
use Cake\Cache\Cache;
use App\Lib\SystemEventRecorder;

# This class detects if any overrides are turned on, and if so, stores that fact in InfluxDB
class OverrideDetector {

    public function detect() {
        # Load all Outputs
        $this->Outputs = TableRegistry::get('Outputs');
        $this->Sensors = TableRegistry::get('Sensors');
        $outputs = $this->Outputs->find('all',[
            'contain' => ['Sensors','Zones']
        ]);

        # Load CT that matches said Output
        foreach ($outputs as $output) {
            if ($output->ct_sensor) {
                $value = Cache::read('sensor-value-' . $output->ct_sensor->id);
                # If the Output appears to be 'ON' and it's status says is one of the 'OFF' modes,
                # then an override is probably on. Record an event.
                if (
                    $value > env('THRESHOLD_CT_ON') && 
                    (
                        $output->status == $this->Outputs->enumValueToKey('status','Off')
                        ||
                        $output->status == $this->Outputs->enumValueToKey('status','Force Off')
                    )
                ) {
                    # If CT is above certain level, store an event
                    $recorder = new SystemEventRecorder();
                    $recorder->recordEvent('system_events', 'output_override_detected', 1, [
                        'value' => $value,
                        'output_id' => $output->id,
                        'sensor_id' => $output->ct_sensor->id,
                    ]);
                }
            }
        }
    }
}