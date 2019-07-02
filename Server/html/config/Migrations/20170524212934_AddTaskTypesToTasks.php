<?php
use Migrations\AbstractMigration;

class AddTaskTypesToTasks extends AbstractMigration
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
        $tasks = $this->fetchAll('SELECT * FROM tasks');
        foreach ($tasks as $task) {
            $plant = 'Plant batch in';
            $move = 'Move batch to';
            $remove = 'Remove batch from';

            if ($task['completed_date'] != '0000-00-00 00:00:00') {
                $this->execute("UPDATE tasks SET status = 1 WHERE id = ".$task['id']);
            }
            if ($task['label'] == $plant) {
                $this->execute("UPDATE tasks SET type = 0 WHERE id = ".$task['id']);
            } else if ($task['label'] == $move) {
                $this->execute("UPDATE tasks SET type = 1 WHERE id = ".$task['id']);
            } else if ($task['label'] == $remove) {
                $this->execute("UPDATE tasks SET type = 2 WHERE id = ".$task['id']);
            } else {
                $this->execute("UPDATE tasks SET type = 3 WHERE id = ".$task['id']);
            }
        }
    }
}
