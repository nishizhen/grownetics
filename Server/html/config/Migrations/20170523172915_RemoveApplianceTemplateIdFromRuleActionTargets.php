<?php
use Migrations\AbstractMigration;

class RemoveApplianceTemplateIdFromRuleActionTargets extends AbstractMigration
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
        $table = $this->table('rule_action_targets');
        $table->removeColumn('appliance_template_id');
        $table->update();
    }
}
