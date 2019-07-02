<?php
namespace FeatureFlags\View\Helper;

use Cake\View\Helper;
use Cake\View\View;

/**
 * Flags helper
 */
class FeatureFlagsHelper extends Helper
{
    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    public $helpers = ['Form'];

    public function getFlagValue($flagName) {
        try {
            $sf = new \SensioLabs\Consul\ServiceFactory(['base_uri' => 'http://consul:8500']);
            $kv = $sf->get('kv');
            $flagValue = $kv->get('feature_flags/'.$flagName, ['raw' => true])->getBody();
            return $flagValue;
        } catch (\Exception $e) {
            if ($e->getCode() == 404) {
                # This key does not exist yet, set it to disabled and return that.
                $sf = new \SensioLabs\Consul\ServiceFactory(['base_uri' => 'http://consul:8500']);
                $kv = $sf->get('kv');
                $kv->put('feature_flags/'.$flagName, 0);
                return 0;
            } else {
                # Cannot contact Consul, assume the feature is disabled.
                return 0;
            }
        }
    }

    public function getStatusBadge($flagName) {
        if ($this->getFlagValue($flagName)) {
            return '<span class="label label-success">Enabled</span>';
        } else {
            return '<span class="label label-danger">Disabled</span>';
        }
    }

    public function getToggleLink($flagName) {
        if ($this->getFlagValue($flagName)) {
            return $this->Form->postLink(__('Disable'), ['controller' => 'feature-flags/feature-flags', 'action' => 'disable', 'data'=>['flagName'=>$flagName]], ['confirm' => __('Are you sure you want to disable feature "'.$flagName.'"?')]);
        } else {
            return $this->Form->postLink(__('Enable'), ['controller' => 'feature-flags/feature-flags', 'action' => 'enable', 'data'=>['flagName'=>$flagName]], ['confirm' => __('Are you sure you want to enable feature "'.$flagName.'"?')]);
        }
    }
}
