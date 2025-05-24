<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => [
                'type'           => 'INT',
                'constraint'     => 10,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name'        => [
                'type'       => 'LONGTEXT',
                'null'       => false,
            ],
            'email'       => [
                'type'       => 'LONGTEXT',
                'null'       => false,
                'unique'     => true,
            ],
            'password'    => [
                'type'       => 'LONGTEXT',
                'null'       => false,
            ],
            'role_id'     => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'null'       => true, // Nullable
            ],
            'image_url'   => [
                'type'       => 'LONGTEXT',
                'null'       => true,
            ],
            'created_at'  => [
                'type'       => 'DATETIME',
                'null'       => true,
                'default'    => null,
            ],
            'updated_at'  => [
                'type'       => 'DATETIME',
                'null'       => true,
                'default'    => null,
            ],
        ]);

        // Add primary key and foreign key for role_id if necessary
        $this->forge->addPrimaryKey('id');
        // If you want to add the foreign key, uncomment this
        // $this->forge->addForeignKey('role_id', 'roles', 'id', 'CASCADE', 'CASCADE');
        
        $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}
