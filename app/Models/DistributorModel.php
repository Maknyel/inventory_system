<?php

namespace App\Models;

use CodeIgniter\Model;

class DistributorModel extends Model
{
    protected $table = 'distributor';         // Table name
    protected $primaryKey = 'id';             // Primary key

    protected $allowedFields = ['name', 'description', 'type'];

    // Timestamps (if using created_at and updated_at columns)
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
