<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UsersSeeder extends Seeder
{
    public function run()
    {
        // User data for Admin
        $data = [
            'name' => 'Marcniel Agustin',
            'email' => 'marcniel_christian12@yahoo.com',
            'password' => password_hash('Abc123456', PASSWORD_BCRYPT), // Make sure to hash the password
            'role_id' => 1, // Assuming '1' is the Admin role ID in your roles table
            'image_url' => null, // Optional field
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        // Insert the admin user into the 'users' table
        $this->db->table('users')->insert($data);
    }
}
