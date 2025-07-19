<?php

namespace App\Models;

use CodeIgniter\Model;

class InventoryHistoryGroupModel extends Model
{
    protected $table = 'inventory_history_group';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'inventory_history_id',
        'dr_number',
        'created_at',
        'updated_at',
        'total_amount',
        'discount',
        'discount_amount',
        'grand_total_amount',
    ];

    // Enable automatic timestamps if you want CI to handle created_at/updated_at automatically
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Optional: Define return type if you want objects instead of arrays
    // protected $returnType = 'array'; // default

    // You can add relationships or custom methods here if needed
}
