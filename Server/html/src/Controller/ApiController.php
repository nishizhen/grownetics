<?php
/**
 * Batches Controller
 *
 * @property Batch $Batch
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
namespace App\Controller;

use Cake\Cache\Cache;
use Cake\Chronos\Chronos;
use Aura\Intl\Exception;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

use App\Lib\SystemEventRecorder;

/**
 * @property \App\Model\Table\DevicesTable $Devices
 * @property \App\Model\Table\ZonesTable $Zones
 * @property \App\Model\Table\RulesTable $Rules
 * @property \App\Model\Table\OutputsTable $Outputs
 * @property \App\Model\Table\NotificationsTable $Notifications
 * @property \App\Model\Table\RuleConditionsTable $RuleConditions
 */
class ApiController extends AppController
{

    public function isAuthorized($user)
    {
        if (in_array($this->request->action, [
            'raw', 'reboot', 'testp'
        ])) {
            return true;
        }

        return parent::isAuthorized($user);
    }

    public $components = array('Paginator');

    public function test()
    {
        $response = '';
        if ($this->request->is('post')) {
            $params = array('q' => '{"id":' .
                $this->request['data']['device'] . ',"v":"1.0.5","st":1,"m":100,"d":"' .
                $this->request['data']['data'] . '"}');
            $response = $this->requestAction('/api/raw', $params);
        }
        $this->loadModel('Devices');
        $devices = $this->Devices->find('list');
        $this->set(compact('devices'));
        $this->set('response', $response);
    }

    public function tlc()
    {
        $this->loadModel('Devices');
        $this->loadModel('Outputs');
        $this->loadModel('Notifications');
        $this->loadModel('Rules');
        $this->loadModel('RuleConditions');
        $this->loadModel('RuleActions');

        if (!isset($this->request->query['debug'])) {
            $this->viewBuilder()->layout('blank');
            $this->response->type('text/plain');
        }

        if ($this->request->is('get')) {
            $data = json_decode(stripslashes($this->request->getQuery('q')), true);
        } else if ($this->request->is('post')) {
            if (isset($this->request->data['data'])) {
                $data = $this->request->data['data']['q'];
            } else {
                $data = $this->request->params['q'];
            }
            $data = json_decode(stripslashes($data), true);
        }

        $sensorsData = '';

        // If we received a boot request, or if we know the device to need to reboot
        try {
            if (($device = Cache::read('device-' . $data['id'])) === false || !isset($device['id'])) {
                $device = $this->Devices->get($data['id']);
                if (isset($data['v'])) {
                    $device['version'] = $data['v'];
                }
                Cache::write('device-' . $data['id'], $device);
            }
        } catch (\Exception $e) {
            $this->set('response', "{'error':'Device not found.'}");
            return;
        }

        $recorder = new SystemEventRecorder();

        if (
            isset($data['b'])
            ||
            (
                isset($data['st'])
                &&
                (
                    $this->Devices->isRebooting($device)
                    &&
                    $data['st'] < 1
                )
            )
        ) {
            $sensorsData = $this->Devices->getSensors($device);

            # Record boot event to InfluxDB
            # Value is 1 because there was 1 boot
            $recorder->recordEvent('system_events', 'device_boot', 1, ['device_id' => $device->id]);

            if ($device->type == $this->Devices->enumValueToKey('type', 'Control')) {
                $notificationData = array(
                    'source_type' => $this->Notifications->enumValueToKey('source_type', 'Device'),
                    'status' => $this->Notifications->enumValueToKey('status', 'Queued'),
                    'message' => 'Control Device ' . $device->id . ' is rebooting',
                    'notification_level' => $this->RuleActions->enumValueToKey('notification_level', 'Dashboard Alert')
                );
                $notification = $this->Notifications->newEntity($notificationData);
                $this->Notifications->save($notification);
            }
        }

        $date = new Chronos($device->last_message);
        if ($device->last_message && !$date->wasWithinLast('10 minutes')) {
            $notificationData = $this->Notifications->newEntity(array(
                'status' => 0,
                'message' => "Device " . $data['id'] . " rebooted.",
                'notification_level' => $this->RuleActions->enumValueToKey('notification_level', 'Dashboard Alert')
            ));
            $this->Notifications->save($notificationData);
        }

        if (isset($data['d'])) {
            $this->Devices->updateDeviceInfo($device, $data);

            $recorder->recordEvent('system_events', 'data_received', 1, ['type' => 'Devices', 'device_id' => $device->id]);
            $data = $this->Devices->processData($data);

            $sensors = $data['sensorArray'];

            $ruleConditionsQuery = [];
            if ($sensors) {
                $ruleConditionsQuery = [
                    'conditions' => [
                        'data_source' => $this->RuleConditions->enumValueToKey('data_source', 'Sensor'),
                        'data_id IN' => $sensors
                    ],
                    'contain' => [
                        'Rules'
                    ]
                ];
            }

            # Toggles conditions as needed
            $ruleConditionIds = $this->RuleConditions->processConditions($ruleConditionsQuery);
            # Checks condition status and toggles rules as needed
            if ($ruleConditionIds) {
                $this->Rules->processRules($ruleConditionIds);
            }
        }
        //            if (($outputsData = Cache::read('outputs-for-device-'.$data['id'])) === false) {
        $outputsData = $this->Outputs->getOutputsAndTimedScheduleForDevice($device->id);
//                Cache::write('outputs-for-device-'.$data['id'], $outputsData);
//            }

        $response = $outputsData;

        if (strlen($response) > 0 && strlen($sensorsData) > 0) {
            $response .= ',' . $sensorsData;
        } else if (strlen($sensorsData) > 0) {
            $response = $sensorsData;
        }

        // MAINTENANCE MODE! So elegant.
        //if (FALSE) {
        //	if (strlen($response) > 0) {
        //		$response .= ',';
        //	}
        //	$response .= '"maintenance":0';
        //}
        $this->set('response', '{' . $response . '}');
    }

