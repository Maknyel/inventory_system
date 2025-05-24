<?php

namespace App\Models;

use CodeIgniter\Model;

class InventoryModel extends Model
{
    protected $table = 'inventory';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'description', 'unit', 'inventory_type', 'sub_inventory_type', 'reordering_level', 'icon', 'created_at', 'updated_at'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Optionally, if you want to use validation rules
    protected $validationRules = [
        'name' => 'required|min_length[3]',
        'description' => 'required',
        'inventory_type' => 'required',
        'reordering_level' => 'required|integer',
    ];

    // Optionally, custom validation messages
    protected $validationMessages = [
        'name' => [
            'required' => 'The inventory name is required.',
            'min_length' => 'The inventory name must be at least 3 characters.',
        ],
        // Add additional validation messages as needed
    ];

    public function joinInventoryType()
    {
        return $this->select('inventory.*, inventory_type.name as inventory_type_name')
                    ->join('inventory_type', 'inventory.inventory_type = inventory_type.id');
    }
}
