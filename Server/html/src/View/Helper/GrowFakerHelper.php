<?php
namespace App\View\Helper;

use Cake\View\Helper;
use Cake\View\View;

/**
 * GrowFaker helper
 */
class GrowFakerHelper extends Helper
{

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    public function getRequests($device_id) {
        try {
            $sf = new \SensioLabs\Consul\ServiceFactory(['base_uri' => 'http://consul:8500']);
            $kv = $sf->get('kv');
            return $kv->get('faker/devices/' . $device_id . '/stats/requests', ['raw' => true])->getBody();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getMode($device_id) {
        try {
            $sf = new \SensioLabs\Consul\ServiceFactory(['base_uri' => 'http://consul:8500']);
            $kv = $sf->get('kv');
            $mode = $kv->get('faker/devices/' . $device_id . '/mode', ['raw' => true])->getBody();
            switch ($mode) {
                case 0:
                    return "(None)";
                case 1:
                    return "Flat";
                case 2:
                    return "Random";
                case 3:
                    return "Drift";
                case 4:
                    return "Heat";
                case 5:
                    return "Cool";
                case 6:
                    return "Dead";
                case 7:
                    return "Demo";
                case 8:
                    return "Sketchy";
            }
        } catch (\Exception $e) {
            return "(None)";
        }
    }
}
