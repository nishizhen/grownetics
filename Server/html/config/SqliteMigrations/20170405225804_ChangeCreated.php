<?php
use Migrations\AbstractMigration;

class ChangeCreated extends AbstractMigration
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
        $this->table('datapoints')
            ->removeColumn('created')
            ->update();
        $this->table('datapoints')
            ->addColumn('created', 'datetime', [
                'default' => null,
                'length' => null,
                'null' => true,
            ])
            ->update();
    }
}
