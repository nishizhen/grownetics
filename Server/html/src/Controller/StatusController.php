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

use InfluxDB\Client;
use InfluxDB\Database;

use App\Lib\SystemHealth;

/**
 * @property \App\Model\Table\DevicesTable $Devices
 * @property \App\Model\Table\ZonesTable $Zones
 * @property \App\Model\Table\RulesTable $Rules
 * @property \App\Model\Table\OutputsTable $Outputs
 * @property \App\Model\Table\NotificationsTable $Notifications
 * @property \App\Model\Table\RuleConditionsTable $RuleConditions
 */
class StatusController extends AppController
{

    public function isAuthorized($user)
    {
        if (in_array($this->request->action, [
            'influxdb', 'hdd', 'appdb', 'growpulse', 'system'
        ])) {
            return true;
        }

        return parent::isAuthorized($user);
    }

    public $components = array('Paginator');

    private function _returnJson($response) {
        $this->loadComponent('RequestHandler');
        $this->RequestHandler->renderAs($this, 'json');
        $this->response->type('application/json');
        $this->set('response', $response);
    }

    public function system()
    {
        $response = 0;

        $systemHealth = new SystemHealth();
        if ($systemHealth->system()) {
            $response = 1;
        }

        $this->_returnJson($response);
    }
}
