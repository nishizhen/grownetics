<?php
use Migrations\AbstractMigration;

class AddCultivarAndZoneIdToNotes extends AbstractMigration
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
        $this->table('notes')
        ->addColumn('cultivar_id', 'integer', [
            'default' => null,
            'length'  => 11,
            'null'    => true,
        ])
        ->addColumn('zone_id', 'integer', [
            'default' => null,
            'length'  => 11,
            'null'    => true,
        ])
        ->changeColumn('batch_id', 'integer', [
            'default' => null,
            'length'  => 11,
            'null'    => true
        ])
        ->changeColumn('note', 'string', [
            'default' => null,
            'length'  => 255,
            'null'    => true
        ])
        ->update();

        $table = $this->table('notes_plants');
        $table->addColumn('note_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => true,
        ]);
        $table->addColumn('plant_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => true,
        ]);
        $table->create();

        $table = $this->table('photos');
        $table->addColumn('photo_name', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => true,
        ])
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
        ->addColumn('deleted', 'datetime', [
            'default' => null,
            'null' => true,
        ]);
        $table->create();

        $table = $this->table('notes_photos');
        $table->addColumn('note_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => true,
        ]);
        $table->addColumn('photo_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => true,
        ]);
        $table->create();
    }
}
