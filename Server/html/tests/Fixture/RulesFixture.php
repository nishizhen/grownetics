<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class RulesFixture extends TestFixture
{
   public $import = ['table' => 'rules'];

   public $records = [
            [
                'id' => '1',
                'label' => 'Humidity Alarm Low',
                'status' => '1',
                'autoreset' => 1,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],
            [
                'id' => '2',
                'label' => 'Humidity Alarm High',
                'status' => '1',
                'autoreset' => 1,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],
            [
                'id' => '3',
                'label' => 'Air Temperature Alarm Low',
                'status' => '1',
                'autoreset' => 1,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],
            [
                'id' => '4',
                'label' => 'Air Temperature Alarm High',
                'status' => '1',
                'autoreset' => 1,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],
            [
                'id' => '5',
                'label' => 'Emergency Shutdown Lights',
                'status' => '1',
                'autoreset' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],
            [
                'id' => '6',
                'label' => 'CO2 Alarm Low',
                'status' => '1',
                'autoreset' => 1,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],
            [
                'id' => '7',
                'label' => 'CO2 Alarm High',
                'status' => '1',
                'autoreset' => 1,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],
        ];
}
