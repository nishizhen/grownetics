<?php

namespace Facebook\WebDriver;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\WebDriverBrowserType;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;

require_once('/var/www/html/vendor/autoload.php');

class GrowerBatchesAclTest extends \PHPUnit\Framework\TestCase {

    /** @var $driver RemoteWebDriver */
    protected $driver;

    protected $browser;

    protected function setUp() {
        $this->browser = 'chrome';
        $this->driver = RemoteWebDriver::create( 'http://hub:4444/wd/hub', array(
            WebDriverCapabilityType::BROWSER_NAME => $this->browser
        ) );
    }

    protected function tearDown() {
        $this->driver->quit();
    }

    public function testBatchesAcl() {

        # Ensure no cookies are hanging around. This probably shouldn't be needed..
        $this->driver->manage()->deleteAllCookies();

        $this->driver->get( 'http://nginx/harvest-batches' );

        # Login
        $this->driver->findElement(WebDriverBy::id('email'))
            ->sendKeys('grower@grownetics.co');
        $this->driver->findElement(WebDriverBy::id('password'))
            ->sendKeys('GrowBetter16');
        $this->driver->findElement(
            WebDriverBy::className('btn')
        )->click();

        $this->driver->wait()->until(
            WebDriverExpectedCondition::titleContains('Grownetics - HarvestBatches')
        );

        $this->driver->wait()->until(
            WebDriverExpectedCondition::presenceOfElementLocated(
                WebDriverBy::linkText('Batches Archive')
            )
        );

        $this->driver->get( 'http://nginx/harvest-batches/archive' );

        $this->driver->wait()->until(
            WebDriverExpectedCondition::titleContains('Grownetics - HarvestBatches')
        );
    }
}
