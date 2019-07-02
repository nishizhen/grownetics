<?php

namespace Facebook\WebDriver;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\WebDriverBrowserType;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;
use Facebook\WebDriver\WebDriverSelect;

use Cake\ORM\TableRegistry;

require_once('/var/www/html/vendor/autoload.php');

class BatchWorkflowTest extends \PHPUnit_Framework_TestCase {

    /** @var $driver RemoteWebDriver */
    public $driver;
    public $browser;

    public function setUp() {
        $this->browser = 'chrome';
        $this->driver = RemoteWebDriver::create('http://hub:4444/wd/hub', [
                WebDriverCapabilityType::BROWSER_NAME => $this->browser
            ]
        );

        $Zones = TableRegistry::get('Zones');
        $Recipes = TableRegistry::get('Recipes');
        $RecipeEntries = TableRegistry::get('RecipeEntries');
        $Cultivars = TableRegistry::get('Cultivars');

        $cultivar = $Cultivars->newEntity([
            'label' => 'Guap Test',
            'batch_count' => 1
        ]);
        $Cultivars->save($cultivar);
        $this->cultivar_id = $cultivar->id;

        $clone_zone = $Zones->newEntity([
            'label' => 'Clone 1 test',
            'plant_zone_type_id' => $Zones->enumValueToKey('plant_zone_types', 'Clone'),
            'dontMap' => true,
            'zone_type_id' => $Zones->enumValueToKey('zone_types', 'Room')
        ]);
        $Zones->save($clone_zone);
        $this->clone_zone_id = $clone_zone->id;

        $veg_zone = $Zones->newEntity([
            'label' => 'Veg 1 Test',
            'plant_zone_type_id' => $Zones->enumValueToKey('plant_zone_types', 'Veg'),
            'dontMap' => true,
            'zone_type_id' => $Zones->enumValueToKey('zone_types', 'Room')
        ]);
        $Zones->save($veg_zone);
        $this->veg_zone_id = $veg_zone->id;

        $flower_zone = $Zones->newEntity([
            'label' => 'Flower 1 Test',
            'plant_zone_type_id' => $Zones->enumValueToKey('plant_zone_types', 'Bloom'),
            'dontMap' => true,
            'zone_type_id' => $Zones->enumValueToKey('zone_types', 'Room')
        ]);
        $Zones->save($flower_zone);
        $this->flower_zone_id = $flower_zone->id;

        $dry_zone = $Zones->newEntity([
            'label' => 'Dry 1 test',
            'plant_zone_type_id' => $Zones->enumValueToKey('plant_zone_types', 'Dry'),
            'dontMap' => true,
            'zone_type_id' => $Zones->enumValueToKey('zone_types', 'Room')
        ]);
        $Zones->save($dry_zone);
        $this->dry_zone_id = $dry_zone->id;

        $recipe = $Recipes->newEntity([
            'label' => 'Guap 70 Day Test'
        ]);
        $Recipes->save($recipe);
        $this->recipe_id = $recipe->id;
 
        $recipe_entries = [
            [
                'recipe_id' => $this->recipe_id,
                'days' => 7,
                'plant_zone_type_id' => 2
            ],
            [
                'recipe_id' => $this->recipe_id,
                'days' => 70,
                'plant_zone_type_id' => 3
            ]
        ];
        $entities = $RecipeEntries->newEntities($recipe_entries);
        $RecipeEntries->saveMany($entities);
    }

    public function tearDown() {
        $this->driver->quit();
    }

    public function testBatchWorkflow() {
        # Ensure no cookies are hanging around. This probably shouldn't be needed..
        $this->driver->manage()->deleteAllCookies();

        $this->driver->get( 'http://nginx/harvest-batches/add' );

        # Login
        $this->driver->findElement(WebDriverBy::id('email'))
            ->sendKeys('admin@grownetics.co');
        $this->driver->findElement(WebDriverBy::id('password'))
            ->sendKeys('GrowBetter16');
        $this->driver->findElement(
            WebDriverBy::className('btn')
        )->click();

        $this->driver->wait()->until(
            WebDriverExpectedCondition::titleContains('Grownetics - HarvestBatches')
        );

        # Select Cultivar
        $selectCultivar = new WebDriverSelect($this->driver->findElement(WebDriverBy::name('cultivar_id')));
        $selectCultivar->selectByValue($this->cultivar_id);

        # Add plant date
        $this->driver->executeScript("$('#harvestBatchForm > fieldset > div:nth-child(2) > div > input').val('2018-05-20')");

        # Select Recipe
        $selectRecipe = new WebDriverSelect($this->driver->findElement(WebDriverBy::name('recipe_id')));
        $selectRecipe->selectByValue($this->recipe_id);

        # Wait for recipe to load
        sleep(5);
        # Select Veg/Flower Zone
        /* $this->driver->executeScript("$('.roomDDown').each(function(ind, val) {
	            if (ind == 0) {
		            $(val).dropdown('set selected','".$this->veg_zone_id."');
	            } else if (ind == 1) {
	                $(val).dropdown('set selected', '".$this->flower_zone_id."'); 
	            } else if (ind == 2) {
	                $(val).dropdown('set selected','". $this->flower_zone_id."');
	            } else {
	                $(val).dropdown('set selected', '".$this->dry_zone_id."');
	            }
        });"); */

        $this->driver->executeScript("$('.roomDDown').each(function(ind, val) {
	            if (ind == 0) {
		            $(val).dropdown('set selected','".$this->clone_zone_id."');
	            } else if (ind == 1) {
	                $(val).dropdown('set selected', '".$this->veg_zone_id."'); 
	            } else if (ind == 2) {
	                $(val).dropdown('set selected','". $this->flower_zone_id."');
	            } else {
	                $(val).dropdown('set selected', '".$this->dry_zone_id."');
	            }
        });");

        # Scroll down to the submit button
        $this->driver->executeScript("arguments[0].scrollIntoView();", [$this->driver->findElement(
            WebDriverBy::className('submitBtn')
        )]);

        # Add start plant id
        $this->driver->executeScript("$('#start-id').val('1A400021266F0EE000000200');");

        # Add end plant id
        $this->driver->executeScript("$('#end-id').val('1A400021266F0EE000000209');");

        # Add plant list
        $this->driver->findElement(WebDriverBy::id('plant_list'))
            ->sendKeys('1A400021266F0EE000000210, 1A400021266F0EE000000211, 1A400021266F0EE000000212');

        $this->driver->findElement(
            WebDriverBy::className('submitBtn')
        )->click();

        $this->driver->wait()->until(WebDriverExpectedCondition::urlContains('/view/'));

        $this->driver->findElement(
            WebDriverBy::className('completeTaskBtn')
        )->click();

        $this->driver->wait()->until(WebDriverExpectedCondition::alertIsPresent());

        $this->driver->switchTo()->alert()->accept();

        # Allow batch to move, once optimized the timeout can be lowered
        sleep(30);

        $this->driver->wait()->until(
            WebDriverExpectedCondition::presenceOfElementLocated(
                WebDriverBy::className('alert-success')
            )
        );

    }
}
