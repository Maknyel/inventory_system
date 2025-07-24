<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddShowInInventoryToSubInventoryType extends Migration
{
    public function up()
    {
        // Get database connection
        $db = \Config\Database::connect();

        // Only add column if it doesn't exist
        if (! $db->fieldExists('show_in_inventory', 'sub_inventory_type')) {
            $fields = [
                'show_in_inventory' => [
                    'type'       => 'TINYINT',
                    'null'       => true,
                    'default'    => 0,
                ],
            ];
            $this->forge->addColumn('sub_inventory_type', $fields);
        }
    }

    public function down()
    {
        // Drop the column only if it exists
        $db = \Config\Database::connect();
        if ($db->fieldExists('show_in_inventory', 'sub_inventory_type')) {
            $this->forge->dropColumn('sub_inventory_type', 'show_in_inventory');
        }
    }
}
