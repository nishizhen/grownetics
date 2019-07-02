<?php
namespace App\Lib;

use InfluxDB\Point;
use InfluxDB\Client;
use InfluxDB\Database;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use App\Lib\SystemEventRecorder;
use App\Lib\FeatureFlags;
use Cake\Cache\Cache;

class SystemHealth {

    # Can we contact influxdb, and has growpulse run recently
    public function growpulse()
    {
        // fetch the database
        $database = Client::fromDSN(sprintf('influxdb://root:root@%s:%s/%s', env('INFLUX_HOST'), env('INFLUX_PORT'), 'system_events'));

        // executing a query will yield a resultset object
        try {
            $result = $database->query('select * from growpulse_tick GROUP BY * ORDER BY DESC LIMIT 1');

            // get the points from the resultset yields an array
            $point = $result->getPoints()[0];
            $date = new \DateTime($point['time']);
            $now = new \DateTime();
            if ($now->getTimestamp() - $date->getTimestamp() < 60) {
                return true;
            }

        } catch (\Exception $e ){
            
        }
    }

    # Can we talk to the main application database
    public function appdb()
    {
        $Floorplans = TableRegistry::get('Floorplans');
        $floorplan = $Floorplans->get(1);
        if ($floorplan) {
            return true;
        }
    }

    # Is the hard drive too full
    public function hdd()
    {
        if (round(disk_free_space('/')/disk_total_space('/')*100,2) > 20) {
            return(true);
        }
    }

    # Are any high temp shutdowns enabled
    public function highTempShutdown()
    {
        $Outputs = TableRegistry::get('Outputs');
        $outputs = $Outputs->find('all',[
            'conditions' => [
                'status' => $Outputs->enumValueToKey('status','High Temp Shutdown')
            ]
        ])->count();

        if ($outputs == 0) {
            return true;
        }
    }

    # Are device boot rates within range
    public function deviceBoots()
    {
        if (!env('THRESHOLD_DEVICE_BOOTS')) {
            return false;
        }
        // fetch the database
        $database = Client::fromDSN(sprintf('influxdb://root:root@%s:%s/%s', env('INFLUX_HOST'), env('INFLUX_PORT'), 'system_events'));
        // executing a query will yield a resultset object
        try {
            $result = $database->query('select count("value") as "value" from device_boot  WHERE time > now() - 3m GROUP BY time(1m) ORDER BY DESC LIMIT 2');

            // get the points from the resultset yields an array
            if (count($result->getPoints())) {
                $point = $result->getPoints()[1];
                if ($point['value'] > env('THRESHOLD_DEVICE_BOOTS')) {
                    return false;
                } else {
                    // There are less than the threshold boots, good.
                    return true;
                }
            } else {
                // If there are no device boots, good.
                return true;
            }
        } catch (\Exception $e ){
            return false;
        }
    }

    # Are an overrides detected
    public function overrides()
    {
        // fetch the database
        $database = Client::fromDSN(sprintf('influxdb://root:root@%s:%s/%s', env('INFLUX_HOST'), env('INFLUX_PORT'), 'system_events'));
        // executing a query will yield a resultset object
        try {
            $result = $database->query('select count("value") as "value" from output_override_detected  WHERE time > now() - 3m GROUP BY time(1m) ORDER BY DESC LIMIT 2');

            // get the points from the resultset yields an array
            if (count($result->getPoints())) {
                $point = $result->getPoints()[1];
                if ($point['value']) {
                    return false;
                } else {
                    // There are no overrides on, good
                    return true;
                }
            } else {
                // If there are overrides on, bad
                return true;
            }
        } catch (\Exception $e ){
            return false;
        }
    }

    # Are data receive counts within range
    public function dataReceived()
    {
        if (!env('THRESHOLD_DATA_RECEIVED')) {
            return false;
        }
        // fetch the database
        $database = Client::fromDSN(sprintf('influxdb://root:root@%s:%s/%s', env('INFLUX_HOST'), env('INFLUX_PORT'), 'system_events'));
        // executing a query will yield a resultset object
        try {
            $result = $database->query('select count("value") as "value" from data_received  WHERE time > now() - 3m GROUP BY time(1m) ORDER BY DESC LIMIT 2');

            // get the points from the resultset yields an array
            if (count($result->getPoints())) {
                $point = $result->getPoints()[1];
                if ($point['value'] > env('THRESHOLD_DATA_RECEIVED')) {
                    return true;
                }
            }
        } catch (\Exception $e ){
        }
        return false;
    }

    # Is the BACnet system online?
    public function bacnetUpdates()
    {
        if (env('BACNET_ENABLED')) {
            if (!env('THRESHOLD_BACNET_UPDATES')) {
                return false;
            }
            // fetch the database
            $database = Client::fromDSN(sprintf('influxdb://root:root@%s:%s/%s', env('INFLUX_HOST'), env('INFLUX_PORT'), 'system_events'));
            // executing a query will yield a resultset object
            try {
                $result = $database->query('select count("value") as "value" from bacnet_update  WHERE time > now() - 30m GROUP BY time(10m) ORDER BY DESC LIMIT 3');
                // get the points from the resultset yields an array
                if (count($result->getPoints())) {
                    $point = $result->getPoints()[1];
                    if ($point['value'] > env('THRESHOLD_BACNET_UPDATES')) {
                        return true;
                    }
                }
            } catch (\Exception $e ){
                return false;
            }
        } else {
            return true;
        }
    }

