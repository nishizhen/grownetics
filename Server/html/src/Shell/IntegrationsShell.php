<?php

namespace App\Shell;

use Cake\Console\Shell;
use Cake\ORM\TableRegistry;
use InfluxDB\Point;
use App\Lib\Integrations\InfisenseApi;
use App\Lib\TimeSeriesWrapper;

/**
 * @property \App\Model\Table\NotificationsTable $Notifications
 * @property \App\Model\Table\SensorsTable $Sensors
 * @property \App\Model\Table\DevicesTable $Devices
 * @property \App\Model\Table\DataPointsTable $DataPoints
 * @property \App\Model\Table\RulesTable $Rules
 * @property \App\Model\Table\ChatsTable $Chats
 * @property \App\Model\Table\UsersTable $Users
 * @property \App\Model\Table\ZonesTable $Zones
 * @property \App\Model\Table\UserContactMethodsTable $UserContactMethods
 */
class IntegrationsShell extends Shell
{
  public $clients = NULL;

  public function initialize()
  {
    $this->loadModel('Notifications');
    $this->loadModel('Devices');
    $this->loadModel('Zones');
    $this->loadModel('Rules');
    $this->loadModel('DataPoints');
  }

  public function poll()
  {
    $infisenseApi = new InfisenseApi();
    $infisenseApi->poll();
  }

  # Called with bin/cake integrations backfill 2020-11-13 2020-11-15
  public function backfill($startText, $endText = false)
  {
    $this->Devices = TableRegistry::get("Devices");
    $this->Sensors = TableRegistry::get("Sensors");
    $infisense = new InfisenseApi();
    $loopStartTime = new \DateTime($startText);
    # Default to now if we don't have a specific end time
    if ($endText) {
      $endTime = new \DateTime($endText);
    } else {
      $endTime = new \DateTime();
    }

    while ($loopStartTime < $endTime) {
      $loopEndTime = clone $loopStartTime;
      date_add($loopEndTime, date_interval_create_from_date_string('2 hours'));
      print_r("Processing " . date_format($loopStartTime, 'Y-m-d H:m:s') . " to " . date_format($loopEndTime, 'Y-m-d H:m:s') . "\n");

      $data = $infisense->query($loopStartTime, $loopEndTime);
      
      $infisense->processBulkData($data);
      $loopStartTime = $loopEndTime;
    }
  }
}


