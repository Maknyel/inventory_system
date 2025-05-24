<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInventoryTable extends Migration
{
    public function up()
    {
        // Creating 'inventory' table
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'icon'             => ['type' => 'LONGTEXT', 'null' => true],
            'name'             => ['type' => 'LONGTEXT', 'null' => true],
            'description'      => ['type' => 'LONGTEXT', 'null' => true],
            'current_price'    => ['type' => 'FLOAT', 'null' => true],
            'current_quantity' => ['type' => 'INT', 'null' => true],
            'inventory_type'   => ['type' => 'LONGTEXT', 'null' => true],
            'reordering_level' => ['type' => 'INT', 'null' => true],
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
            'updated_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);
        
        // Add primary key
        $this->forge->addPrimaryKey('id');

        // Create the table
        $this->forge->createTable('inventory');
    }

    public function down()
    {
        // Drop the 'inventory' table if it exists
        $this->forge->dropTable('inventory');
    }
}
