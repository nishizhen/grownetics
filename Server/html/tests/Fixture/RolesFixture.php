<?php
/**
 * Created by PhpStorm.
 * User: nateschreiner
 * Date: 6/14/18
 * Time: 12:07 PM
 */

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class RolesFixture extends TestFixture
{
    public $import = ['table' => 'roles'];

    public $records = [
        [
            'label' => 'Admin',
        ],
        [
            'label' => 'Owner',
        ],
        [
            'label' => 'Grower',
        ],
        [
            'label' => 'User',
        ],
        [
            'label' => 'Invitee',
        ],
        [
            'label' => 'Guest',
        ]
    ];
}