<?php
use Migrations\AbstractMigration;

class RemoveOutputIdFromRules extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function up()
    {
        $this->execute('INSERT INTO rules_outputs (rule_id, output_id) 
SELECT DISTINCT id, output_id FROM rules;');

        $table = $this->table('rules');
        $table->removeColumn('output_id');
        $table->update();
    }

    public function down()
    {
        // This is for Dev only. We won't be downgrading any production data, so this rollback is incomplete. Once you roll up, then back, you'll lose Rules.output_id values.
        
        $table = $this->table('rules');
        $table->addColumn('output_id','integer');
        $table->update();   
    }
}
