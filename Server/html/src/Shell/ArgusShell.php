<?php

namespace App\Shell;

use Cake\Console\Shell;
use App\Lib\SystemEventRecorder;
use App\Lib\Integrations\ArgusApi;
use InfluxDB\Point;
use InfluxDB\Client;
use InfluxDB\Database;
use Cake\I18n\Time;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;

# This class is responsible for scraping Argus for data we care about and storing the data in InfluxDB
class ArgusShell extends Shell
{
    public function main()
    {
        $time = time();
        $recorder = new SystemEventRecorder();
        $argusApi = new ArgusApi();

        # Load the params from a text file
        $file = new File (APP . '/Shell/params.txt');
        $allParameters = preg_split('/\n/',$file->read());

        // # Override the parameters loaded from file for testing
        // $allParameters = [
        //     2453,
        //     4429,
        //     1921,
        //     4433,
        //     1813,
        //     1814,
        //     1922,
        // ];

        // # Prepare an output file to store which params were valid for next run
        $fileOut = new File (APP . '/Shell/params_valid.txt');
        $fileOut->delete();
        $fileOut->create();

        # Keep this as 1, or refactor it out. Grouping the parameters can cause weird things to return.
        $parametersPerRequest = 1;

        if (env('ARGUS_URL')) {
            $this->out('Starting ArgusShell');
            $this->out('Update Rate: '.env('ARGUS_RATE'));
            $this->out('========================================');

            while (true) {
                // Check every X seconds
                if (time() - $time > env('ARGUS_RATE')) {
                    # Copy the parameters into a new array we can slice up as needed
                    $parameters = $allParameters;

                    while ($parameters) {
                        $points = [];

                        # We request multiple sets of data points at once, as the request is more efficient that way
                        # Create a semi-colon separated list of parameters to pass to the argus api
                        # Actually don't do this. It results in unexpected responses.
                        $parameterGroup = join(';',array_slice($parameters,0,$parametersPerRequest));
                        $this->out('Getting: '.$parameterGroup.' Remaining: '.sizeof($parameters));
                        # Remove those parameters from the original list
                        $parameters = array_slice($parameters, $parametersPerRequest);

                        # Query the Argus API for the past hour of parameter data
                        try {
                            $response = $argusApi->getParameter($parameterGroup,'?from=1h');
                        } catch (\Exception $e) {
                            $this->out('Querying Argus API failed.');
                        }

                        if ($response) {
                            foreach($response->DataList as $list) {
                                $recordCount = sizeof($list->DataSet);
                                if ($recordCount) {
                                    foreach ($list->DataSet as $set) {
                                        $values = preg_split('/ /', $set->Data);
                                        $this->out($set->Time);
                                        $this->out($set->Data);
                                        $time = new Time($set->Time);
                                        $time = $time->modify('+6 hours');
                                        # Create a new InfluxDB point for the results
                                        $point = new Point(
                                            "argus", // name of the measurement
                                            (float) $values[0], // the measurement value
                                            [
                                                'facility_id' => env('FACILITY_ID'),
                                                'parameter_id' => $list->Parameter,
                                                'unit' => (isset($values[1]) ? $values[1] : null),
                                            ],
                                            [], // optional additional fields
                                            $time->toUnixString() // Time precision has to be set to seconds!
                                        );
                                        array_push($points,$point);
                                    }
                                    $this->out('From: '.$list->Parameter.' '.$list->{'Parameter Label'});
                                    $this->out("");

                                    $fileOut->write($list->Parameter."\n");
                                } else {
                                    # We didn't get any records back from this. Ignore it.
                                }
                            }

                            # Now that we have all the data, send it all to Influx
                            try {
                                $database = Client::fromDSN(sprintf('influxdb://root:root@%s:%s/%s', env('INFLUX_HOST'), env('INFLUX_PORT'), "integration_data"));
                                $result = $database->writePoints($points, Database::PRECISION_SECONDS);
                            } catch (\Exception $e) {
                                $this->out('Writing argus data to influxdb failed');
                                $this->out($e);
                            }
                        }

                        # Give argus some breathing room
                        sleep(1);
                    }
                    $time = time();
                    $this->out("Tick: " . $time);
                }
                sleep(1);
            }
        } else {
            $this->out('No ARGUS_URL set. Exiting.');
        }
    }
}
