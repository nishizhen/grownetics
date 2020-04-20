<?php
/**
 * Batches Controller
 *
 * @property Batch $Batch
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
namespace App\Controller;

use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\I18n\Time;
use Cake\Cache\Cache;

use App\Lib\SystemHealth;
use App\Lib\BackupImporter;

/**
 * @property \App\Model\Table\ChatsTable $Chats
 * @property \App\Model\Table\SensorsTable $Sensors
 * @property \App\Model\Table\DevicesTable $Devices
 */
class DashController extends AppController {

/**
 * Components
 *
 * @var array
 */
    public $components = array('Paginator', 'RequestHandler');

/**
 * index method
 *
 * @return void
 */
    public function index() {
        $this->loadModel("Users");
        try {
            $this->loadModel("Floorplans");
            $this->floorplan = $this->Floorplans->find()->last();
        } catch (RecordNotFoundException $e) {
            $this->log($e, 'debug');
            $this->floorplan = NULL;
        }

        $this->loadModel('Chats');
        $params = array(
            'order' => array('Chats.created' => 'asc'),
            'limit' => 50,
            'fields' => array('Chats.created','Chats.message')
        );
        $chats = $this->Chats->find('all',$params)
        ->contain([
            'Users' => function ($q) {
               return $q
                    ->select(['name','email']);
            }
        ]);
        $chatData = [];
        $this->set('chats',$chats);
        foreach ($chats as $chat) {
            $chatData[] = $chat;
        }
        $this->set('chatData', $chatData);

        $this->set('floorplan', $this->floorplan);

        $id = $this->request->session()->read('Auth.User.id');
        $user = $this->Users->get($id);
        $this->set('showMetric', $user->show_metric);

        // Test server's internet connection
        $online = $this->testInternetConnection();
        $this->set('online',$online);

        $id = $this->request->session()->read('Auth.User.id');
        $user = $this->Users->get($id);
        $configs = json_decode($user['dashboard_config']);
        $this->set('configs', $configs);
    }

    function testInternetConnection() {
        $result = $this->curl_download('http://google.com');
        if (stristr($result,'301 Moved')) {
            return true;
        } else {
            return false;
        }
    }

    function curl_download($Url){

        // is cURL installed yet?
        if (!function_exists('curl_init')){
            return false;
        }

        // OK cool - then let's create a new cURL resource handle
        $ch = curl_init();

        // Now set some options (most are optional)

        // Set URL to download
        curl_setopt($ch, CURLOPT_URL, $Url);

        // Set a referer
        curl_setopt($ch, CURLOPT_REFERER, "http://www.example.org/yay.htm");

        // User agent
        curl_setopt($ch, CURLOPT_USERAGENT, "MozillaXYZ/1.0");

        // Include header in result? (0 = yes, 1 = no)
        curl_setopt($ch, CURLOPT_HEADER, 0);

        // Should cURL return or print out the data? (true = return, false = print)
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Timeout in seconds
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        // Download the given URL, and return output
        $output = curl_exec($ch);

        // Close the cURL resource, and free system resources
        curl_close($ch);

        return $output;
    }

    public function server() {

    }

    public function admin() {
        
    }

    public function status() {

        $url = "http://".env('RABBIT_HOST').":".env('RABBIT_PORT')."/api/vhosts";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, env('RABBIT_USER').":".env('RABBIT_PASS'));
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $json = json_decode(curl_exec($curl));
        $result = false;
        if (is_array($json)) {
            $result = $json[0];
        }
        curl_close($curl);
        $this->set('messageStats',$result);

        $this->loadModel('Devices');
        $this->loadModel('DataPoints');

        $workingDevices = 0;
        $devices = $this->Devices->find('all');
        foreach ($devices as $device) {
            if ($time = Cache::read('device-' . $device['id'])) {

                if (time() - strtotime($time['last_message']) < 60 * 5) {
                    $workingDevices++;
                }
            }
        }
        $this->set('workingDevices',$workingDevices);
        $this->set('totalDevices',$this->Devices->find('all')->count());

        $systemHealth = new SystemHealth();
        $this->set('hdd',$systemHealth->hdd());
        $this->set('growpulse',$systemHealth->growpulse());
        $this->set('appdb',$systemHealth->appdb());
        $this->set('highTempShutdown',$systemHealth->highTempShutdown());
        $this->set('deviceBoots',$systemHealth->deviceBoots());
        $this->set('dataReceived',$systemHealth->dataReceived());
        $this->set('bacnetUpdates',$systemHealth->bacnetUpdates());
        $this->set('overrides',$systemHealth->overrides());
        $this->set('ruleAlerts',$systemHealth->ruleAlerts());
        $this->set('outputSchedules',$systemHealth->outputSchedules());
        $this->set('notifications',$systemHealth->notifications());
        $this->set('powerPanels',$systemHealth->powerPanels());
    }

//    function testInternetConnection()
//    {
//        $result = $this->curl_download('http://google.com');
//        if (stristr($result, '301 Moved')) {
//            return true;
//        } else {
//            return false;
//        }
//    }

/**
 * config method
 *
 * lookup dashboard config by user
 * @return void
 */
    public function config() {
        $this->loadModel("Users");

        $id = $this->request->session()->read('Auth.User.id');

        $user = $this->Users->get($id);


        if ($this->request->is('post') && isset($this->request->data['dashboard_config'])) {
            $user['dashboard_config'] = json_encode($this->request->data['dashboard_config']);

            $this->log('saving dashboard config...', 'debug');
            $this->log($user['dashboard_config'], 'debug');

            $this->Users->save($user);
        }


        $dashboard_config = $user['dashboard_config'];

        $this->set('dashboard_config', $dashboard_config);

        $this->set('_serialize',array( 'dashboard_config'));

    }

    public function featureFlags() {
        $sf = new \SensioLabs\Consul\ServiceFactory(['base_uri' => 'http://consul:8500']);
        $kv = $sf->get('kv');
        $flagValue = $kv->get('feature_flags', ['raw' => true,'recurse'=>true])->getBody();
        $flags = json_decode($flagValue);
        $this->set('flags',$flags);
    }

    public function backups($hostname=null) {
        $backupImporter = new BackupImporter();
        if ($hostname) {
            $backupImporter->restoreLatestFromHost($hostname);
            $this->redirect('/');
        } else {
            $hosts = $backupImporter->getHostList();
            $this->set('hosts',$hosts);
        }
    }
}