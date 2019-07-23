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
        # Get Admin
        $user = $this->Users->get(2);

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
        $user->current_organization_id = $organizationId;

        $this->session(['Auth.User' => $user->toArray()]);
        dd($this->Session->read('Auth.User.id'));
        
        $this->post('/cultivars/add', [
            'label' => 'Test OrganizationBehaviorTest cultivar'
        ]);

        $this->assertResponseSuccess();
        
        # Switch active user organization
        $user->current_organization_id = null;
        $this->session(['Auth.User' => $user]);

        # Attempt to read cultivar, fail
        $this->configRequest([
            'headers' => ['Accept' => 'application/json']
        ]);
        $response = $this->get('/cultivars/index.json');

        $this->assertResponseSuccess();
        dd((string) $this->_response->getBody(['cultivars']));
        $this->assertEquals(0, count(json_decode((string) $this->_response->getBody(['cultivars']))->cultivars));
    }

    public function testBeforeSaveUser()
    {
        # Create cultivar
        # Read cultivar
        # Attempt to read cultivar as another user, fail
    }

    public function testBeforeSaveOnsite()
    {
        putenv('ONSITE=True');
        # Create cultivar
        # Read cultivar
        # Attempt to read cultivar as another user, succeed
    }
}
