<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * PlantZoneTypesFixture
 *
 */
class PlantZoneTypesFixture extends TestFixture
{
    public $import = ['table' => 'plant_zone_types'];

    public $records = [
            [
                'id' => '1',
                'label' => 'Clone',
            ],
            [
                'id' => '2',
                'label' => 'Veg',
            ],
            [
                'id' => '3',
                'label' => 'Bloom',
            ],
            [
                'id' => '4',
                'label' => 'Dry',
            ],
            [
                'id' => '5',
                'label' => 'Cure',
            ],
            [
                'id' => '6',
                'label' => 'Processing',
            ],
            [
                'id' => '7',
                'label' => 'Storage',
            ],
            [
                'id' => '8',
                'label' => 'Shipping',
            ],
        ];
}