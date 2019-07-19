<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * AclsRolesFixture
 */
class AclsRolesFixture extends TestFixture
{
    public $import = ['table' => 'acls_roles'];

    public $records = [
        # Link Owner to Cultivars allow rule
        [
            'acl_id' => 1,
            'role_id' => 2,
        ]
    ];
}
