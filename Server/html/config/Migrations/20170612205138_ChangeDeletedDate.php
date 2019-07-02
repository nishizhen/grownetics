<?php
use Migrations\AbstractMigration;

class ChangeDeletedDate extends AbstractMigration
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
        foreach ([
                     'acls',
                     'acls_roles',
                     'appliance_templates',
                     'appliances',
                     'appliances_zones',
                     'batch_notes',
                     'batch_recipe_entries',
                     'chats',
                     'controlpoint',
                     'devices',
                     'facilities',
                     'floorplans',
                     'harvest_batch_logs',
                     'harvest_batches',
                     'map_item_types',
                     'map_items',
                     'notes',
                     'notifications',
                     'outputs',
                     'plants',
                     'recipe_entries',
                     'recipes',
                     'roles',
                     'rule_action_targets',
                     'rule_actions',
                     'rule_conditions',
                     'rules',
                     'sensor_types',
                     'sensors',
                     'sensors_zones',
                     'strains',
                     'tasks',
                     'user_contact_methods',
                     'users',
                     'wikis',
                     'zones'
                 ] as $tableName) {

            $table = $this->table($tableName);

            if ($table->hasColumn('deleted_date')) {
                $table->removeColumn('deleted_date');
            }
            if ($table->hasColumn('deleted')) {
                $table->changeColumn('deleted', 'datetime');
            } else {
                $table->addColumn('deleted', 'datetime');
            }
            $table->save();
        }
    }
}
