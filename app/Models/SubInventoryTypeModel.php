<?php

namespace App\Models;

use CodeIgniter\Model;

class SubInventoryTypeModel extends Model
{
    protected $table      = 'sub_inventory_type';
    protected $primaryKey = 'id';

    protected $allowedFields = ['name', 'description', 'inventory_type_id'];

    // Timestamps (if using created_at and updated_at columns)
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
