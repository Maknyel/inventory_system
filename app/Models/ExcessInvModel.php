<?php

namespace App\Models;

use CodeIgniter\Model;

class ExcessInvModel extends Model
{
    protected $table = 'excess_inv';
    protected $primaryKey = 'id';
    protected $allowedFields = ['inventory_id', 'quantity', 'history', 'created_at', 'updated_at'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Example validation rules (adjust as needed)
    protected $validationRules = [
        'inventory_id' => 'permit_empty|string',
        'quantity'     => 'permit_empty|string',
        'history'      => 'permit_empty|string',
    ];

    protected $validationMessages = [
        'inventory_id' => [
            'string' => 'Inventory ID must be a string',
        ],
        'quantity' => [
            'string' => 'Quantity must be a string',
        ],
        'history' => [
            'string' => 'History must be a string',
        ],
    ];

    // You can add custom methods if you want
    // Example: filter by inventory_id
    public function findByInventoryId($inventoryId)
    {
        return $this->where('inventory_id', $inventoryId)->findAll();
    }
}
