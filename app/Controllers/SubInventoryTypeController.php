<?php namespace App\Controllers;

use App\Models\SubInventoryTypeModel;
use App\Models\InventoryTypeModel;
use CodeIgniter\RESTful\ResourceController;

class SubInventoryTypeController extends BaseController
{
    protected $subInventoryTypeModel;
    protected $inventoryTypeModel;

    public function __construct()
    {
        $this->subInventoryTypeModel = new SubInventoryTypeModel();
        $this->inventoryTypeModel = new InventoryTypeModel();
    }

    public function index()
    {
        $currentPage = $this->request->getVar('page_sub_inventory_type') ?: 1;
        $perPage = 10;
        // Perform the join between sub_inventory_type and inventory_type
$query = $this->subInventoryTypeModel
    ->select('sub_inventory_type.*, inventory_type.name as inventory_type_name') // Select fields, including the inventory_type name
    ->join('inventory_type', 'sub_inventory_type.inventory_type_id = inventory_type.id', 'left') // Join the tables
    ->paginate($perPage, 'sub_inventory_type', $currentPage); // Paginate the results

    // Get the total count for pagination
    $data['types'] = $query;
    $data['pager'] = $this->subInventoryTypeModel->pager;
    $data['currentPage'] = $currentPage;
    $data['totalItems'] = $this->subInventoryTypeModel->pager->getTotal('sub_inventory_type');
    $data['totalPages'] = $data['totalItems'] ? ceil($data['totalItems'] / $perPage) : 1;
        $data['inventory_type'] = $this->inventoryTypeModel->findAll();
        if (!session()->has('user_id')) {
            return redirect()->to(base_url('login'));
        }
        return view('sub_inventory_type/index', $data);
    }

    public function apiShow($id)
    {
        return $this->response->setJSON($this->subInventoryTypeModel->find($id));
    }

    public function apiStore()
    {
        $data = $this->request->getJSON(true);
        $this->subInventoryTypeModel->insert($data);
        return $this->response->setJSON(['status' => 'created']);
    }

    // API: update item
    public function apiUpdate($id)
    {
        $data = $this->request->getJSON(true);
        $this->subInventoryTypeModel->update($id, $data);
        return $this->response->setJSON(['status' => 'updated']);
    }

    // API: delete item
    public function apiDelete($id)
    {
        $this->subInventoryTypeModel->delete($id);
        return $this->response->setJSON(['status' => 'deleted']);
    }


    public function export()
    {
        $inventoryTypes = $this->subInventoryTypeModel->findAll();

        $filename = 'sub_inventory_types_' . date('Y-m-d_H-i-s') . '.csv';

        // Open the output stream to the browser
        $file = fopen('php://output', 'w');

        // Set the header row for CSV
        fputcsv($file, ['ID', 'Name', 'Description', 'Inventory Type','Created At', 'Updated At']);

        // Write data rows to CSV
        foreach ($inventoryTypes as $inventoryType) {
            fputcsv($file, [
                $inventoryType['id'],
                $inventoryType['name'],
                $inventoryType['description'],
                $inventoryType['inventory_type_id'],
                $inventoryType['created_at'],
                $inventoryType['updated_at']
            ]);
        }

        // Set the appropriate headers for the download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Close the file after output
        fclose($file);

        // Prevent further rendering
        exit;
    }
}
