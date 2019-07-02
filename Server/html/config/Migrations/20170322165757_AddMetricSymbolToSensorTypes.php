<?php
use Migrations\AbstractMigration;

class AddMetricSymbolToSensorTypes extends AbstractMigration
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
        $table = $this->table('sensor_types');
        $table->addColumn('metric_symbol', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
        ]);
        $table->update();
        $this->execute("UPDATE sensor_types SET metric_symbol='&degC' WHERE label = 'Waterproof Temperature Sensor' OR label = 'Air Temperature Sensor'");
        $this->execute("UPDATE sensor_types SET symbol='pH' WHERE label = 'pH Sensor'");
    }
}
