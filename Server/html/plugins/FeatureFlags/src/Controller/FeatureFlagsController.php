<?php
namespace FeatureFlags\Controller;

use FeatureFlags\Controller\AppController;

/**
 * FeatureFlags Controller
 *
 *
 * @method \FeatureFlags\Model\Entity\FeatureFlag[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class FeatureFlagsController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
    }

    # Why separate functions instead of one toggle() function? To prevent race conditions. If two users hit toggle
    # around the same time, then unexpected behavior could occur to the user.
    public function enable()
    {
        $this->request->allowMethod(['post', 'delete']);
        $flagName = $this->request->getQuery()['data']['flagName'];

        $sf = new \SensioLabs\Consul\ServiceFactory(['base_uri' => 'http://consul:8500']);
        $kv = $sf->get('kv');
        $kv->put('feature_flags/'.$flagName, true);

        return $this->redirect($this->referer());
    }

    public function disable()
    {
        $this->request->allowMethod(['post', 'delete']);
        $flagName = $this->request->getQuery()['data']['flagName'];

        $sf = new \SensioLabs\Consul\ServiceFactory(['base_uri' => 'http://consul:8500']);
        $kv = $sf->get('kv');
        $kv->put('feature_flags/'.$flagName, false);

        return $this->redirect($this->referer());
    }
}
