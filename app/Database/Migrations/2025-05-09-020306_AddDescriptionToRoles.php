<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDescriptionToRoles extends Migration
{
    public function up()
    {
        // Add the description column to the roles table
        $this->forge->addColumn('roles', [
            'description' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'name', // Position it after the 'name' column
            ],
        ]);
    }

    public function down()
    {
        // Drop the description column if rolling back
        $this->forge->dropColumn('roles', 'description');
    }
}
