<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldsToInventoryHistoryGroup extends Migration
{
    public function up()
    {
        $this->forge->addColumn('inventory_history_group', [
            'total_amount' => [
                'type'       => 'LONGTEXT',
                'null'       => true,
            ],
            'discount' => [
                'type'       => 'LONGTEXT',
                'null'       => true,
            ],
            'discount_amount' => [
                'type'       => 'LONGTEXT',
                'null'       => true,
            ],
            'grand_total_amount' => [
                'type'       => 'LONGTEXT',
                'null'       => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('inventory_history_group', [
            'total_amount',
            'discount',
            'discount_amount',
            'grand_total_amount',
        ]);
    }
}
