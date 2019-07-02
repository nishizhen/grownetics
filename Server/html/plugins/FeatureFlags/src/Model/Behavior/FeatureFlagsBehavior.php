<?php
namespace FeatureFlags\Model\Behavior;

use Cake\ORM\Behavior;
use Cake\ORM\Table;

/**
 * FeatureFlags behavior
 */
class FeatureFlagsBehavior extends Behavior
{

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    public function getFeatureFlagValue($flagName) {
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
}
