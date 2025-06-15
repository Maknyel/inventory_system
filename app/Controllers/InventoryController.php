<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\InventoryModel;
use App\Models\InventoryTypeModel;
use App\Models\SubInventoryTypeModel;
use App\Models\InventoryHistoryModel;
use App\Models\InventoryHistoryReturnModel;
use App\Models\InventoryHistoryGroupModel;

class InventoryController extends Controller
{
    public function index()
    {
        $inventoryTypeModel = new InventoryTypeModel();
        $data['inventory_type_data'] = $inventoryTypeModel->findAll();
        if (!session()->has('user_id')) {
            return redirect()->to(base_url('login'));
        }
        return view('inventory/index', $data);
    }
    public function sub_inventory_type($inventory_type_id)
    {
        $subInventoryTypeModel = new SubInventoryTypeModel();
        $data['sub_inventory_type_data'] = $subInventoryTypeModel->where('inventory_type_id', $inventory_type_id)->findAll();


        $inventoryTypeModel = new InventoryTypeModel();
        $id = $inventory_type_id;
        $data['inventory_type_data'] = $inventoryTypeModel->find($id);
        if (!session()->has('user_id')) {
            return redirect()->to(base_url('login'));
        }
        return view('inventory/sub_inventory_type', $data);
    }
    public function show($inventory_type_id, $sub_inventory_type)
    {

        $inventoryModel = new InventoryModel();
        
        $perPage = 10;


        $inventoryTypeModel = new InventoryTypeModel();
        $data['inventory_type_parse'] = $inventoryTypeModel->find($inventory_type_id);

        $subInventoryTypeModel = new SubInventoryTypeModel();
        $data['sub_inventory_type_parse'] = $subInventoryTypeModel->find($sub_inventory_type);

        // Get the current page number from the URL, default to 1
        $currentPage = $this->request->getVar('page_inventory') ?: 1;
        $search = $this->request->getGet('search');
        $orderBy = $this->request->getGet('orderby') ?? 'inventory.name';
        $orderDir = $this->request->getGet('orderdir') ?? 'asc';

        // Fetch paginated inventory data
        $inventoryModel->select('inventory.*, inventory_type.name as inventory_type_name')
                   ->join('inventory_type', 'inventory.inventory_type = inventory_type.id');

        // Apply search
        if (!empty($search)) {
            $inventoryModel->groupStart()
                ->like('inventory.name', $search)
                ->orLike('inventory.description', $search)
                ->orLike('inventory_type.name', $search)
                ->groupEnd();
        }

        if (!empty($inventory_type_id)) {
            $inventoryModel->groupStart()
                ->like('inventory.inventory_type', $inventory_type_id)
                ->groupEnd();
        }
        if (!empty($sub_inventory_type)) {
            $inventoryModel->groupStart()
                ->like('inventory.sub_inventory_type', $sub_inventory_type)
                ->groupEnd();
        }
        
        if (!empty($sub_inventory_type)) {
            $inventoryModel->groupStart()
                ->like('inventory.sub_inventory_type', $sub_inventory_type)
                ->groupEnd();
        }

        // Apply ordering
        $inventoryModel->orderBy($orderBy, $orderDir);

        // Get paginated results
        $data['inventory'] = $inventoryModel->paginate($perPage, 'inventory');


        // Get pager instance to render pagination links
        $data['pager'] = $inventoryModel->pager;

        // Current page
        $data['currentPage'] = $currentPage;

        // Total items (count of all inventory records)
        // $data['totalItems'] = $inventoryModel->countAllResults();

        // Total pages based on the per page count
        // $data['totalPages'] = ceil($data['totalItems'] / $perPage);
        $data['totalItems'] = $inventoryModel->pager->getTotal('inventory');
        $data['totalPages'] = $data['totalItems'] ? ceil($data['totalItems'] / $perPage) : 1;

        // Fetch inventory types for the dropdown
        $inventoryTypeModel = new InventoryTypeModel();
        $data['inventory_type_data'] = $inventoryTypeModel->findAll();

        $data['search'] = $search;
        // $data['inventory_type'] = $inventory_type;
        $data['orderby'] = $orderBy;
        $data['orderdir'] = $orderDir;

        if (!session()->has('user_id')) {
            return redirect()->to(base_url('login'));
        }
        return view('inventory/show', $data);
    }

