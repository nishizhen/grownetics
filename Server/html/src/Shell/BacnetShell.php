<?php

namespace App\Shell;

use Cake\Console\Shell;


/**
 * @property \App\Model\Table\ZonesTable $Zones
 */
class BacnetShell extends Shell
{
    public $clients = NULL;

    public function initialize()
    {
        $this->loadModel('Zones');
    }

    public function main()
    {
        $time = time();
        $this->out('Starting BacnetShell');
        $this->out('BACnet Url: '.env('BACNET_URL'));
        $this->out('Update Rate: '.env('BACNET_RATE'));
        $this->out('========================================');

        while (true) {
            // Check every X seconds
            if (time() - $time > env('BACNET_RATE')) {
                $this->out('Update Zone BACnet Points');
                $this->Zones->updateBacnetPoints($this);

                $time = time();
                $this->out("Tick: " . $time);
            }
            sleep(1);
        }
    }
}
