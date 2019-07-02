<?php
use Migrations\AbstractMigration;

class AddExtensionToPhotos extends AbstractMigration
{

    public function up()
    {

        $this->table('photos')
            ->addColumn('extension', 'string', [
                'after' => 'deleted',
                'default' => null,
                'length' => 45,
                'null' => true,
            ])
            ->update();

        $this->table('batch_notes')->drop()->save();
    }

    public function down()
    {

        $this->table('batch_notes')
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
            ->addColumn('user_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('harvest_batch_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('note', 'text', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('deleted', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->create();

        $this->table('photos')
            ->removeColumn('extension')
            ->update();
    }
}

