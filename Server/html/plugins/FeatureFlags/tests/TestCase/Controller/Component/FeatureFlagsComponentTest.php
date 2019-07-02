<?php
namespace FeatureFlags\Test\TestCase\Controller\Component;

use Cake\Controller\ComponentRegistry;
use Cake\TestSuite\TestCase;
use FeatureFlags\Controller\Component\FeatureFlagsComponent;

/**
 * FeatureFlags\Controller\Component\FeatureFlagsComponent Test Case
 */
class FeatureFlagsComponentTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \FeatureFlags\Controller\Component\FeatureFlagsComponent
     */
    public $FeatureFlags;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $registry = new ComponentRegistry();
        $this->FeatureFlags = new FeatureFlagsComponent($registry);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->FeatureFlags);

        parent::tearDown();
    }

    /**
     * Test initial setup
     *
     * @return void
     */
    public function testInitialization()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
