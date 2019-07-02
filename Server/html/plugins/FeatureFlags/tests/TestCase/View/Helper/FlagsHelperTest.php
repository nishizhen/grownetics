<?php
namespace FeatureFlags\Test\TestCase\View\Helper;

use Cake\TestSuite\TestCase;
use Cake\View\View;
use FeatureFlags\View\Helper\FlagsHelper;

/**
 * FeatureFlags\View\Helper\FlagsHelper Test Case
 */
class FlagsHelperTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \FeatureFlags\View\Helper\FlagsHelper
     */
    public $Flags;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $view = new View();
        $this->Flags = new FlagsHelper($view);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Flags);

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
