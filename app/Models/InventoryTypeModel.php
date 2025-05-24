<?php

namespace App\Models;

use CodeIgniter\Model;

class InventoryTypeModel extends Model
{
    protected $table      = 'inventory_type';
    protected $primaryKey = 'id';

    protected $allowedFields = ['name', 'description'];

    // Timestamps (if using created_at and updated_at columns)
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
