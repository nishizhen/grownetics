<?php

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\OrganizationsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\OrganizationsTable Test Case
 */
class OrganizationsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\OrganizationsTable
     */
    public $Organizations;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.organizations',
        'app.users',
        'app.users_roles',
        'app.roles',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Organizations') ? [] : ['className' => OrganizationsTable::class];
        $this->Organizations = TableRegistry::getTableLocator()->get('Organizations', $config);
        $this->Users = TableRegistry::get("Users");
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
        unset($this->Organizations);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test afterSave method
     *
     * @return void
     */
    public function testAfterSave()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test addUserByEmail method
     *
     * @return void
     */
    public function testAddUserByEmail()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test acceptInvite method
     *
     * @return void
     */
    public function testTryToAcceptBadInvite()
    {
        # Get User
        $userId = $this->Users->get(1)->id;

        # Get Organization
        $organizationId = $this->Organizations->get(1)->id;

        # Create Invite (Handbook/Development/organizations.md)
        $invite = $this->UsersRoles->newEntity([
            'organization_id' => $organizationId,
            'user_id' => 2,
            'role_id' => $this->Roles->findByLabel('Organization Invitee')->first()->id
        ]);
        $this->UsersRoles->save($invite);

        $this->assertFalse($this->Organizations->acceptInvite(1, 1));
    }

    public function testAcceptInvite()
    {
        # Get User
        $userId = $this->Users->get(1)->id;

        # Get Organization
        $organizationId = $this->Organizations->get(1)->id;

        # Create Invite (Handbook/Development/organizations.md)
        $invite = $this->UsersRoles->newEntity([
            'organization_id' => $organizationId,
            'user_id' => $userId,
            'role_id' => $this->Roles->findByLabel('Organization Invitee')->first()->id
        ]);
        $this->UsersRoles->save($invite);

        $this->assertNotFalse($this->Organizations->acceptInvite($organizationId, $userId));

        $invite = $this->UsersRoles->get($invite->id);

        $this->assertEquals($invite->role_id, $this->Roles->findByLabel('Organization Member')->first()->id);
    }

    public function testUpgradeMemberToAdmin()
    {
        # Get User
        $userId = $this->Users->get(1)->id;

        # Get Organization
        $organizationId = $this->Organizations->get(1)->id;

        $orgAdminRoleId = $this->Roles->findByLabel('Organization Admin')->first()->id;

        # Create Membership
        $role = $this->UsersRoles->newEntity([
            'organization_id' => $organizationId,
            'user_id' => $userId,
            'role_id' => $this->Roles->findByLabel('Organization Member')->first()->id
        ]);
        $this->UsersRoles->save($role);

        $this->assertNotFalse($this->Organizations->setUserRole($organizationId, $userId, $orgAdminRoleId));

        $role = $this->UsersRoles->get($role->id);

        $this->assertEquals($role->role_id, $orgAdminRoleId);
    }

    public function testDowngradeAdminToMember()
    {
        # Get User
        $userId = $this->Users->get(1)->id;

        # Create second user for admin
        $secondAdmin = $this->Users->newEntity([
            'username' => 'admin',
            'email' => 'test@test.com',
            'name' => 'Test',
            'skipRegister' => True
        ]);
        $this->Users->save($secondAdmin);

        # Get Organization
        $organizationId = $this->Organizations->get(1)->id;

        $orgMemberRoleId = $this->Roles->findByLabel('Organization Member')->first()->id;
        $orgAdminRoleId = $this->Roles->findByLabel('Organization Admin')->first()->id;

        # Create Membership
        $role = $this->UsersRoles->newEntity([
            'organization_id' => $organizationId,
            'user_id' => $userId,
            'role_id' => $orgAdminRoleId
        ]);
        $this->UsersRoles->save($role);

        $this->assertNotFalse($this->Organizations->setUserRole($organizationId, $userId, $orgAdminRoleId));

        $role = $this->UsersRoles->get($role->id);

        $this->assertEquals($role->role_id, $orgAdminRoleId);
    }

    public function testAttemptDowngradeLastAdminToMember()
    {
        # Get User
        $userId = $this->Users->get(1)->id;

        # Get Organization
        $organizationId = $this->Organizations->get(1)->id;

        $orgAdminRoleId = $this->Roles->findByLabel('Organization Member')->first()->id;

        # Create Membership
        $role = $this->UsersRoles->newEntity([
            'organization_id' => $organizationId,
            'user_id' => $userId,
            'role_id' => $this->Roles->findByLabel('Organization Admin')->first()->id
        ]);
        $this->UsersRoles->save($role);

        $this->expectException(\Exception::class);

        $this->Organizations->setUserRole($organizationId, $userId, $orgAdminRoleId);
        
        $role = $this->UsersRoles->get($role->id);

        $this->assertEquals($role->role_id, $orgAdminRoleId);
    }
}
