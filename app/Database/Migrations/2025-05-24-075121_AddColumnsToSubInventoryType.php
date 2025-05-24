<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnsToSubInventoryType extends Migration
{
    public function up()
    {
        $fields = [
            'has_purpose' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'default' => 0,
            ],
            'has_distributor' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'default' => 0,
            ],
            'has_reeturn' => [   // note the typo here as you requested; if you want "has_return", just rename it
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'default' => 0,
            ],
        ];

        $this->forge->addColumn('sub_inventory_type', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('sub_inventory_type', ['has_purpose', 'has_distributor', 'has_reeturn']);
    }
}
