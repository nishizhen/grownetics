<?php
use Migrations\AbstractSeed;

/**
 * Rules seed.
 */
class RulesSeed extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeds is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     *
     * @return void
     */
    public function run()
    {
        $data = [
            //CLONE
            [
                'id' => '1',
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'Humidity Alarm Low',
                'status' => '1',
                'autoreset' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],
            [
                'id' => '2',
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'Humidity Alarm High',
                'status' => '1',
                'autoreset' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],
            [
                'id' => '3',
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'Air Temperature Alarm Low',
                'status' => '1',
                'autoreset' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],
            [
                'id' => '4',
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'Air Temperature Alarm High',
                'status' => '1',
                'autoreset' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],
            [
                'id' => '5',
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'Emergency Shutdown Lights',
                'status' => '1',
                'autoreset' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],
            [
                'id' => '6',
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'CO2 Alarm Low',
                'status' => '1',
                'autoreset' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],
            [
                'id' => '7',
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'CO2 Alarm High',
                'status' => '1',
                'autoreset' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],

            //VEGG
            [
                'id' => '8',
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'Humidity Alarm Low',
                'status' => '1',
                'autoreset' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],
            [
                'id' => '9',
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'Humidity Alarm High',
                'status' => '1',
                'autoreset' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],
            [
                'id' => '10',
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'Air Temperature Alarm Low',
                'status' => '1',
                'autoreset' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],
            [
                'id' => '11',
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'Air Temperature Alarm High',
                'status' => '1',
                'autoreset' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],
            [
                'id' => '12',
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'Emergency Shutdown Lights',
                'status' => '1',
                'autoreset' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],
            [
                'id' => '13',
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'CO2 Alarm Low',
                'status' => '1',
                'autoreset' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],
            [
                'id' => '14',
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'CO2 Alarm High',
                'status' => '1',
                'autoreset' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],

            //BLOOM
            [
                'id' => '15',
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'Humidity Alarm Low',
                'status' => '1',
                'autoreset' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],
            [
                'id' => '16',
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'Humidity Alarm High',
                'status' => '1',
                'autoreset' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],
            [
                'id' => '17',
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'Air Temperature Alarm Low',
                'status' => '1',
                'autoreset' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],
            [
                'id' => '18',
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'Air Temperature Alarm High',
                'status' => '1',
                'autoreset' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],
            [
                'id' => '19',
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'Emergency Shutdown Lights',
                'status' => '1',
                'autoreset' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],
            [
                'id' => '20',
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'CO2 Alarm Low',
                'status' => '1',
                'autoreset' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],
            [
                'id' => '21',
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'CO2 Alarm High',
                'status' => '1',
                'autoreset' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],

            //DRY
            [
                'id' => '22',
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'Humidity Alarm Low',
                'status' => '1',
                'autoreset' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],
            [
                'id' => '23',
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'Humidity Alarm High',
                'status' => '1',
                'autoreset' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],
            [
                'id' => '24',
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'Air Temperature Alarm Low',
                'status' => '1',
                'autoreset' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],
            [
                'id' => '25',
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'Air Temperature Alarm High',
                'status' => '1',
                'autoreset' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],
            [
                'id' => '26',
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'Emergency Shutdown Lights',
                'status' => '1',
                'autoreset' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],
            [
                'id' => '27',
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'CO2 Alarm Low',
                'status' => '1',
                'autoreset' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],
            [
                'id' => '28',
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'CO2 Alarm High',
                'status' => '1',
                'autoreset' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],

            //CURE
            [
                'id' => '29',
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'Humidity Alarm Low',
                'status' => '1',
                'autoreset' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],
            [
                'id' => '30',
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'Humidity Alarm High',
                'status' => '1',
                'autoreset' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],
            [
                'id' => '31',
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'Air Temperature Alarm Low',
                'status' => '1',
                'autoreset' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],
            [
                'id' => '32',
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'Air Temperature Alarm High',
                'status' => '1',
                'autoreset' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],
            [
                'id' => '33',
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'Emergency Shutdown Lights',
                'status' => '1',
                'autoreset' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],
            [
                'id' => '34',
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'CO2 Alarm Low',
                'status' => '1',
                'autoreset' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],
            [
                'id' => '35',
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'CO2 Alarm High',
                'status' => '1',
                'autoreset' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],

            //PROCESSING
            [
                'id' => '36',
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'Humidity Alarm Low',
                'status' => '1',
                'autoreset' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],
            [
                'id' => '37',
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'Humidity Alarm High',
                'status' => '1',
                'autoreset' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],
            [
                'id' => '38',
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'Air Temperature Alarm Low',
                'status' => '1',
                'autoreset' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],
            [
                'id' => '39',
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'Air Temperature Alarm High',
                'status' => '1',
                'autoreset' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],
            [
                'id' => '40',
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'Emergency Shutdown Lights',
                'status' => '1',
                'autoreset' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],
            [
                'id' => '41',
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'CO2 Alarm Low',
                'status' => '1',
                'autoreset' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],
            [
                'id' => '42',
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'CO2 Alarm High',
                'status' => '1',
                'autoreset' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],

            //STORAGE
            [
                'id' => '43',
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'Humidity Alarm Low',
                'status' => '1',
                'autoreset' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],
            [
                'id' => '44',
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'Humidity Alarm High',
                'status' => '1',
                'autoreset' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],
            [
                'id' => '45',
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'Air Temperature Alarm Low',
                'status' => '1',
                'autoreset' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],
            [
                'id' => '46',
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'Air Temperature Alarm High',
                'status' => '1',
                'autoreset' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],
            [
                'id' => '47',
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'Emergency Shutdown Lights',
                'status' => '1',
                'autoreset' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],
            [
                'id' => '48',
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'CO2 Alarm Low',
                'status' => '1',
                'autoreset' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],
            [
                'id' => '49',
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'CO2 Alarm High',
                'status' => '1',
                'autoreset' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],

            //SHIPPING
            [
                'id' => '50',
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'Humidity Alarm Low',
                'status' => '1',
                'autoreset' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],
            [
                'id' => '51',
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'Humidity Alarm High',
                'status' => '1',
                'autoreset' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],
            [
                'id' => '52',
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'Air Temperature Alarm Low',
                'status' => '1',
                'autoreset' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],
            [
                'id' => '53',
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'Air Temperature Alarm High',
                'status' => '1',
                'autoreset' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],
            [
                'id' => '54',
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'Emergency Shutdown Lights',
                'status' => '1',
                'autoreset' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],
            [
                'id' => '55',
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'CO2 Alarm Low',
                'status' => '1',
                'autoreset' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],
            [
                'id' => '56',
                'created' => date(DATE_ATOM),
                'modified' => date(DATE_ATOM),
                'label' => 'CO2 Alarm High',
                'status' => '1',
                'autoreset' => NULL,
                'deleted' => '0000-00-00 00:00:00',
                'is_default' => '1',
            ],
        ];

        $table = $this->table('rules');
        $table->insert($data)->save();
    }
}