    public function raw()
    {
        $this->loadModel('Devices');
        $this->loadModel('Outputs');
        $this->loadModel('Notifications');
        $this->loadModel('Rules');
        $this->loadModel('RuleConditions');
        $this->loadModel('RuleActions');

        if (!isset($this->request->query['debug'])) {
            $this->viewBuilder()->layout('blank');
            $this->response->type('text/plain');
        }

        if ($this->request->is('get')) {
            $data = json_decode(stripslashes($this->request->getQuery('q')), true);
        } else if ($this->request->is('post')) {
              $data = $this->request->getQueryParams()['q'];
            $data = json_decode(stripslashes($data), true);
        }

        $sensorsData = '';
        // If we received a boot request, or if we know the device to need to reboot
        try {
            // if (($device = Cache::read('device-'.$data['id'])) === false || !isset($device['id'])) {
                $device = $this->Devices->get($data['id']);
            //     if (isset($data['v'])) {
            //         $device['version'] = $data['v'];
            //     }
            //     Cache::write('device-'.$data['id'], $device);
            // }
        } catch (\Exception $e) {
            $this->set('response', "{'error':'Device not found.'}");
            return;
        }

        $recorder = new SystemEventRecorder();

        $date = new Chronos($device->last_message);
        if ($device->last_message && !$date->wasWithinLast('10 minutes')) {
            $notificationData = $this->Notifications->newEntity(array(
                'status' => 0,
                'message' => "Device " . $data['id'] . " rebooted.",
                'notification_level' => $this->RuleActions->enumValueToKey('notification_level', 'Dashboard Alert')
            ));
            $this->Notifications->save($notificationData);
        }

        if (isset($data['d'])) {
            $this->Devices->updateDeviceInfo($device, $data);

            $recorder->recordEvent('system_events','data_received', 1, ['type' => 'Devices', 'device_id' => $device->id]);
            $data = $this->Devices->processData($data);

            $sensors = $data['sensorArray'];

            $ruleConditionsQuery = [];
            if ($sensors) {
                $ruleConditionsQuery = [
                    'conditions' => [
                        'data_source' => $this->RuleConditions->enumValueToKey('data_source', 'Sensor'),
                        'data_id IN' => $sensors
                    ],
                    'contain' => [
                        'Rules'
                    ]
                ];
            }

            # Toggles conditions as needed
            $ruleConditionIds = $this->RuleConditions->processConditions($ruleConditionsQuery);
            # Checks condition status and toggles rules as needed
            if ($ruleConditionIds) {
                $this->Rules->processRules($ruleConditionIds);
            }
        }

        if (
            isset($data['b'])
            ||
            (
                isset($data['st'])
                &&
                (
                    $this->Devices->isRebooting($device)
                    &&
                    $data['st'] < 1
                )
            )
        ) {
            $sensorsData = $this->Devices->getSensors($device);

            # Record boot event to InfluxDB
            # Value is 1 because there was 1 boot
            $recorder->recordEvent('system_events','device_boot', 1, ['device_id' => $device->id]);

            if ($device->type == $this->Devices->enumValueToKey('type', 'Control')) {
                $notificationData = array(
                    'source_type' => $this->Notifications->enumValueToKey('source_type', 'Device'),
                    'status' => $this->Notifications->enumValueToKey('status', 'Queued'),
                    'message' => 'Control Device ' . $device->id. ' is rebooting',
                    'notification_level' => $this->RuleActions->enumValueToKey('notification_level', 'Dashboard Alert')
                );
                $notification = $this->Notifications->newEntity($notificationData);
                $this->Notifications->save($notification);

                # Control Device has rebooted, disable the outputs.
                $this->Devices->enableBurnoutProtection($device);
            }
        }

        $outputsData = '';
        if ($device->type == $this->Devices->enumValueToKey('type','Control')) {
            // die("?");
            # Do not return outputs for Burnout Protected devices.
            # http://handbook.cropcircle.io/System/burnout-protection/
            if (!$this->Devices->isBurnoutProtected($device)) {
                $recorder->recordEvent('system_events', 'control_device_returned_outputs', 1, ['device_id' => $device->id]);
                $outputsData = $this->Outputs->getRelayOutputsForDevice($device->id);
            } else {
                $recorder->recordEvent('system_events', 'control_device_burnout_protected', 1, ['device_id' => $device->id]);
            }
        }
        // print_r($device); die("!");

        $response = $outputsData;

        if (strlen($response) > 0 && strlen($sensorsData) > 0) {
            $response .= ',' . $sensorsData;
        } else if (strlen($sensorsData) > 0) {
            $response = $sensorsData;
        }

        // MAINTENANCE MODE! So elegant.
        //if (FALSE) {
        //	if (strlen($response) > 0) {
        //		$response .= ',';
        //	}
        //	$response .= '"maintenance":0';
        //}
        $this->set('response', '{' . $response . '}');
    }

    public function aldc($id) {
        $this->loadModel('Devices');
        $this->viewBuilder()->layout('blank');
        $this->viewBuilder()->template('raw');
        $this->response->type('text/plain');


        try {
            if (($device = Cache::read('device-'.$id)) === false || !isset($device['id'])) {
                $device = $this->Devices->get($id);
                Cache::write('device-'.$id, $device);
            }
        } catch (\Exception $e) {
            $this->set('response', "{'error':'Device not found.'}");
            return;
        }

        $this->Devices->updateDeviceInfo($device, ['id'=>$id]);

        $recorder = new SystemEventRecorder();
        $recorder->recordEvent('system_events','aldc_request', 1, ['device_id' => $device->id]);

        try {
            $sf = new \SensioLabs\Consul\ServiceFactory(['base_uri' => 'http://consul:8500']);
            $kv = $sf->get('kv');
            $response = $kv->get('devices/' . $device['id'] . '/analogPins', ['raw' => true])->getBody();
        } catch (\Exception $e) {
            return false;
        }


        $this->set('response', $response);
    }
}
