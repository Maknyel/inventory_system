<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDistributorTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'type' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'name' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'description' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);

        $this->forge->addKey('id', true); // Primary key
        $this->forge->createTable('distributor');
    }

    public function down()
    {
        $this->forge->dropTable('distributor');
    }
}