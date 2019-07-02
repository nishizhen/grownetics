<?php
/**
 * Created by PhpStorm.
 * User: mcollins
 * Date: 4/30/18
 * Time: 6:59 PM
 */

namespace Facebook\WebDriver;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;

require_once('/var/www/html/vendor/autoload.php');

class MapSensorLayersTest extends \PHPUnit_Framework_TestCase
{
//    public $fixtures = [
//        'app.floorplans'
//    ];

    /** @var $driver RemoteWebDriver */
    protected $driver;

    protected $browser;

    protected function setUp() {
        $this->browser = getenv('BROWSER') ? getenv('BROWSER') : 'chrome';
        $this->driver = RemoteWebDriver::create( 'http://hub:4444/wd/hub', [
            WebDriverCapabilityType::BROWSER_NAME => $this->browser
            ]
         );
    }

    protected function tearDown() {
        $this->driver->quit();
    }

    public function testMapSensorLayers() {
        // Ensure no cookies are hanging around. This probably shouldn't be needed..

        $this->driver->manage()->deleteAllCookies();

        $this->driver->get( 'http://nginx/dash' );

        # Login

        $this->driver->findElement(WebDriverBy::id('email'))
            ->sendKeys('admin@grownetics.co');
        $this->driver->findElement(WebDriverBy::id('password'))
            ->sendKeys('GrowBetter16');

        $this->driver->findElement(
            WebDriverBy::id('submit')
        )->click();


        $this->driver->wait()->until(
            WebDriverExpectedCondition::titleContains('Grownetics - Dash')
        );

        $this->driver->wait()->until(
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::className('leaflet-control-layers-toggle'))
        );

        $layerToggle = $this->driver->findElement(WebDriverBy::className('leaflet-control-layers-toggle'));
        $layerToggle->click();

        $hoverAction = $this->driver->action();
        $hoverAction->moveToElement($layerToggle);

        $this->driver->wait()->until(
            WebDriverExpectedCondition::elementToBeClickable(WebDriverBy::xpath('//div[contains(text(), "Temperature High")]'))
        );

        $tempLabel = $this->driver->findElement(WebDriverBy::xpath('//div[contains(text(), "Temperature High")]'));
        $parent = $tempLabel->findElement(WebDriverBy::xpath('../..')); // up two levels
        $tempToggle = $parent->findElement(WebDriverBy::cssSelector('input[type="radio"]'));

        $tempToggle->click();

        $tempLabel = $this->driver->findElement(WebDriverBy::xpath('//div[contains(text(), "Temperature Low")]'));
        $parent = $tempLabel->findElement(WebDriverBy::xpath('../..')); // up two levels
        $tempToggle = $parent->findElement(WebDriverBy::cssSelector('input[type="radio"]'));

        $tempToggle->click();


    }
}