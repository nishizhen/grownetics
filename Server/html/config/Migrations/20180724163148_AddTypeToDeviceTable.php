<?php
use Migrations\AbstractMigration;

class AddTypeToDeviceTable extends AbstractMigration
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
        if (!$table->hasColumn('type')) {
            $table->addColumn('type', 'integer', [
                'after' => 'reboot_rate',
                'default' => 0,
                'length' => 11,
                'null' => true,
            ]);
            $table->update();
        }
    }
}
