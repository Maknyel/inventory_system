<?php
use CodeIgniter\Database\Database;
use App\Models\UserModel;

function global_name()
{
    return "Faith Construct Epoxy Resin Specialist";
}

function generateInitialsImage($name) {
    $parts = explode(' ', $name);
    $initials = strtoupper(substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));

    $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100">
        <rect width="100%" height="100%" fill="#ccc"/>
        <text x="50%" y="50%" font-size="40" text-anchor="middle" fill="#333" dy=".3em" font-family="Arial, sans-serif">'
        . $initials .
        '</text>
    </svg>';

    return 'data:image/svg+xml;base64,' . base64_encode($svg);
}

if (!function_exists('current_user')) {
    function current_user()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return null;
        }

        $userModel = new UserModel();
        return $userModel->find($userId);
    }
}

function inventory_supplier_count(): int
{
    $db = \Config\Database::connect();
    return $db->table('inventory_supplier')->countAll();
}

function inventory_item_count(): int
{
    $db = \Config\Database::connect();
    return $db->table('inventory')->countAll();
}

function low_stock_inventory_count(): int
{
    $db = \Config\Database::connect();
    $builder = $db->table('inventory');

    $builder->groupStart()
        ->where('reordering_level > current_quantity')
        ->orWhere('current_quantity IS NULL')
        ->orWhere('current_quantity <', 1)
        ->groupEnd();

    return $builder->countAllResults();
}

function get_low_stock_items(): array
{
    $db = \Config\Database::connect();
    $builder = $db->table('inventory');
    $builder->select('inventory.icon, inventory.name, inventory.description, inventory.current_quantity, inventory.current_price, inventory.reordering_level, inventory_type.name as inventory_type_name');
    $builder->join('inventory_type', 'inventory.inventory_type = inventory_type.id', 'left');
    
    $builder->groupStart()
        ->where('reordering_level > current_quantity')
        ->orWhere('current_quantity IS NULL')
        ->orWhere('current_quantity <', 1)
        ->groupEnd();

    return $builder->get()->getResultArray();
}

function users_count(): int
{
    $db = \Config\Database::connect();
    return $db->table('users')->countAll();
}
