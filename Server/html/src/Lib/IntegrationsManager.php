<?php
namespace App\Lib;

use Cake\ORM\TableRegistry;
use Cake\Cache\Cache;
use App\Lib\SystemEventRecorder;
use InfluxDB\Point;
use InfluxDB\Client;
use InfluxDB\Database;

# This class scrapes data from our remote integrations
class IntegrationsManager {

    public function scrape() {
        # Query Infisense

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.infisense.com/data/?start_time=2020-05-01%2000%3A00%3A00&end_time=2020-05-05%2001%3A00%3A00&point_types=temp&point_types=relative_humidity&return_type=json",
        CURLOPT_HTTPHEADER => array(
            "x-api-key: " . env('INFINISENSE_API_KEY'),
            "accept: application/json"
        ),
        CURLOPT_RETURNTRANSFER => true
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        $data = json_decode($response)->data;

        $points = [];

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
}
}