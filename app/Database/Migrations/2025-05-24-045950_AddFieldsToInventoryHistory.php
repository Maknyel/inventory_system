<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldsToInventoryHistory extends Migration
{
    public function up()
    {
        $this->forge->addColumn('inventory_history', [
            'customer_own_distribution' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'distributor_id' => [
                'type' => 'INT',
                'null' => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('inventory_history', [
            'customer_own_distribution',
            'distributor_id',
        ]);
    }
}
