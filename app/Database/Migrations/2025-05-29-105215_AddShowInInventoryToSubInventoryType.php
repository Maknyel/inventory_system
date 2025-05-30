<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddShowInInventoryToSubInventoryType extends Migration
{
    public function up()
    {
        $fields = [
            'show_in_inventory' => [
                'type'       => 'TINYINT',
                'null'       => true,
                'default'    => 0 // replace this with the column name after which you want to add this
            ],
        ];
        $this->forge->addColumn('sub_inventory_type', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('sub_inventory_type', 'show_in_inventory');
    }
}
