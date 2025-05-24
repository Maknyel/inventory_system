<?php

namespace App\Models;

use CodeIgniter\Model;

class InventorySupplierModel extends Model
{
    protected $table      = 'inventory_supplier';
    protected $primaryKey = 'id';

    protected $allowedFields = ['name', 'description'];

    // Timestamps (if using created_at and updated_at columns)
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
