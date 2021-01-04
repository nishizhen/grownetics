<?php
namespace App\Lib;

use InfluxDB\Point;
use InfluxDB\Client;
use InfluxDB\Database;

class TimeSeriesWrapper {

    # Store data points to the TSDB
    public function store($points,$database_name)
    {
      try {
        $database = Client::fromDSN(sprintf('influxdb://%s:%s@%s:%s/%s', env('INFLUX_USER'), env('INFLUX_PASS'), env('INFLUX_HOST'), env('INFLUX_PORT'), $database_name));
        // we are writing unix timestamps, which have a second precision
        $result = $database->writePoints($points, Database::PRECISION_SECONDS);
      } catch (\Exception $e) {
        # Failed to save to influx. As above should probably create an alert here
      }
    }
  }