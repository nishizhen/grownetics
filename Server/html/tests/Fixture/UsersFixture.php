<?php
/**
 * Created by PhpStorm.
 * User: nateschreiner
 * Date: 6/14/18
 * Time: 12:07 PM
 */

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;
use Cake\ORM\TableRegistry;


class UsersFixture extends TestFixture
{
    public $import = ['table' => 'users'];

    public $records = [
            [
                'email'       => 'hello@grownetics.co',
                'password'    => '$2a$10$zVl7XRZXIDYexh2AaMkxqume1ddPeWufArdNlmNzYupwraSqpgkdW',
                'role_id'        => 4,
                'name'        => 'Demo User'
            ],
            [
                'email'       => 'admin@grownetics.co',
                'password'    => '$2a$10$zVl7XRZXIDYexh2AaMkxqume1ddPeWufArdNlmNzYupwraSqpgkdW',
                'role_id'        => 1,
                'name'        => 'Admin'
            ],
            [
                'email'       => 'owner@grownetics.co',
                'password'    => '$2a$10$zVl7XRZXIDYexh2AaMkxqume1ddPeWufArdNlmNzYupwraSqpgkdW',
                'role_id'        => 2,
                'name'        => 'Owner'
            ],
            [
                'email'       => 'grower@grownetics.co',
                'password'    => '$2a$10$zVl7XRZXIDYexh2AaMkxqume1ddPeWufArdNlmNzYupwraSqpgkdW',
                'role_id'        => 3,
                'name'        => 'Grower'
            ]
    ];
}