    public function savePosOut(){
        if ($this->request->isAJAX()) {
            $dr_number = get_dr_number();
            $json = $this->request->getJSON(true);

            $items = $json['items'] ?? [];
            $customer_own_distribution = $json['type'] ?? null;
            $distributor_id = $json['distributor_id'] ?? null;

            if (empty($items)) {
                return $this->response->setStatusCode(400)->setJSON(['error' => 'Cart is empty.']);
            }

            $inventoryModel = new InventoryModel();
            $inventoryHistoryModel = new InventoryHistoryModel();
            $user_id = session()->get('user_id') ?? 1;

            $db = \Config\Database::connect();
            $db->transStart();

            foreach ($items as $item) {
                $id = $item['inventory_id'];
                $quantity = (int)$item['quantity'];
                $remarks = $item['remarks'] ?? 'POS sale';

                // Fetch current inventory record
                $inventory = $inventoryModel->find($id);

                if (!$inventory) {
                    $db->transRollback();
                    return $this->response->setStatusCode(404)->setJSON(['error' => "Inventory item ID {$id} not found."]);
                }

                if ((int)$inventory['current_quantity'] < $quantity) {
                    $db->transRollback();
                    return $this->response->setStatusCode(400)->setJSON(['error' => "Not enough stock for item: {$inventory['name']}"]);
                }

                // Deduct from current_quantity
                $newQuantity = (int)$inventory['current_quantity'] - $quantity;
                $db->table('inventory')
                    ->where('id', $id)
                    ->set('current_quantity', $newQuantity)
                    ->update();

                // Insert into inventory history
                $insertedId = $inventoryHistoryModel->insert([
                    'name'                          => $inventory['name'],
                    'description'                   => $inventory['description'],
                    'price'                         => $inventory['current_price'],
                    'quantity'                      => $quantity,
                    'return_quantity'               => 0,
                    'in_out'                        => 'out',
                    'inventory_id'                  => $id,
                    'user_id'                       => $user_id,
                    'remarks'                       => $remarks,
                    'supplier_id'                   => null,
                    'customer_own_distribution'     => $customer_own_distribution,
                    'distributor_id'                => $distributor_id,
                    'created_at'                    => date('Y-m-d H:i:s'),
                    'updated_at'                    => date('Y-m-d H:i:s')
                ]);

                $inventoryHistoryGroupModel = new InventoryHistoryGroupModel();
                $inventoryHistoryGroupModel->insert([
                    'inventory_history_id'              => $insertedId,
                    'dr_number'                         => $dr_number,
                    'created_at'                        => ('Y-m-d H:i:s'),
                    'updated_at'                        => ('Y-m-d H:i:s'),
                ]);
                
                
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setStatusCode(500)->setJSON(['error' => 'POS transaction failed.']);
            }

            return $this->response->setJSON(['dr_number' => $dr_number, 'message' => 'POS transaction recorded successfully!']);
        }

        return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid request.']);
    }

    public function saveReturn(){
        $json = $this->request->getJSON(true);

        $id = $json['inventory_history_id'];
        $quantity = (int)$json['quantity'];
        $remarks = $json['remarks'] ?? 'Inventory return via form';

        $inventoryModel = new InventoryModel();
        $inventoryHistoryModel = new InventoryHistoryModel();
        $inventoryHistoryReturnModel = new InventoryHistoryReturnModel();

        
        
        // Fetch current inventory record
        $inventoryHistory = $inventoryHistoryModel->find($id);

        if (!$inventoryHistory) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Inventory History not found.']);
        }

        // Deduct from current_quantity
        $newQuantity = (int)$inventoryHistory['return_quantity'] + $quantity;

        $db = \Config\Database::connect();
        $db->table('inventory_history')
            ->where('id', $id)
            ->set('return_quantity', $newQuantity)
            ->update();


        $inventory_id = $inventoryHistory['inventory_id'];
        $inventory = $inventoryModel->find($inventory_id);

