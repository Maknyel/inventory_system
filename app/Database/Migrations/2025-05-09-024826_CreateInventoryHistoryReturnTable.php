<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInventoryHistoryReturnTable extends Migration
{
    public function up()
    {
        // Create 'inventory_history_return' table
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'inventory_out_id'=> ['type' => 'LONGTEXT', 'null' => true],  // Foreign key from inventory_out table
            'quantity'        => ['type' => 'LONGTEXT', 'null' => true],  // Nullable
            'remarks'         => ['type' => 'LONGTEXT', 'null' => true],  // Nullable
            'created_at'      => ['type' => 'DATETIME', 'null' => false], // Non-nullable
            'updated_at'      => ['type' => 'DATETIME', 'null' => false], // Non-nullable
        ]);

        // Add primary key
        $this->forge->addPrimaryKey('id');

        // Create the table
        $this->forge->createTable('inventory_history_return');
    }

    public function down()
    {
        // Drop the 'inventory_history_return' table if it exists
        $this->forge->dropTable('inventory_history_return');
    }
}
