<?php

namespace Facebook\WebDriver;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\WebDriverBrowserType;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;

require_once('/var/www/html/vendor/autoload.php');

class RulesCrudTest extends \PHPUnit_Framework_TestCase {

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

    public function testOutputCrud() {
        # Ensure no cookies are hanging around. This probably shouldn't be needed..

        $this->driver->manage()->deleteAllCookies();

        $this->driver->get( 'http://nginx/rules' );

        # Login

        $this->driver->findElement(WebDriverBy::id('email'))
            ->sendKeys('admin@grownetics.co');
        $this->driver->findElement(WebDriverBy::id('password'))
            ->sendKeys('GrowBetter16');
        $this->driver->findElement(
            WebDriverBy::className('btn')
        )->click();

        $this->driver->wait()->until(
            WebDriverExpectedCondition::titleContains('Grownetics - Rules')
        );

        # Add an output

        $this->driver->get( 'http://nginx/rules/add' );

        $this->driver->wait()->until(
            WebDriverExpectedCondition::titleContains('Grownetics - Rules')
        );

        $this->driver->findElement(WebDriverBy::id('label'))
            ->sendKeys('Test add rule');

        $this->driver->findElement(
            WebDriverBy::className('submitBtn')
        )->click();

        $this->driver->wait()->until(
            WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
                WebDriverBy::className('paginator')
            )
        );

        # Click the new Output and edit it.

        $this->driver->executeScript("arguments[0].scrollIntoView();", [$this->driver->findElement(
            WebDriverBy::linkText('Test add rule')
        )]);

        $this->driver->findElement(
            WebDriverBy::linkText('Test add rule')
        )->click();

        $this->driver->wait()->until(
            WebDriverExpectedCondition::titleContains('Grownetics - Rules')
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

        # Ensure the edit worked
        $this->driver->wait()->until(
            WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
                WebDriverBy::linkText('Test add rule edited')
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


        # Ensure the output was deleted
        $this->driver->wait()->until(
            WebDriverExpectedCondition::invisibilityOfElementLocated(
                WebDriverBy::linkText('Test add rule edited')
            )
        );
    }
}
