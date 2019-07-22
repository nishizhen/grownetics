<?php

namespace Facebook\WebDriver;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\WebDriverBrowserType;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;

use Cake\ORM\TableRegistry;
use Cake\I18n\Time;

require_once('/var/www/html/vendor/autoload.php');

class CalendarTest extends \PHPUnit\Framework\TestCase {
    protected $driver;
    protected $browser;

    public $fixtures = array(
        'app.users'
    );

    protected function setUp() {
        $this->browser = getenv('BROWSER') ? getenv('BROWSER') : 'chrome';
        $this->driver = RemoteWebDriver::create('http://hub:4444/wd/hub', [
            WebDriverCapabilityType::BROWSER_NAME => $this->browser
            ]
        );
        $this->date = Time::now()->format('Y-m-d');

        $Tasks = TableRegistry::get('Tasks');
        $Users = TableRegistry::get('Users');
        $task = $Tasks->newEntity([
            'label' => 'Task test',
            'status' => $Tasks->enumValuetoKey('status', 'Incomplete'),
            'harvestbatch_id' => 0,
            'due_date' => $this->date,
            'assignee' => $Users->find('all')->first()->id,
            'type' => $Tasks->enumValuetoKey('type', 'Generic')
        ]);
        $Tasks->save($task);
    }

    protected function tearDown() {
        $this->driver->quit();
    }

    public function testCalendar() {
        
        # Ensure no cookies are hanging around. This probably shouldn't be needed..
        $this->driver->manage()->deleteAllCookies();
        $this->driver->get('http://nginx/');

        # Login
        $this->driver->findElement(WebDriverBy::id('email'))
            ->sendKeys('admin@grownetics.co');
        $this->driver->findElement(WebDriverBy::id('password'))
            ->sendKeys('GrowBetter16');
        $this->driver->findElement(
            WebDriverBy::className('btn')
        )->click();

        $this->driver->executeScript("arguments[0].scrollIntoView();", [$this->driver->findElement(
            WebDriverBy::xpath('//div[contains(@id,"'.$this->date.'")]')
        )]);

        $this->driver->wait()->until(
            WebDriverExpectedCondition::elementToBeClickable(WebDriverBy::xpath('//div[contains(@id,"'.$this->date.'")]'))
        );
    }
}
