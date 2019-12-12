<?php

namespace App\Test\TestCase\Model\Behavior;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;

/**
 * App\Model\Behavior\OrganizationBehavior Test Case
 */
class OrganizationBehaviorTest extends IntegrationTestCase
{
    public $fixtures = [
        'app.organizations',
        'app.cultivars',
        'app.users',
        'app.users_roles',
        'app.roles',
        'app.notifications',
        'app.acls',
        'app.acls_roles',
    ];

    /**
     * Test subject
     *
     * @var \App\Model\Behavior\OrganizationBehavior
     */
    public $Organization;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->Users = TableRegistry::get("Users");
        $this->Cultivars = TableRegistry::get("Cultivars");
        $this->Organizations = TableRegistry::get("Organizations");
        $this->UsersRoles = TableRegistry::get("UsersRoles");
        $this->Roles = TableRegistry::get("Roles");
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Organization);

        parent::tearDown();
    }

    public function testBeforeSaveOrganization()
    {
        $this->markTestIncomplete('Not implemented yet.');


        # Get User
        $user = $this->Users->get(1);

        $role = $this->UsersRoles->newEntity([
            'user_id' => $user->id,
            'role_id' => $this->Roles->findByLabel('Owner')->first()->id
        ]);
        $this->UsersRoles->save($role);

        # Get Organization
        $organizationId = $this->Organizations->get(1)->id;

        # Create Membership
        $role = $this->UsersRoles->newEntity([
            'organization_id' => $organizationId,
            'user_id' => $user->id,
            'role_id' => $this->Roles->findByLabel('Organization Member')->first()->id
        ]);
        $this->UsersRoles->save($role);

        $this->session(['Auth.User' => $user]);
        
        $this->post('/cultivars/add', [
            'label' => 'Test OrganizationBehaviorTest cultivar'
        ]);

        $this->assertResponseOk();
        
        # Read cultivar
        $cultivars = $this->Cultivars->find('all');
dd($cultivars->toArray());

        # Switch active user organization
        # TODO: 

        # Attempt to read cultivar, fail
        $cultivars = $this->Cultivars->find('all');
        $this->assertEquals(0, $cultivars->count());
    }

    public function testBeforeSaveUser()
    {
        $this->markTestIncomplete('Not implemented yet.');
        # Create cultivar
        # Read cultivar
        # Attempt to read cultivar as another user, fail
    }

    public function testBeforeSaveOnsite()
    {
        $this->markTestIncomplete('Not implemented yet.');
        putenv('ONSITE=True');
        # Create cultivar
        # Read cultivar
        # Attempt to read cultivar as another user, succeed
    }
}
