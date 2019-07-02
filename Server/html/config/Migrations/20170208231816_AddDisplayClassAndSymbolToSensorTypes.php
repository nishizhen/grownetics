<?php
use Migrations\AbstractMigration;

class AddDisplayClassAndSymbolToSensorTypes extends AbstractMigration
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
        $table->addColumn('symbol', 'string', [
            'default' => null,
            'null' => false,
            'limit' => 255,
        ]);
        $table->addColumn('display_class', 'string', [
            'default' => null,
            'null' => false,
            'limit' => 255,
        ]);
        $table->update();
    }
}
