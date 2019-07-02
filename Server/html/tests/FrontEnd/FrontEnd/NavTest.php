<?php
/**
 * Created by PhpStorm.
 * User: mcollins
 * Date: 4/13/18
 * Time: 11:32 AM
 */

namespace Facebook\WebDriver;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;
use Facebook\WebDriver\Exception\NoSuchElementException;

require_once('/var/www/html/vendor/autoload.php');

class NavTest  extends \PHPUnit_Framework_TestCase
{
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

    public function testNavLinks() {
        // get the dashboard, any page should do though
        $this->driver->get( 'http://nginx/dash' );

        // status link should not exist, so expect findElement to throw NoSuchElementException.
        $this->expectException(NoSuchElementException::class);
        $this->driver->findElement(WebDriverBy::xpath("//a[@href='/dash/status']"));

    }
}