        if (!$inventory) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Inventory not found.']);
        }


        // Deduct from current_quantity
        $newQuantityv2 = (int)$inventory['current_quantity'] + $quantity;

        $db = \Config\Database::connect();
        $db->table('inventory')
            ->where('id', $inventory['id'])
            ->set('current_quantity', $newQuantityv2)
            ->update();


        $inventoryHistoryReturnModel->insert([
            'inventory_out_id'              => $id,
            'quantity'                      => $quantity,
            'remarks'                       => $remarks,
            'created_at'                    => date('Y-m-d H:i:s'),
            'updated_at'                    => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON(['message' => 'Inventory return recorded successfully']);
    }

    public function saveOut(){
        $json = $this->request->getJSON(true);

        $id = $json['inventory_id'];
        $quantity = (int)$json['quantity'];
        $remarks = $json['remarks'] ?? 'Inventory out via form';
        $customer_own_distribution = $json['customer_own_distribution'] ?? null;
        $distributor_id = $json['distributor_id'] ?? null;

        $inventoryModel = new InventoryModel();
        $inventoryHistoryModel = new InventoryHistoryModel();

        // Fetch current inventory record
        $inventory = $inventoryModel->find($id);

        if (!$inventory) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Inventory not found.']);
        }

        if ((int)$inventory['current_quantity'] < $quantity) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Not enough stock available.']);
        }

        // Deduct from current_quantity
        $newQuantity = (int)$inventory['current_quantity'] - $quantity;

        $db = \Config\Database::connect();
        $db->table('inventory')
            ->where('id', $id)
            ->set('current_quantity', $newQuantity)
            ->update();

        // Insert into inventory history
        $inventoryHistoryModel->insert([
            'name'                          => $inventory['name'],
            'description'                   => $inventory['description'],
            'price'                         => $inventory['current_price'], // Use current price on out
            'quantity'                      => $quantity,
            'return_quantity'               => 0,
            'in_out'                        => 'out',
            'inventory_id'                  => $id,
            'user_id'                       => session()->get('user_id') ?? 1,
            'remarks'                       => $remarks,
            'supplier_id'                   => null,
            'customer_own_distribution'     => $customer_own_distribution,
            'distributor_id'                => $distributor_id,
            'created_at'                    => date('Y-m-d H:i:s'),
            'updated_at'                    => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON(['message' => 'Inventory out recorded successfully']);
    }

    public function saveStock()
    {
        $json = $this->request->getJSON(true);

        $id = $json['inventory_id'];
        $supplierId = $json['supplier_id'];
        $quantity = (int)$json['quantity'];
        $price = (float)$json['price'];

        $inventoryModel = new InventoryModel();
        $InventoryhistoryModel = new InventoryHistoryModel();

        // Fetch current inventory record
        $inventory = $inventoryModel->find($id);



        if (!$inventory) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Inventory not found.']);
        }

        // Update current_quantity and current_price
        $quantityVal = (int)$inventory['current_quantity'] + $quantity;
        
        $db = \Config\Database::connect();
        $db->table('inventory')
            ->where('id', $id)
            ->set('current_quantity', $quantityVal)
            ->set('current_price', $price)
            ->update();
        

        // Insert into history
        $InventoryhistoryModel->insert([
            'name' => $inventory['name'],
            'description' => $inventory['description'],
            'price' => $price,
            'quantity' => $quantity,
            'return_quantity' => 0,
            'in_out' => 'in',
            'inventory_id' => $id,
            'user_id' => session()->get('user_id') ?? 1, // Adjust user ID as needed
            'remarks' => 'Stock added via form',
            'supplier_id' => $supplierId,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON(['message' => 'Stock added successfully']);
    }

    public function savePosStock()
    {
        $json = $this->request->getJSON(true);
        $supplierId = $json['supplier_id'];
        $items = $json['items'] ?? [];

        $inventoryModel = new InventoryModel();
        $historyModel = new InventoryHistoryModel();
        $db = \Config\Database::connect();
        $userId = session()->get('user_id') ?? 1;

        foreach ($items as $item) {
            $inventory = $inventoryModel->find($item['id']);
            if (!$inventory) continue;

            // Update stock
            $newQty = $inventory['current_quantity'] + $item['quantity'];
            $db->table('inventory')
                ->where('id', $item['id'])
                ->set('current_quantity', $newQty)
                ->set('current_price', $item['price'])
                ->update();

            // Add to history
            $historyModel->insert([
                'name' => $inventory['name'],
                'description' => $inventory['description'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'return_quantity' => 0,
                'in_out' => 'in',
                'inventory_id' => $item['id'],
                'user_id' => $userId,
                'remarks' => 'POS Stock In',
                'supplier_id' => $supplierId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        return $this->response->setJSON(['message' => 'POS stock-in successful']);
    }


    public function getInventoryInList()
    {
        $model = new \App\Models\InventoryHistoryModel();
        
        
        // Get query parameters
        $inventoryType = $this->request->getGet('inventory_type');
        $subInventoryType = $this->request->getGet('sub_inventory_type');

        // Build query
        $query = $model->select('
            inventory_history.id,
            inventory_history.name,
            inventory_history.description,
            inventory_history.price,
            inventory_history.quantity,
            inventory_history.return_quantity,
            inventory_history.in_out,
            inventory_history.inventory_id,
            inventory_history.created_at,
            inventory_history.updated_at,
            inventory.inventory_type,
            inventory.sub_inventory_type
        ');
        $query->where('inventory_history.in_out', 'in');
        $query->where('(inventory_history.price - IFNULL(inventory_history.return_quantity, 0)) >', 0);
        $query->join('inventory', 'inventory_history.inventory_id = inventory.id');

        if ($inventoryType) {
            $query->where('inventory.inventory_type', $inventoryType);
        }

        if ($subInventoryType) {
            $query->where('inventory.sub_inventory_type', $subInventoryType);
        }

        $data = $query->findAll();

        return $this->response->setJSON($data);
    }

    public function getInventoryList()
    {
        $model = new \App\Models\InventoryModel();
        
        
        // Get query parameters
        $inventoryType = $this->request->getGet('inventory_type');
        $subInventoryType = $this->request->getGet('sub_inventory_type');

        // Build query
        $query = $model->select('inventory.id, inventory.name, inventory.current_quantity, inventory.current_price, inventory.unit, inventory.description')
        ->join('sub_inventory_type', 'inventory.sub_inventory_type = sub_inventory_type.id', 'left');

        // if ($inventoryType) {
        //     $query->where('inventory.inventory_type', $inventoryType);
        // }

        // Apply filter: if subInventoryType is set, use it; otherwise, filter where show_in_inventory = 1
        // $query->groupStart()
        //     ->where('inventory.sub_inventory_type', $subInventoryType)
        //     ->orWhere('sub_inventory_type.show_in_inventory', 1)
        // ->groupEnd();
        if ($inventoryType && !$subInventoryType) {
            $query->groupStart()
                ->where('inventory.inventory_type', $inventoryType)
            ->groupEnd()
            ->orWhere('sub_inventory_type.show_in_inventory', 1);
        }else if(!$inventoryType && $subInventoryType){
            $query->groupStart()
                ->where('inventory.sub_inventory_type', $subInventoryType)
            ->groupEnd()
            ->orWhere('sub_inventory_type.show_in_inventory', 1);
        }else{
            $query->groupStart()
                ->where('inventory.inventory_type', $inventoryType)
                ->where('inventory.sub_inventory_type', $subInventoryType)
            ->groupEnd()
            ->orWhere('sub_inventory_type.show_in_inventory', 1);
        }

        $data = $query->findAll();

        return $this->response->setJSON($data);
    }

    public function getInventoryById($id)
    {
        $inventoryModel = new InventoryModel();
        $item = $inventoryModel->find($id);
        return $this->response->setJSON($item);
    }

    public function store()
    {
        $inventoryModel = new InventoryModel();

        $data = [
            'name' => $this->request->getPost('name'),
            'unit' => $this->request->getPost('unit'),
            'description' => $this->request->getPost('description'),
            'inventory_type' => $this->request->getPost('inventory_type'),
            'sub_inventory_type' => $this->request->getPost('sub_inventory_type'),
            'reordering_level' => $this->request->getPost('reordering_level'),
            'icon' => $this->request->getPost('icon'),
        ];


        $inventoryModel->save($data);
        return $this->response->setJSON(['success' => true]);
    }

    public function update($id)
    {
        $inventoryModel = new InventoryModel();
        $item = $inventoryModel->find($id);

        if (!$item) {
            return $this->response->setStatusCode(404, 'Item not found');
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'unit' => $this->request->getPost('unit'),
            'description' => $this->request->getPost('description'),
            'inventory_type' => $this->request->getPost('inventory_type'),
            'sub_inventory_type' => $this->request->getPost('sub_inventory_type'),
            'reordering_level' => $this->request->getPost('reordering_level'),
            'icon' => $this->request->getPost('icon'),
        ];

        $inventoryModel->update($id, $data);
        return $this->response->setJSON(['success' => true]);
    }

    public function delete($id)
    {
        $inventoryModel = new InventoryModel();
        $item = $inventoryModel->find($id);

        if (!$item) {
            return $this->response->setStatusCode(404, 'Item not found');
        }

        $inventoryModel->delete($id);
        return $this->response->setJSON(['success' => true]);
    }

    public function export()
    {
        $inventoryModel = new InventoryModel();
        $items = $inventoryModel->findAll();

        $filename = 'inventory_export_' . date('Y-m-d') . '.csv';
        $file = fopen('php://output', 'w');

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename="' . $filename . '"');

        // Add the column headers
        fputcsv($file, ['ID', 'Icon', 'Name', 'Description', 'Inventory Type', 'Reordering Level', 'Created At', 'Updated At']);

        // Add the data
        foreach ($items as $item) {
            fputcsv($file, [
                $item['id'],
                $item['icon'] ? base_url('uploads/icons/' . $item['icon']) : 'No Icon',
                $item['name'],
                $item['description'],
                $item['inventory_type'],
                $item['reordering_level'],
                $item['created_at'],
                $item['updated_at']
            ]);
        }

        fclose($file);
        exit;
    }

    public function inventoryIn()
    {
        // This method will handle the /inventory_in route
        if (!session()->has('user_id')) {
            return redirect()->to(base_url('login'));
        }
        $inventoryType = $this->request->getGet('inventory_type');
        $subInventoryType = $this->request->getGet('sub_inventory_type');
        $inventoryTypeModel = new InventoryTypeModel();
        $data['inventory_type_parse'] = $inventoryTypeModel->find($inventoryType);

        $subInventoryTypeModel = new SubInventoryTypeModel();
        $data['sub_inventory_type_parse'] = $subInventoryTypeModel->find($subInventoryType);
        return view('inventory/inventory_in', $data);
    }

    public function inventoryOut()
    {
        $inventoryType = $this->request->getGet('inventory_type');
        $subInventoryType = $this->request->getGet('sub_inventory_type');
        $inventoryTypeModel = new InventoryTypeModel();
        $data['inventory_type_parse'] = $inventoryTypeModel->find($inventoryType);

        $subInventoryTypeModel = new SubInventoryTypeModel();
        $data['sub_inventory_type_parse'] = $subInventoryTypeModel->find($subInventoryType);
        // This method will handle the /inventory_out route
        if (!session()->has('user_id')) {
            return redirect()->to(base_url('login'));
        }
        return view('inventory/inventory_out', $data);
    }

    public function inventoryReturn()
    {
        $inventoryType = $this->request->getGet('inventory_type');
        $subInventoryType = $this->request->getGet('sub_inventory_type');
        $inventoryTypeModel = new InventoryTypeModel();
        $data['inventory_type_parse'] = $inventoryTypeModel->find($inventoryType);

        $subInventoryTypeModel = new SubInventoryTypeModel();
        $data['sub_inventory_type_parse'] = $subInventoryTypeModel->find($subInventoryType);
        // This method will handle the /inventory_out route
        if (!session()->has('user_id')) {
            return redirect()->to(base_url('login'));
        }
        return view('inventory/inventory_return', $data);
    }

    public function inventoryOutPos()
    {
        $inventoryType = $this->request->getGet('inventory_type');
        $subInventoryType = $this->request->getGet('sub_inventory_type');
        $inventoryTypeModel = new InventoryTypeModel();
        $data['inventory_type_parse'] = $inventoryTypeModel->find($inventoryType);

        $subInventoryTypeModel = new SubInventoryTypeModel();
        $data['sub_inventory_type_parse'] = $subInventoryTypeModel->find($subInventoryType);
        // This method will handle the /inventory_out route
        if (!session()->has('user_id')) {
            return redirect()->to(base_url('login'));
        }
        return view('inventory/inventory_out_pos', $data);
    }

    public function inventoryType()
    {
        // This method will handle the /inventory_type route
        if (!session()->has('user_id')) {
            return redirect()->to(base_url('login'));
        }
        return view('inventory/inventory_type');
    }

    public function doUpload()
    {
        $file = $this->request->getFile('file');

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/icons/', $newName);

            return $this->response->setJSON([
                'success' => true,
                'filename' => $newName
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'error' => $file->getErrorString()
        ]);
    }

    public function inventoryReturnHistory(){
        if (!session()->has('user_id')) {
            return redirect()->to(base_url('login'));
        }
        
        $inventoryHistoryReturnModel = new InventoryHistoryReturnModel();
        $subInventoryType = $this->request->getGet('sub_inventory_type');
        
        // Get the current page number from the URL, default to 1
        $currentPage = $this->request->getVar('page_inventory_history_return') ?: 1;
        $search = $this->request->getGet('search');
        $inventory_type = $this->request->getGet('inventory_type');
        $orderBy = $this->request->getGet('orderby') ?? 'inventory_history.name';
        $orderDir = $this->request->getGet('orderdir') ?? 'asc';
        $perPage = $this->request->getGet('number_per_page') ?? 10;
        
        // Fetch paginated inventory data
        $inventoryHistoryReturnModel->select('
            inventory_history_return.*,
            inventory_history.id,
            inventory_history.supplier_id,
            inventory_history.distributor_id,
            inventory_history.inventory_id,
            inventory_history.name,
            inventory_history.description,
            inventory_history.remarks,
            inventory_history.price,
            inventory_supplier.name as supplier_name,
            inventory_type.name as inventory_type_name,
            sub_inventory_type.name as sub_inventory_type_name,
            inventory.inventory_type,
            inventory.sub_inventory_type'
        )
            ->join('inventory_history', 'inventory_history.id = inventory_history_return.inventory_out_id')
            ->join('inventory_supplier', 'inventory_history.supplier_id = inventory_supplier.id','left')
            ->join('distributor', 'inventory_history.distributor_id = distributor.id','left')
            ->join('inventory', 'inventory_history.inventory_id = inventory.id')
            ->join('inventory_type', 'inventory.inventory_type = inventory_type.id')
            ->join('sub_inventory_type', 'inventory.sub_inventory_type = sub_inventory_type.id');

        if (!empty($search)) {
            $inventoryHistoryReturnModel->groupStart()
                ->like('inventory_history.name', $search)
                ->orLike('inventory_history.description', $search)
                ->orLike('inventory_history.remarks', $search)
                ->groupEnd();
        }

        if (!empty($inventory_type)) {
            $inventoryHistoryReturnModel->groupStart()
                ->like('inventory_type.id', $inventory_type)
                ->groupEnd();
        }

        if (!empty($subInventoryType)) {
            $inventoryHistoryReturnModel->groupStart()
                ->like('inventory.sub_inventory_type', $subInventoryType)
                ->groupEnd();
        }


        $inventoryHistoryReturnModel->orderBy($orderBy, $orderDir);
        $data['inventory_history_return'] = $inventoryHistoryReturnModel->paginate($perPage, 'inventory_history_return');
        $data['pager'] = $inventoryHistoryReturnModel->pager;
        $data['currentPage'] = $currentPage;
        $data['totalItems'] = $inventoryHistoryReturnModel->pager->getTotal('inventory_history_return');
        $data['totalPages'] = $data['totalItems'] ? ceil($data['totalItems'] / $perPage) : 1;
        $inventoryTypeModel = new InventoryTypeModel();
        $data['inventory_type_data'] = $inventoryTypeModel->findAll();        
        $data['search'] = $search;
        $data['orderby'] = $orderBy;
        $data['orderdir'] = $orderDir;
        $data['inventory_type'] = $inventory_type;
        $data['number_per_page'] = $perPage;
        
        $inventoryTypeModel = new InventoryTypeModel();
        $data['inventory_type_parse'] = $inventoryTypeModel->find($inventory_type);

        $subInventoryTypeModel = new SubInventoryTypeModel();
        $data['sub_inventory_type_parse'] = $subInventoryTypeModel->find($subInventoryType);
        return view('inventory_return/index', $data);        
    }
}
