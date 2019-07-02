<?php
/**
 * Created by PhpStorm.
 * User: nateschreiner
 * Date: 6/20/18
 * Time: 8:57 AM
 */

namespace Facebook\WebDriver;

use Facebook\WebDriver\Remote\WebDriverCapabilityType;
use Facebook\WebDriver\Remote\RemoteWebDriver;

class TaskAndRecipeCrudTest extends \PHPUnit_Framework_TestCase
{
    public $driver;
    public $browser;

    public function setUp() {
        $this->browser = 'chrome';
        $this->driver = RemoteWebDriver::create('http://hub:4444/wd/hub', [
                WebDriverCapabilityType::BROWSER_NAME => $this->browser
            ]
        );
    }

    public function testCrud() {
        # Ensure no cookies are hanging around. This probably shouldn't be needed..
        $this->driver->manage()->deleteAllCookies();

        $this->driver->get( 'http://nginx/recipes/add' );

        # Login
        $this->driver->findElement(WebDriverBy::id('email'))
            ->sendKeys('admin@grownetics.co');
        $this->driver->findElement(WebDriverBy::id('password'))
            ->sendKeys('GrowBetter16');
        $this->driver->findElement(
            WebDriverBy::className('btn')
        )->click();

        $this->driver->wait()->until(
            WebDriverExpectedCondition::titleContains('Grownetics - Recipes')
        );

        # Input label
        $this->driver->findElement(WebDriverBy::name('label'))->sendKeys('Task / Recipe Creation Test');


        $this->driver->findElement(
            WebDriverBy::className('submitBtn')
        )->click();

        $this->driver->wait()->until(WebDriverExpectedCondition::urlContains('/view/'));

        // Add 1st Zone
        $this->driver->findElement(WebDriverBy::id('days_input'))->sendKeys('5');
        $this->driver->executeScript("
            $('select[name=\"plant_zone_type_id\"]>option:eq(1)').attr('selected', true);
         ");
        $this->driver->findElement(WebDriverBy::className('btn-success'))->click();

        sleep(5);

        // Add Task to first zone
        $this->driver->executeScript("
            $('#1').prev('td').children().click();
        ");
        sleep(5);
        $this->driver->findElement(WebDriverBy::id('Clone'))->sendKeys('1');
        $this->driver->findElement(WebDriverBy::id('add-Clone-task'))->click();
        sleep(5);

        // Add 2nd Zone
        $this->driver->findElement(WebDriverBy::id('days_input'))->sendKeys('10');
        $this->driver->executeScript("
            $('select[name=\"plant_zone_type_id\"]>option:eq(2)').attr('selected', true);
         ");
        $this->driver->findElement(WebDriverBy::className('btn-success'))->click();

        sleep(5);

        // Add Task to second Zone
        $this->driver->executeScript("
            $('#2').prev('td').children().click();
        ");
        sleep(5);
        $this->driver->findElement(WebDriverBy::id('Veg'))->sendKeys('1');
        $taskSelection = new WebDriverSelect($this->driver->findElement(WebDriverBy::id('tasks')));
        $taskSelection->selectByValue('2');
        /* $this->driver->executeScript("
            $('select[id=\"tasks\"]>option:eq(2)').attr('selected',true);
        "); */
        $this->driver->findElement(WebDriverBy::id('label'))->sendKeys('Monitor');
        $this->driver->findElement(WebDriverBy::id('add-Veg-task'))->click();
        sleep(5);

        $this->driver->wait()->until(
            WebDriverExpectedCondition::presenceOfElementLocated(
                WebDriverBy::className('alert-success')
            )
        );
    }
}
