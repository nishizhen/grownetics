<?php
use Migrations\AbstractMigration;

class AddAssigneeToTasks extends AbstractMigration
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
        $table = $this->table('tasks');
        $table->addColumn('assignee', 'string', [
            'default' => null,
            'null' => false,
            'limit' => 255,
        ]);
        $table->update();
    }
}
