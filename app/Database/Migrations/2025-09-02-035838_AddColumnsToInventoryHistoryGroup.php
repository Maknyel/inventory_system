<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnsToInventoryHistoryGroup extends Migration
{
    public function up()
    {
        $fields = [
            'name' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'address' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'ref_po_number' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
        ];

        $this->forge->addColumn('inventory_history_group', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('inventory_history_group', ['name', 'address', 'ref_po_number']);
    }
}
