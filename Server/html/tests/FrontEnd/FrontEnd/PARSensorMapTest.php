<?php
/**
 * Created by PhpStorm.
 * User: mcollins
 * Date: 4/30/18
 * Time: 5:15 PM
 */

namespace Facebook\WebDriver;

use Facebook\WebDriver\Interactions\Internal\WebDriverMouseMoveAction;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;

require_once('/var/www/html/vendor/autoload.php');

class PARSensorMapTest extends \PHPUnit\Framework\TestCase
{
//    public $fixtures = [
//        'app.floorplans'
//    ];

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

    public function testPARSensorsOnMap() {
        # Ensure no cookies are hanging around. This probably shouldn't be needed..

        $this->driver->manage()->deleteAllCookies();

        $this->driver->get( 'http://nginx/sensors/add' );

        # Login

        $this->driver->findElement(WebDriverBy::id('email'))
            ->sendKeys('admin@grownetics.co');
        $this->driver->findElement(WebDriverBy::id('password'))
            ->sendKeys('GrowBetter16');

        $this->driver->findElement(
            WebDriverBy::id('submit')
        )->click();

        $this->driver->wait()->until(
            WebDriverExpectedCondition::titleContains('Grownetics - Sensors')
        );

        // select PAR sensor type
        $select = new WebDriverSelect($this->driver->findElement(WebDriverBy::cssSelector('select[name="sensor_type_id"]')));
        $select->selectByValue(11); // sensor type 11 is PAR

        // set pin (because it's required).
        $pinfield = $this->driver->findElement(WebDriverBy::cssSelector('input[name="sensor_pin"]'));
        $pinfield->sendKeys('A0'); // not sure what the actual pin is, but shouldn't matter for testing

        // fill out other fields
        $this->driver->findElement(WebDriverBy::cssSelector('input[name="label"]'))->sendKeys('test PAR sensor');
        (new WebDriverSelect($this->driver->findElement(
            WebDriverBy::cssSelector('select[name="zone_id"]')
        )))->selectByValue(20); //Flower 1
        $this->driver->findElement(WebDriverBy::cssSelector('input[name="calibration"]'))->sendKeys('5');
        (new WebDriverSelect($this->driver->findElement(
            WebDriverBy::cssSelector('select[name="status"]')
        )))->selectByValue(1); // Enabled

        $this->driver->findElement(
            WebDriverBy::cssSelector('button[type="submit"]')
        )->click();

        // should be redirected back to sensors index if successful
        $this->driver->wait()->until(
            WebDriverExpectedCondition::urlIs('http://nginx/sensors')
        );

        $this->driver->get( 'http://nginx/dash' );

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
            WebDriverExpectedCondition::elementToBeClickable(WebDriverBy::xpath('//span[contains(text(), "PAR")]'))
        );

        $PARLabel = $this->driver->findElement(WebDriverBy::xpath('//span[contains(text(), "PAR")]'));
        $parent = $PARLabel->findElement(WebDriverBy::xpath('../..')); // up two levels
        $PARToggle = $parent->findElement(WebDriverBy::cssSelector('input[type="radio"]'));

        $PARToggle->click();

    }
}