<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInventorySupplierTable extends Migration
{
    public function up()
    {
        // Create 'inventory_supplier' table
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name'          => ['type' => 'LONGTEXT', 'null' => true],  // Nullable
            'description'   => ['type' => 'LONGTEXT', 'null' => true],  // Nullable
            'created_at'    => ['type' => 'DATETIME', 'null' => false], // Non-nullable
            'updated_at'    => ['type' => 'DATETIME', 'null' => false], // Non-nullable
        ]);

        // Add primary key
        $this->forge->addPrimaryKey('id');

        // Create the table
        $this->forge->createTable('inventory_supplier');
    }

    public function down()
    {
        // Drop the 'inventory_supplier' table if it exists
        $this->forge->dropTable('inventory_supplier');
    }
}
