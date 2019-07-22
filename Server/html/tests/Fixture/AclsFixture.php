<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * AclsFixture
 */
class AclsFixture extends TestFixture
{
    public $import = ['table' => 'acls'];

    public $records = [
        [
            'controller' => 'cultivars',
            'action' => '*',
            'rule' => 'allow'
        ],
    ];
}
