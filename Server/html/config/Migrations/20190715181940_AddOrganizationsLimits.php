<?php
use Migrations\AbstractMigration;

class AddOrganizationsLimits extends AbstractMigration
{

    public function up()
    {

        $this->table('appliances')
            ->addColumn('owner_type', 'integer', [
                'after' => 'deleted',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->addColumn('owner_id', 'integer', [
                'after' => 'owner_type',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->update();

        $this->table('appliances_zones')
            ->addColumn('owner_type', 'integer', [
                'after' => 'deleted',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->addColumn('owner_id', 'integer', [
                'after' => 'owner_type',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->update();

        $this->table('batch_recipe_entries')
            ->addColumn('owner_type', 'integer', [
                'after' => 'days',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->addColumn('owner_id', 'integer', [
                'after' => 'owner_type',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->update();

        $this->table('chats')
            ->addColumn('owner_type', 'integer', [
                'after' => 'deleted',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->addColumn('owner_id', 'integer', [
                'after' => 'owner_type',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->update();

        $this->table('cultivars')
            ->addColumn('owner_type', 'integer', [
                'after' => 'batch_count',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->addColumn('owner_id', 'integer', [
                'after' => 'owner_type',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->update();

        $this->table('devices')
            ->addColumn('owner_type', 'integer', [
                'after' => 'type',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->addColumn('owner_id', 'integer', [
                'after' => 'owner_type',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->update();

        $this->table('facilities')
            ->addColumn('owner_type', 'integer', [
                'after' => 'deleted',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->addColumn('owner_id', 'integer', [
                'after' => 'owner_type',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->update();

        $this->table('floorplans')
            ->addColumn('owner_type', 'integer', [
                'after' => 'deleted',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->addColumn('owner_id', 'integer', [
                'after' => 'owner_type',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->update();

        $this->table('harvest_batches')
            ->addColumn('owner_type', 'integer', [
                'after' => 'dry_whole_trimmed_weight',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->addColumn('owner_id', 'integer', [
                'after' => 'owner_type',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->update();

        $this->table('map_items')
            ->addColumn('owner_type', 'integer', [
                'after' => 'zone_id',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->addColumn('owner_id', 'integer', [
                'after' => 'owner_type',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->update();

        $this->table('map_items_zones')
            ->addColumn('owner_type', 'integer', [
                'after' => 'zone_id',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->addColumn('owner_id', 'integer', [
                'after' => 'owner_type',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->update();

        $this->table('notes')
            ->addColumn('owner_type', 'integer', [
                'after' => 'zone_id',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->addColumn('owner_id', 'integer', [
                'after' => 'owner_type',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->update();

        $this->table('notes_photos')
            ->addColumn('owner_type', 'integer', [
                'after' => 'photo_id',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->addColumn('owner_id', 'integer', [
                'after' => 'owner_type',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->update();

        $this->table('notes_plants')
            ->addColumn('owner_type', 'integer', [
                'after' => 'plant_id',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->addColumn('owner_id', 'integer', [
                'after' => 'owner_type',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->update();

        $this->table('notifications')
            ->addColumn('owner_type', 'integer', [
                'after' => 'user_id',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->addColumn('owner_id', 'integer', [
                'after' => 'owner_type',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->update();

        $this->table('outputs')
            ->addColumn('owner_type', 'integer', [
                'after' => 'pre_high_temp_shutdown_status',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->addColumn('owner_id', 'integer', [
                'after' => 'owner_type',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->update();

        $this->table('photos')
            ->addColumn('owner_type', 'integer', [
                'after' => 'extension',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->addColumn('owner_id', 'integer', [
                'after' => 'owner_type',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->update();

        $this->table('plants')
            ->addColumn('owner_type', 'integer', [
                'after' => 'cultivar_id',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->addColumn('owner_id', 'integer', [
                'after' => 'owner_type',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->update();

        $this->table('recipe_entries')
            ->addColumn('owner_type', 'integer', [
                'after' => 'task_label',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->addColumn('owner_id', 'integer', [
                'after' => 'owner_type',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->update();

        $this->table('recipes')
            ->addColumn('owner_type', 'integer', [
                'after' => 'deleted',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->addColumn('owner_id', 'integer', [
                'after' => 'owner_type',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->update();

        $this->table('rule_action_targets')
            ->addColumn('owner_type', 'integer', [
                'after' => 'deleted',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->addColumn('owner_id', 'integer', [
                'after' => 'owner_type',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->update();

        $this->table('rule_actions')
            ->addColumn('owner_type', 'integer', [
                'after' => 'deleted',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->addColumn('owner_id', 'integer', [
                'after' => 'owner_type',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->update();

        $this->table('rule_conditions')
            ->addColumn('owner_type', 'integer', [
                'after' => 'averaging_method',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->addColumn('owner_id', 'integer', [
                'after' => 'owner_type',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->update();

        $this->table('rules')
            ->addColumn('owner_type', 'integer', [
                'after' => 'type',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->addColumn('owner_id', 'integer', [
                'after' => 'owner_type',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->update();

        $this->table('sensors')
            ->addColumn('owner_type', 'integer', [
                'after' => 'data_type',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->addColumn('owner_id', 'integer', [
                'after' => 'owner_type',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->update();

        $this->table('sensors_zones')
            ->addColumn('owner_type', 'integer', [
                'after' => 'deleted',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->addColumn('owner_id', 'integer', [
                'after' => 'owner_type',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->update();

        $this->table('set_points')
            ->addColumn('owner_type', 'integer', [
                'after' => 'alert_level',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->addColumn('owner_id', 'integer', [
                'after' => 'owner_type',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->update();

        $this->table('tasks')
            ->addColumn('owner_type', 'integer', [
                'after' => 'type',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->addColumn('owner_id', 'integer', [
                'after' => 'owner_type',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->update();

        $this->table('zones')
            ->addColumn('owner_type', 'integer', [
                'after' => 'light_level_set',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->addColumn('owner_id', 'integer', [
                'after' => 'owner_type',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->update();

        $this->table('controlpoint')->drop()->save();

        $this->table('harvest_batch_logs')->drop()->save();

        $this->table('wikis')->drop()->save();
    }

    public function down()
    {

        $this->table('controlpoint')
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('control_type', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('input_sensor_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('output_sensor_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('status', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('deleted', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->create();

        $this->table('harvest_batch_logs')
            ->addColumn('label', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('description', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('zone_id', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('batch_id', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('entry_date', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('deleted', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->create();

        $this->table('wikis')
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('label', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('body', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('slug', 'string', [
                'default' => null,
                'limit' => 45,
                'null' => true,
            ])
            ->addColumn('version', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('user_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('change_message', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('deleted', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->create();

        $this->table('appliances')
            ->removeColumn('owner_type')
            ->removeColumn('owner_id')
            ->update();

        $this->table('appliances_zones')
            ->removeColumn('owner_type')
            ->removeColumn('owner_id')
            ->update();

        $this->table('batch_recipe_entries')
            ->removeColumn('owner_type')
            ->removeColumn('owner_id')
            ->update();

        $this->table('chats')
            ->removeColumn('owner_type')
            ->removeColumn('owner_id')
            ->update();

        $this->table('cultivars')
            ->removeColumn('owner_type')
            ->removeColumn('owner_id')
            ->update();

        $this->table('devices')
            ->removeColumn('owner_type')
            ->removeColumn('owner_id')
            ->update();

        $this->table('facilities')
            ->removeColumn('owner_type')
            ->removeColumn('owner_id')
            ->update();

        $this->table('floorplans')
            ->removeColumn('owner_type')
            ->removeColumn('owner_id')
            ->update();

        $this->table('harvest_batches')
            ->removeColumn('owner_type')
            ->removeColumn('owner_id')
            ->update();

        $this->table('map_items')
            ->removeColumn('owner_type')
            ->removeColumn('owner_id')
            ->update();

        $this->table('map_items_zones')
            ->removeColumn('owner_type')
            ->removeColumn('owner_id')
            ->update();

        $this->table('notes')
            ->removeColumn('owner_type')
            ->removeColumn('owner_id')
            ->update();

        $this->table('notes_photos')
            ->removeColumn('owner_type')
            ->removeColumn('owner_id')
            ->update();

        $this->table('notes_plants')
            ->removeColumn('owner_type')
            ->removeColumn('owner_id')
            ->update();

        $this->table('notifications')
            ->removeColumn('owner_type')
            ->removeColumn('owner_id')
            ->update();

        $this->table('outputs')
            ->removeColumn('owner_type')
            ->removeColumn('owner_id')
            ->update();

        $this->table('photos')
            ->removeColumn('owner_type')
            ->removeColumn('owner_id')
            ->update();

        $this->table('plants')
            ->removeColumn('owner_type')
            ->removeColumn('owner_id')
            ->update();

        $this->table('recipe_entries')
            ->removeColumn('owner_type')
            ->removeColumn('owner_id')
            ->update();

        $this->table('recipes')
            ->removeColumn('owner_type')
            ->removeColumn('owner_id')
            ->update();

        $this->table('rule_action_targets')
            ->removeColumn('owner_type')
            ->removeColumn('owner_id')
            ->update();

        $this->table('rule_actions')
            ->removeColumn('owner_type')
            ->removeColumn('owner_id')
            ->update();

        $this->table('rule_conditions')
            ->removeColumn('owner_type')
            ->removeColumn('owner_id')
            ->update();

        $this->table('rules')
            ->removeColumn('owner_type')
            ->removeColumn('owner_id')
            ->update();

        $this->table('sensors')
            ->removeColumn('owner_type')
            ->removeColumn('owner_id')
            ->update();

        $this->table('sensors_zones')
            ->removeColumn('owner_type')
            ->removeColumn('owner_id')
            ->update();

        $this->table('set_points')
            ->removeColumn('owner_type')
            ->removeColumn('owner_id')
            ->update();

        $this->table('tasks')
            ->removeColumn('owner_type')
            ->removeColumn('owner_id')
            ->update();

        $this->table('zones')
            ->removeColumn('owner_type')
            ->removeColumn('owner_id')
            ->update();
    }
}

