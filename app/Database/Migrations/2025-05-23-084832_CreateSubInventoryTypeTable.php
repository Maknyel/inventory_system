<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSubInventoryTypeTable extends Migration
{
    public function up()
    {
        // Creating 'inventory_type' table
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name'        => ['type' => 'LONGTEXT', 'null' => false], // Non-nullable
            'description' => ['type' => 'LONGTEXT', 'null' => true],  // Nullable
            'inventory_type_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],  // Nullable
            'created_at'  => ['type' => 'DATETIME', 'null' => true],  // Nullable
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],  // Nullable
        ]);

        // Add primary key
        $this->forge->addPrimaryKey('id');

        // Create the table
        $this->forge->createTable('sub_inventory_type');
    }

    public function down()
    {
        // Drop the 'sub_inventory_type' table if it exists
        $this->forge->dropTable('sub_inventory_type');
    }
}
