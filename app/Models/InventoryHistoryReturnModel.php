<?php
namespace App\Models;

use CodeIgniter\Model;

class InventoryHistoryReturnModel extends Model
{
    protected $table = 'inventory_history_return';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'inventory_out_id', 'quantity', 'remarks'
    ];
    protected $useTimestamps = true;
}
