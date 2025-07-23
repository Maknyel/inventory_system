<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNotificationTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                  => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'inventory_history_id' => [
                'type'       => 'LONGTEXT',
                'null'       => true,
            ],
            'inventory_id'        => [
                'type'       => 'LONGTEXT',
                'null'       => true,
            ],
            'text'                => [
                'type'       => 'LONGTEXT',
                'null'       => true,
            ],
            'column_to_be_updated' => [
                'type'       => 'LONGTEXT',
                'null'       => true,
            ],
            'column_from_value'   => [
                'type'       => 'LONGTEXT',
                'null'       => true,
            ],
            'column_to_value'     => [
                'type'       => 'LONGTEXT',
                'null'       => true,
            ],
            'created_by'          => [
                'type'       => 'LONGTEXT',
                'null'       => true,
            ],
            'is_read'             => [
                'type'       => 'LONGTEXT',
                'null'       => true,
            ],
            'is_accepted'         => [
                'type'       => 'LONGTEXT',
                'null'       => true,
            ],
            'is_viewed'           => [
                'type'       => 'LONGTEXT',
                'null'       => true,
            ],
            'created_at'          => [
                'type'       => 'DATETIME',
                'null'       => false,
            ],
            'updated_at'          => [
                'type'       => 'DATETIME',
                'null'       => false,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('notifications');
    }

    public function down()
    {
        $this->forge->dropTable('notifications');
    }
}
