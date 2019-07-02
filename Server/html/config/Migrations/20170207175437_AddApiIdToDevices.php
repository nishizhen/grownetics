<?php
use Migrations\AbstractMigration;

class AddApiIdToDevices extends AbstractMigration
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
        $table = $this->table('devices');
        $table->addColumn('api_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->update();

        $this->execute('UPDATE devices SET api_id = id');

        //$table->addIndex(array('api_id'), array('unique' => true, 'name' => 'idx_devices_api_id'));
        $table->update();
    }

}
