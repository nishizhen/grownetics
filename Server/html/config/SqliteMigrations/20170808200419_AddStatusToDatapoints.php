<?php
use Migrations\AbstractMigration;

class AddStatusToDataPoints extends AbstractMigration
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
        $table = $this->table('datapoints');
        $table->addColumn('status', 'integer', [
            'default' => 0,
        ]);
        $table->update();
    }
}
