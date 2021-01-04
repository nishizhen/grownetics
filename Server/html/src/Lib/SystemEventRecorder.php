<?php
namespace App\Lib;

use InfluxDB\Point;
use InfluxDB\Client;
use InfluxDB\Database;
use Cake\Log\Log;

class SystemEventRecorder {
    public function recordEvent($db, $eventLabel, $value, $tags=[])
    {
        # We need to store everything as floats. Dirty hack for Influx.
        if (!is_float($value)) {
            $value=(float) $value;
        }
        try {
            $time = time();
            $points = [
                new Point(
                    $eventLabel, // name of the measurement
                    $value, // the measurement value
                    array_merge([
                        'facility_id' => env('FACILITY_ID'),
                    ],$tags),
                    [], // optional additional fields
                    $time // Time precision has to be set to seconds!
                )
            ];
            $database = Client::fromDSN(sprintf('influxdb://%s:%s@%s:%s/%s', env('INFLUX_USER'), env('INFLUX_PASS'), env('INFLUX_HOST'), env('INFLUX_PORT'), $db));
            $result = $database->writePoints($points, Database::PRECISION_SECONDS);
        } catch (\Exception $e) {
            Log::write('error', 'Writing system event to influxdb failed');
        }
    }
}