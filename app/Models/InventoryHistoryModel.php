<?php
namespace App\Models;

use CodeIgniter\Model;

class InventoryHistoryModel extends Model
{
    protected $table = 'inventory_history';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'name', 'description', 'price', 'quantity', 'return_quantity',
        'in_out', 'inventory_id', 'user_id', 'remarks', 'supplier_id',
        'created_at', 'updated_at', 'customer_own_distribution', 'distributor_id'
    ];
    protected $useTimestamps = true;
}
