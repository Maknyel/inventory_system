<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInventoryHistoryTable extends Migration
{
    public function up()
    {
        // Create 'inventory_history' table
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name'          => ['type' => 'LONGTEXT', 'null' => true],  // Nullable
            'description'   => ['type' => 'LONGTEXT', 'null' => true],  // Nullable
            'price'         => ['type' => 'LONGTEXT', 'null' => true],  // Nullable
            'quantity'      => ['type' => 'LONGTEXT', 'null' => true],  // Nullable
            'return_quantity'      => ['type' => 'LONGTEXT', 'null' => true],  // Nullable
            'in_out'        => ['type' => 'LONGTEXT', 'null' => true],  // Nullable
            'inventory_id'  => ['type' => 'INT', 'unsigned' => true, 'null' => true],  // Nullable, foreign key (optional)
            'user_id'       => ['type' => 'INT', 'unsigned' => true, 'null' => true],  // Nullable, foreign key (optional)
            'remarks'       => ['type' => 'LONGTEXT', 'null' => true],  // Nullable
            'supplier_id'   => ['type' => 'INT', 'unsigned' => true, 'null' => true],  // Nullable, foreign key (optional)
            'created_at'    => ['type' => 'DATETIME', 'null' => false], // Non-nullable
            'updated_at'    => ['type' => 'DATETIME', 'null' => false], // Non-nullable
        ]);

        // Add primary key
        $this->forge->addPrimaryKey('id');

        // Create the table
        $this->forge->createTable('inventory_history');
    }

    public function down()
    {
        // Drop the 'inventory_history' table if it exists
        $this->forge->dropTable('inventory_history');
    }
}
