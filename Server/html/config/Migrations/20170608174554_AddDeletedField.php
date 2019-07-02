<?php
use Migrations\AbstractMigration;

class AddDeletedField extends AbstractMigration
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
        $table = $this->table('appliances');
        $table->addColumn('deleted', 'integer', [
            'default' => null,
            'limit' => 4,
            'null' => true,
        ]);
        $table->update();

        $table = $this->table('appliance_templates');
        $table->addColumn('deleted', 'integer', [
            'default' => null,
            'limit' => 4,
            'null' => true,
        ]);
        $table->update();

        $table = $this->table('sensors_zones');
        $table->addColumn('deleted', 'integer', [
            'default' => null,
            'limit' => 4,
            'null' => true,
        ]);
        $table->update();
    }
}