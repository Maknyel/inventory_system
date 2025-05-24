<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RolesSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name' => 'Admin',
                'description' => 'Administrator with full access with the exception of updating and deleting same admin',
            ],
            [
                'name' => 'User',
                'description' => 'Standard user with limited access',
            ],
            [
                'name' => 'Guest',
                'description' => 'Guest user with minimal access',
            ],
            [
                'name' => 'Super Admin',
                'description' => 'Super Admin with full access',
            ],
        ];

        // Using query builder to insert data
        $this->db->table('roles')->insertBatch($data);
    }
}
