<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'users'; // Name of your users table
    protected $primaryKey = 'id'; // Primary key field

    protected $allowedFields = ['name', 'email', 'password', 'role_id', 'image_url', 'created_at', 'updated_at']; // Add all the fields you want to be mass-assigned

    // If you want to use timestamps (created_at and updated_at), you can enable them
    protected $useTimestamps = true;
    
    // You can set the datetime format (default is 'Y-m-d H:i:s')
    protected $dateFormat = 'datetime';
    
    // You can add validation rules if necessary (example shown below)
    protected $validationRules = [
        'email' => 'required|valid_email',
        'password' => 'required|min_length[6]',
    ];
}
