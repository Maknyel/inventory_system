<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table      = 'notifications';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'inventory_history_id',
        'inventory_id',
        'text',
        'column_to_be_updated',
        'column_from_value',
        'column_to_value',
        'created_by',
        'is_read',
        'is_accepted',
        'is_viewed',
    ];

    // Enable automatic timestamps
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Disable validation for now, add if needed
}
