<?php
namespace App\View\Helper;

use Cake\View\Helper;
use Cake\View\View;
use Cake\Cache\Cache;

/**
 * Cache helper
 */
class CacheHelper extends Helper
{

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    public function get($key) {
        return Cache::read($key);
    }
}
