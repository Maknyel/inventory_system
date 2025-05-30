<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInventoryHistoryGroupTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => true,
                'auto_increment' => true
            ],
            'inventory_history_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'null'       => true
            ],
            'dr_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => true
            ]
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('inventory_history_group');
    }

    public function down()
    {
        $this->forge->dropTable('inventory_history_group');
    }
}
