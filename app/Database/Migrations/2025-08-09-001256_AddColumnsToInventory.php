<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnsToInventory extends Migration
{
    public function up()
    {
        $fields = [
            'is_for_sale' => [
                'type'       => 'LONGTEXT',
                'null'       => true,
            ],
            'note' => [
                'type'       => 'LONGTEXT',
                'null'       => true,
            ],
        ];

        $this->forge->addColumn('inventory', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('inventory', ['is_for_sale', 'note']);
    }
}
