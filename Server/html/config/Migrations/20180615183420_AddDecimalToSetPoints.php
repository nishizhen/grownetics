<?php
use Migrations\AbstractMigration;

class AddDecimalToSetPoints extends AbstractMigration
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
        $this->table('set_points')
        ->changeColumn('value', 'decimal', [
                'default' => null,
                'precision' => 9,
                'scale' => 5,
                'null' => true
            ])
        ->update();
    }
}
