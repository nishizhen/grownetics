<?php
use Migrations\AbstractMigration;

class AddShowMetricToUsers extends AbstractMigration
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
        $table = $this->table('users');
        $table->addColumn('show_metric', 'boolean', [
            'default' => 0,
            'null' => false,
        ]);
        $table->update();
    }
}
