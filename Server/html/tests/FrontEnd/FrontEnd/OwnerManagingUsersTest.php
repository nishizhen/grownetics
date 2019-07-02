<?php

namespace Facebook\WebDriver;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\WebDriverBrowserType;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;

require_once('/var/www/html/vendor/autoload.php');

class OwnerManagingUsersTest extends \PHPUnit_Framework_TestCase {

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

    # Should be denied
    public function testGrowerPermissions() {

        # Ensure no cookies are hanging around. This probably shouldn't be needed..
        $this->driver->manage()->deleteAllCookies();

        $this->driver->get( 'http://nginx/devices' );

        # Login as a Grower

        $this->driver->findElement(WebDriverBy::id('email'))
            ->sendKeys('grower@grownetics.co');
        $this->driver->findElement(WebDriverBy::id('password'))
            ->sendKeys('GrowBetter16');
        $this->driver->findElement(
            WebDriverBy::className('btn')
        )->click();

        $this->driver->wait()->until(
            WebDriverExpectedCondition::titleContains('Grownetics - Devices')
        );

        # try to access /users

        $this->driver->get( 'http://nginx/users/' );

        $this->driver->wait()->until(
            WebDriverExpectedCondition::elementTextContains(WebDriverBy::className('alert-danger'), 'not authorized')
        );

    }

    public function testOwnerPermissions() {

        # Ensure no cookies are hanging around. This probably shouldn't be needed..
        $this->driver->manage()->deleteAllCookies();

        $this->driver->get( 'http://nginx/devices' );

        # Login as a Owner

        $this->driver->findElement(WebDriverBy::id('email'))
            ->sendKeys('owner@grownetics.co');
        $this->driver->findElement(WebDriverBy::id('password'))
            ->sendKeys('GrowBetter16');
        $this->driver->findElement(
            WebDriverBy::className('btn')
        )->click();

        $this->driver->wait()->until(
            WebDriverExpectedCondition::titleContains('Grownetics - Devices')
        );

        # access /users

        $this->driver->get( 'http://nginx/users/' );

        $this->driver->wait(10, 500)->until(function ($driver) {
            return $driver->getCurrentURL() === 'http://nginx/users/';
        });

        $this->driver->findElement(
            WebDriverBy::linkText('Edit')
        )->click();

        $this->driver->findElement(WebDriverBy::id('name'))
            ->sendKeys(' edited');

        # Scroll down to the submit button. Lame.
        $this->driver->executeScript("arguments[0].scrollIntoView();", [$this->driver->findElement(
            WebDriverBy::className('submitBtn')
        )]);

        $this->driver->findElement(
            WebDriverBy::className('submitBtn')
        )->click();

        $this->driver->wait()->until(
            WebDriverExpectedCondition::elementTextContains(WebDriverBy::className('alert-success'), 'The user has been saved')
        );

    }
}
