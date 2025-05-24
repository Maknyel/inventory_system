<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUnitToInventoryTable extends Migration
{
    public function up()
    {
        $fields = [
            'unit' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'after' => 'sub_inventory_type', // Place after this column
                'null' => true, // Allow NULL initially
            ],
        ];

        $this->forge->addColumn('inventory', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('inventory', 'unit');
    }
}
