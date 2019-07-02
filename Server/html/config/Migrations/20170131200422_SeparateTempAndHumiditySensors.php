<?php
use Migrations\AbstractMigration;

class SeparateTempAndHumiditySensors extends AbstractMigration
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
        $table = $this->table('sensors');
        $rows = $this->fetchAll('SELECT * FROM sensors where sensor_type_id = 2');

        foreach($rows as $row) {
            unset($row->id);
            unset($row->created);
            unset($row->modified);
            $row->sensor_type_id = 3;
            $table->insert($row);
        }

    }
}