    # Are any alarming rules triggered?
    public function ruleAlerts()
    {
        $RuleActions = TableRegistry::get('RuleActions'); 
        $Rules = TableRegistry::get('Rules');

        $triggeredRuleActionsCount = $RuleActions->find('all',[
            'conditions' => [
                'Rules.status' => $Rules->enumValueToKey('status','Triggered'),
                'notification_level >=' => $RuleActions->enumValueToKey('notification_level','Text Message')
            ],
            'contain' => [
                'Rules'
            ]
        ])->count();

        if ($triggeredRuleActionsCount > 0) {
            return false;
        } else {
            return true;
        }
    }

    # Are any outputs not in the status that the rules say they should be?
    public function outputSchedules() {
        $Outputs = TableRegistry::get('Outputs');
        $Rules = TableRegistry::get('Rules');
        $RuleActions = TableRegistry::get('RuleActions');

        $outputs = $Outputs->find('all',[
            'contain' => ['RuleActionTargets.RuleActions.Rules']
        ]);
        $outputSchedulesOk = true;
        foreach ($outputs as $output) {
            foreach ($output['rule_action_targets'] as $rat) {
                # Ignore unused RATs
                if ($rat['rule_action']) {
                    $rule = $Rules->get($rat['rule_action']->rule_id);
                    if ($rule->type == $Rules->enumValueToKey('type','Lighting Schedule')) {
                        # This whole bit could probably be cleaner, but eh, it works and it's pretty easy to read.
                        if ($rule->status == $Rules->enumValueToKey('status','Triggered')) {
                            if ($rat['rule_action']->type == $RuleActions->enumValueToKey('type','Turn On')) {
                                if ($output->status != $Outputs->enumValueToKey('status','On')) {
                                    $outputSchedulesOk = false;
                                }
                            } else if ($rat['rule_action']->type == $RuleActions->enumValueToKey('type','Turn Off')) {
                                if ($output->status != $Outputs->enumValueToKey('status','Off')) {
                                    $outputSchedulesOk = false;
                                }
                            }
                        } else {
                            if ($rat['rule_action']->type == $RuleActions->enumValueToKey('type','Turn On')) {
                                if ($output->status != $Outputs->enumValueToKey('status','Off')) {
                                    $outputSchedulesOk = false;
                                }
                            } else if ($rat['rule_action']->type == $RuleActions->enumValueToKey('type','Turn Off')) {
                                if ($output->status != $Outputs->enumValueToKey('status','On')) {
                                    $outputSchedulesOk = false;
                                }
                            }
                        }
                    }
                }
            }
        }
        return $outputSchedulesOk;
    }

    # Is notification sending disabled?
    public function notifications() {
        return FeatureFlags::getFlagValue("notification_sending_enabled");
    }

    # Are any power panel devices not online?
    public function powerPanels() {
        $Devices = TableRegistry::get('Devices');
        $devices = $Devices->find('all',[
            'conditions' => ['type' => $Devices->enumValueToKey('type','Control')]
        ]);
        $powerPanelsOk = true;
        foreach ($devices as $device) {
            $cachedDevice = Cache::read('device-'.$device->id);
            if ($cachedDevice['status'] != $Devices->enumValueToKey('status','Active')) {
                $powerPanelsOk = false;
            }
        }
        return $powerPanelsOk;
    }

    # Get an overall status for the system as a whole
    public function system()
    {
        if (
            $this->growpulse() &&
            $this->appdb() &&
            $this->hdd() &&
            $this->highTempShutdown() &&
            $this->deviceBoots() &&
            $this->dataReceived() &&
            $this->bacnetUpdates() &&
            $this->overrides() &&
            $this->ruleAlerts() &&
            $this->outputSchedules() &&
            $this->notifications() &&
            $this->powerPanels()
        ) {
            return true;
        }
    }

    # Store each status individually into influxdb
    public function storeStatuses()
    {
        $recorder = new SystemEventRecorder();
        $recorder->recordEvent('system_events', 'system_status', $this->growpulse(), ['component'=>'growpulse']);
        $recorder->recordEvent('system_events', 'system_status', $this->appdb(), ['component'=>'appdb']);
        $recorder->recordEvent('system_events', 'system_status', $this->hdd(), ['component'=>'hdd']);
        $recorder->recordEvent('system_events', 'system_status', $this->highTempShutdown(), ['component'=>'highTempShutdown']);
        $recorder->recordEvent('system_events', 'system_status', $this->deviceBoots(), ['component'=>'deviceBoots']);
        $recorder->recordEvent('system_events', 'system_status', $this->dataReceived(), ['component'=>'dataReceived']);
        $recorder->recordEvent('system_events', 'system_status', $this->bacnetUpdates(), ['component'=>'bacnetUpdates']);
        $recorder->recordEvent('system_events', 'system_status', $this->overrides(), ['component'=>'overrides']);
        $recorder->recordEvent('system_events', 'system_status', $this->ruleAlerts(), ['component'=>'ruleAlerts']);
        $recorder->recordEvent('system_events', 'system_status', $this->outputSchedules(), ['component'=>'outputSchedules']);
        $recorder->recordEvent('system_events', 'system_status', $this->notifications(), ['component'=>'notifications']);
        $recorder->recordEvent('system_events', 'system_status', $this->powerPanels(), ['component'=>'powerPanels']);
    }
}