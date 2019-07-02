<?php
use Migrations\AbstractMigration;

class AddSensorDataType extends AbstractMigration
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
        $table = $this->table('sensors')
            ->addColumn('data_type', 'integer', [
                'default' => null,
                'length' => 2,
                'null' => true
            ]);
        $table->update();

        $this->execute("UPDATE sensors SET data_type=1 WHERE sensor_type_id = 1");
        $this->execute("UPDATE sensors SET data_type=2 WHERE sensor_type_id = 2");
        $this->execute("UPDATE sensors SET data_type=1 WHERE sensor_type_id = 3");
        $this->execute("UPDATE sensors SET data_type=3 WHERE sensor_type_id = 4");
        $this->execute("UPDATE sensors SET data_type=4 WHERE sensor_type_id = 5");
        $this->execute("UPDATE sensors SET data_type=5 WHERE sensor_type_id = 6");
        $this->execute("UPDATE sensors SET data_type=6 WHERE sensor_type_id = 7");
        $this->execute("UPDATE sensors SET data_type=7 WHERE sensor_type_id = 8");
        $this->execute("UPDATE sensors SET data_type=8 WHERE sensor_type_id = 9");
        $this->execute("UPDATE sensors SET data_type=9 WHERE sensor_type_id = 10");
        $this->execute("UPDATE sensors SET data_type=10 WHERE sensor_type_id = 11");
        $this->execute("UPDATE sensors SET data_type=1 WHERE sensor_type_id = 12");
        $this->execute("UPDATE sensors SET data_type=11 WHERE sensor_type_id = 13");

        $table = $this->table('rule_conditions');
        $table->renameColumn('data_type', 'sensor_type');
        $table->update();
    }
}

