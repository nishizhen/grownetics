<?php

namespace Facebook\WebDriver;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\WebDriverBrowserType;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;

require_once('/var/www/html/vendor/autoload.php');

class LoginCrudTest extends \PHPUnit\Framework\TestCase {

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

    public function testLoginFlows() {
        # Ensure no cookies are hanging around. This probably shouldn't be needed..
        $this->driver->manage()->deleteAllCookies();

        # Bad login
        $this->driver->get( 'http://nginx/devices' );
        self::assertEquals( 'http://nginx/users/login?redirect=%2Fdevices', $this->driver->getCurrentUrl() );
        self::assertEquals( 'Grownetics - Users', $this->driver->getTitle() );

        $this->driver->findElement(WebDriverBy::id('email'))
            ->sendKeys('notauser');

        $this->driver->findElement(
            WebDriverBy::className('btn')
        )->click();

        $this->driver->wait()->until(
            WebDriverExpectedCondition::titleContains('Grownetics - Users')
        );

        # Good login
        $this->driver->get( 'http://nginx/devices' );
        self::assertEquals( 'http://nginx/users/login?redirect=%2Fdevices', $this->driver->getCurrentUrl() );

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

        $this->driver->get( 'http://nginx/sensors/index' );

        $this->driver->wait()->until(
            WebDriverExpectedCondition::titleContains('Grownetics - Sensors')
        );

        $this->driver->wait(10)->until(
            WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
                WebDriverBy::className('paginator')
            )
        );

        # Once logged out, should stay logged out.
        $this->driver->findElement(
            WebDriverBy::className('logout')
        )->click();

        $this->driver->wait()->until(
            WebDriverExpectedCondition::titleContains('Grownetics - Users')
        );

        $this->driver->get( 'http://nginx/sensors/index' );

        $this->driver->wait()->until(
            WebDriverExpectedCondition::titleContains('Grownetics - Users')
        );
    }
}
