<?php
use Migrations\AbstractMigration;

class ChangeTagTasksToMove extends AbstractMigration
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
        $this->execute("UPDATE tasks SET type = 0 WHERE label = 'Plant batch in'");
    }
}
