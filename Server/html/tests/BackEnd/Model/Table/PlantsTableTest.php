<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PlantsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PlantsTable Test Case
 */
class PlantsTableTest extends TestCase
{

    public $fixtures = [];
    /**
     * Test subject
     *
     * @var \App\Model\Table\PlantsTable
     */
    public $PlantsTable;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->PlantsTable = TableRegistry::get('Plants');

        # TODO: Create zones (to test loading into a zone without a specific bench)

        # TODO: Create benches inside those zone

        # TODO: Create plant placeholders on those benches

        # TODO: Create some plants
    }

    public function testMoveToZone()
    {
        $this->markTestIncomplete('Not implemented yet.');
        $this->assertEquals(true,false);
    }

    public function testMoveToBench()
    {
        $this->markTestIncomplete('Not implemented yet.');
        $this->assertEquals(true,false);
    }

    public function testMoveToFullZone()
    {
        $this->markTestIncomplete('Not implemented yet.');
        $this->assertEquals(true,false);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->PlantsTable);

        parent::tearDown();
    }

}
