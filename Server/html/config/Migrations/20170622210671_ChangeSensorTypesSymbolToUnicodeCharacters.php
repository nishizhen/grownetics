<?php
use Migrations\AbstractMigration;

class ChangeSensorTypesSymbolToUnicodeCharacters extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $this->execute("UPDATE sensor_types SET symbol='&#8457;' WHERE label = 'Waterproof Temperature Sensor'");
        $this->execute("UPDATE sensor_types SET symbol='&#8457;' WHERE label = 'Air Temperature Sensor'");
        $this->execute("UPDATE sensor_types SET metric_symbol='&#8451;' WHERE label = 'Waterproof Temperature Sensor'");
        $this->execute("UPDATE sensor_types SET metric_symbol='&#8451;' WHERE label = 'Air Temperature Sensor'");
        $this->execute("UPDATE sensor_types SET symbol='&#956;' WHERE label = 'EC Sensor'");
        $this->execute("UPDATE sensor_types SET symbol='&#37;' WHERE label = 'Humidity Sensor'");
    }
}
