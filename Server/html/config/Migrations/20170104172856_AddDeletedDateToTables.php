<?php
use Migrations\AbstractMigration;

class AddDeletedDateToTables extends AbstractMigration
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
        $table = $this->table('wikis');
        $table->addColumn('deleted_date', 'datetime', [
            'default' => null,
            'null' => true,
        ]);
        $table->update();
        $table = $this->table('user_contact_methods');
        $table->addColumn('deleted_date', 'datetime', [
            'default' => null,
            'null' => true,
        ]);
        $table->update();
        $table = $this->table('sensor_types');
        $table->addColumn('deleted_date', 'datetime', [
            'default' => null,
            'null' => true
        ]);
        $table->update();
        $table = $this->table('harvest_batch_logs');
        $table->addColumn('deleted_date', 'datetime', [
            'default' => null,
            'null' => true,
        ]);
        $table->update();
        $table = $this->table('chats');
        $table->addColumn('deleted_date', 'datetime', [
            'default' => null,
            'null' => true,
        ]);
        $table->update();
        $table = $this->table('acls_roles');
        $table->addColumn('deleted_date', 'datetime', [
            'default' => null,
            'null' => true,
        ]);
        $table->update();
    }
}
