<?php
use Migrations\AbstractMigration;

class AddDefaultToRules extends AbstractMigration
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
        $table = $this->table('rules');
        $table->addColumn('is_default', 'boolean', [
            'default' => null,
            'null' => false
        ]);
        $table->update();
        $table = $this->table('rule_conditions');
        $table->addColumn('is_default', 'boolean', [
            'default' => null,
            'null' => false
        ]);
        $table->update();
        $table = $this->table('rule_actions');
        $table->addColumn('is_default', 'boolean', [
            'default' => null,
            'null' => false
        ]);
        $table->update();
        $table = $this->table('rule_action_targets');
        $table->addColumn('is_default', 'boolean', [
            'default' => null,
            'null' => false
        ]);
        $table->update();
    }
}
