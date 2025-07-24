<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateExcessInvTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'inventory_id' => [
                'type'       => 'LONGTEXT',
                'null'       => true,
            ],
            'quantity' => [
                'type'       => 'LONGTEXT',
                'null'       => true,
            ],
            'history' => [
                'type'       => 'LONGTEXT',
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true); // Primary key
        $this->forge->createTable('excess_inv');
    }

    public function down()
    {
        $this->forge->dropTable('excess_inv');
    }
}
