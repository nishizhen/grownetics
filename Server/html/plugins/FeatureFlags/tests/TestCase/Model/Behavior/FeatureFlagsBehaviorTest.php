<?php
namespace FeatureFlags\Test\TestCase\Model\Behavior;

use Cake\TestSuite\TestCase;
use FeatureFlags\Model\Behavior\FeatureFlagsBehavior;

/**
 * FeatureFlags\Model\Behavior\FeatureFlagsBehavior Test Case
 */
class FeatureFlagsBehaviorTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \FeatureFlags\Model\Behavior\FeatureFlagsBehavior
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
        $this->FeatureFlags = new FeatureFlagsBehavior();
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
