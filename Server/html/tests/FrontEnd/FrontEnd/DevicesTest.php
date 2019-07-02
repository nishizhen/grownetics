<?php

namespace Facebook\WebDriver;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\WebDriverBrowserType;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;

require_once('/var/www/html/vendor/autoload.php');

class DevicesTest extends \PHPUnit_Framework_TestCase {

    /** @var $driver RemoteWebDriver */
    protected $driver;

    protected $browser;

    protected function setUp() {
        $this->browser = getenv('BROWSER') ? getenv('BROWSER') : 'chrome';
        $this->driver = RemoteWebDriver::create( 'http://hub:4444/wd/hub', array(
            WebDriverCapabilityType::BROWSER_NAME => $this->browser
        ) );
    }

    protected function tearDown() {
        $this->driver->quit();
    }

    public function testDeviceCrud() {

        # Ensure no cookies are hanging around. This probably shouldn't be needed..
        $this->driver->manage()->deleteAllCookies();

        $this->driver->get( 'http://nginx/devices' );

        # Login

        $this->driver->findElement(WebDriverBy::id('email'))
            ->sendKeys('admin@grownetics.co');
        $this->driver->findElement(WebDriverBy::id('password'))
            ->sendKeys('GrowBetter16');
        $this->driver->findElement(
            WebDriverBy::className('btn')
        )->click();

        $this->driver->wait()->until(
            WebDriverExpectedCondition::titleContains('Grownetics - Devices')
        );

        # Add an output

        $this->driver->get( 'http://nginx/devices/add' );

        $this->driver->wait()->until(
            WebDriverExpectedCondition::titleContains('Grownetics - Devices')
        );

        $this->driver->findElement(WebDriverBy::id('label'))
            ->sendKeys('Test add device');

        $this->driver->findElement(
            WebDriverBy::className('submitBtn')
        )->click();

        $this->driver->wait()->until(
            WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
                WebDriverBy::className('paginator')
            )
        );

        $this->driver->findElement(WebDriverBy::linkText("ID"))->click();
        $this->driver->wait()->until(
            WebDriverExpectedCondition::urlContains('direction=asc')
        );
        $this->driver->findElement(WebDriverBy::linkText("ID"))->click();
        $this->driver->wait()->until(
            WebDriverExpectedCondition::urlContains('direction=desc')
        );

        # Click the new Output and edit it.

        $this->driver->findElement(
            WebDriverBy::cssSelector('.edit-btn')
        )->click();

        $this->driver->wait()->until(
            WebDriverExpectedCondition::titleContains('Grownetics - Devices')
        );

        $this->driver->findElement(WebDriverBy::id('label'))
            ->sendKeys(' edited');

        # Scroll down to the submit button. Lame.
        $this->driver->executeScript("arguments[0].scrollIntoView();", [$this->driver->findElement(
            WebDriverBy::className('submitBtn')
        )]);

        $this->driver->findElement(
            WebDriverBy::className('submitBtn')
        )->click();

        $this->driver->findElement(WebDriverBy::linkText("ID"))->click();
        $this->driver->wait()->until(
            WebDriverExpectedCondition::urlContains('direction=asc')
        );
        $this->driver->findElement(WebDriverBy::linkText("ID"))->click();
        $this->driver->wait()->until(
            WebDriverExpectedCondition::urlContains('direction=desc')
        );

        # Ensure the edit worked
        $this->driver->wait()->until(
            WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
                WebDriverBy::linkText('Test add device edited')
            )
        );

        # Delete the Output
        $this->driver->findElement(
            WebDriverBy::cssSelector('.actions .dropdown-toggle')
        )->click();

        $this->driver->wait()->until(
            WebDriverExpectedCondition::presenceOfElementLocated(
                WebDriverBy::linkText('Delete')
            )
        );

        $this->driver->findElement(
            WebDriverBy::linkText('Delete')
        )->click();

        $this->driver->wait()->until(WebDriverExpectedCondition::alertIsPresent());

        $this->driver->switchTo()->alert()->accept();

        $this->driver->wait()->until(
            WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
                WebDriverBy::className('paginator')
            )
        );


        # Ensure the device was deleted
        $this->driver->wait()->until(
            WebDriverExpectedCondition::invisibilityOfElementLocated(
                WebDriverBy::linkText('Test add device edited')
            )
        );
    }
}
