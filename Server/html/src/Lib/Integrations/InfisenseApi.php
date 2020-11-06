<?php

namespace App\Lib\Integrations;

use Cake\ORM\TableRegistry;
use Cake\Cache\Cache;
use App\Lib\SystemEventRecorder;
use InfluxDB\Point;
use InfluxDB\Client;
use InfluxDB\Database;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;


# This class scrapes data from our remote integrations
class InfisenseApi
{

  public function scrape()
  {
    # Query Infisense
    $curl = curl_init();

    $now = new \DateTime();
    $hourAgo = new \DateTime('-2 days');

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://api.infisense.com/data/?start_time=".urlencode($hourAgo->format("Y-m-d H:i:s"))."&end_time=".urlencode($now->format("Y-m-d H:i:s"))."&return_type=json",
      CURLOPT_HTTPHEADER => array(
        "x-api-key: " . env('INFISENSE_API_KEY'),
        "accept: application/json"
      ),
      CURLOPT_RETURNTRANSFER => true
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    $jsonResponse = json_decode($response);
    if (property_exists($jsonResponse,'message')) {
      print_r($response);
      return false;
    }
    $data = $jsonResponse->data;

    $points = [];

    $connection = new AMQPStreamConnection('rabbitmq', 5672, 'rabbit', 'rabbit');
    $channel = $connection->channel();

    $channel->queue_declare('data.sensor', false, false, false, false);
    $channel->exchange_declare('data.sensor', 'fanout', false, false, false);
    $channel->queue_bind('data.sensor', 'data.sensor');

    if (!$data) {
      return false;
    }

    # Store Infisense Data in InfluxDB
    foreach ($data as $dataPoint) {
      $points[] = new Point(
        'infisense', // name of the measurement
        (float) $dataPoint[4], // the measurement value
        [
          'source_type' => 0,
          'type' => $dataPoint[1],
          'facility_id' => env('FACILITY_ID'),
          'source_id' => $dataPoint[0],

        ],
        [], // optional additional fields
        strtotime($dataPoint[3])
      );
    }

    # Attempt to save the DataPoint to the local Influx DB instance.
    try {
      $database = Client::fromDSN(sprintf('influxdb://root:root@%s:%s/%s', env('INFLUX_HOST'), env('INFLUX_PORT'), 'integration_data'));
      // we are writing unix timestamps, which have a second precision
      $result = $database->writePoints($points, Database::PRECISION_SECONDS);
    } catch (\Exception $e) {
      # Failed to save to influx. As above should probably create an alert here
    }

    # Filter down to the latest datapoint from each device
    $latestDataPoints = [];
    foreach ($data as $dataPoint) {
      $latestDataPoints[$dataPoint[0]] = $dataPoint;
    }
    print_r($latestDataPoints);
    $this->Devices = TableRegistry::get("Devices");
    foreach ($latestDataPoints as $dataPoint) {
      # Get Device ID for Inifisense ID
      $device = $this->Devices->findByExternalId($dataPoint[0])->first();

      if ($device) {
        # Send data to RabbitMQ
        try {
          $json = json_encode(array(array(
            'value' => (float) $dataPoint[4],
            // 'source_id' => $dataPoint[0],
            'source_id' => 643,
            'source_type' => 0,
            // 'type' => $dataPoint[1],
            'type' => 17,
            'device_id' => $device->id,
            'created' => (string) date("Y-m-d H:i:s"),
            'facility_id' => (float) env('FACILITY_ID')
          )));
          print_r($json);
          $msg = new AMQPMessage($json);
          $channel->basic_publish($msg, 'data.sensor', 'data.sensor');

          $channel->close();
          $connection->close();
        } catch (\Exception $e) {
          // Couldn't connect to AMQP server. Should probably create a notification here
          // but with a flag to only create one, so there aren't a ton of notifications created
          // every time the server dies for some reason.
          // $shell->out($e);
          //   print_r($e);die("?");
        }
      }
    }
  }
}
