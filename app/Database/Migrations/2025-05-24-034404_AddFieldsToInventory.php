<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldsToInventory extends Migration
{
    public function up()
    {
        $this->forge->addColumn('inventory', [
            
            'sub_inventory_type' => [
                'type'       => 'INT',
                'null'       => true,
            ],
            
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('inventory', [
            'customer_own_distribution',
            'sub_inventory_type',
            'distributor_id',
        ]);
    }
}
