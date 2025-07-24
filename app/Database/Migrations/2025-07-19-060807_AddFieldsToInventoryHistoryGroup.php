<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldsToInventoryHistoryGroup extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        $fieldsToAdd = [];

        if (! $db->fieldExists('total_amount', 'inventory_history_group')) {
            $fieldsToAdd['total_amount'] = [
                'type' => 'LONGTEXT',
                'null' => true,
            ];
        }
        if (! $db->fieldExists('discount', 'inventory_history_group')) {
            $fieldsToAdd['discount'] = [
                'type' => 'LONGTEXT',
                'null' => true,
            ];
        }
        if (! $db->fieldExists('discount_amount', 'inventory_history_group')) {
            $fieldsToAdd['discount_amount'] = [
                'type' => 'LONGTEXT',
                'null' => true,
            ];
        }
        if (! $db->fieldExists('grand_total_amount', 'inventory_history_group')) {
            $fieldsToAdd['grand_total_amount'] = [
                'type' => 'LONGTEXT',
                'null' => true,
            ];
        }

        if (! empty($fieldsToAdd)) {
            $this->forge->addColumn('inventory_history_group', $fieldsToAdd);
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();

        $fields = ['total_amount', 'discount', 'discount_amount', 'grand_total_amount'];

        foreach ($fields as $field) {
            if ($db->fieldExists($field, 'inventory_history_group')) {
                $this->forge->dropColumn('inventory_history_group', $field);
            }
        }
    }
}
