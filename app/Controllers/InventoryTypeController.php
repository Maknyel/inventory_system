<?php namespace App\Controllers;

use App\Models\InventoryTypeModel;
use CodeIgniter\RESTful\ResourceController;

class InventoryTypeController extends BaseController
{
    protected $inventoryTypeModel;

    public function __construct()
    {
        $this->inventoryTypeModel = new InventoryTypeModel();
    }

    public function index()
    {
        $perPage = 10;
        $currentPage = $this->request->getVar('page_inventory_type') ?: 1;
        $data['types'] = $this->inventoryTypeModel->paginate($perPage,'inventory_type');
        $data['pager'] = $this->inventoryTypeModel->pager;
        $data['currentPage'] = $currentPage;
        // $data['totalItems'] = $this->inventoryTypeModel1->countAllResults();
        // $data['totalPages'] = ceil($data['totalItems'] / $perPage);
        $data['totalItems'] = $this->inventoryTypeModel->pager->getTotal('inventory_type');
        $data['totalPages'] = $data['totalItems'] ? ceil($data['totalItems'] / $perPage) : 1;
        if (!session()->has('user_id')) {
            return redirect()->to(base_url('login'));
        }
        return view('inventory_type/index', $data);
    }

    // API: fetch single item
    public function apiShow($id)
    {
        return $this->response->setJSON($this->inventoryTypeModel->find($id));
    }

    // API: store new item
    public function apiStore()
    {
        $data = $this->request->getJSON(true);
        $this->inventoryTypeModel->insert($data);
        return $this->response->setJSON(['status' => 'created']);
    }

    // API: update item
    public function apiUpdate($id)
    {
        $data = $this->request->getJSON(true);
        $this->inventoryTypeModel->update($id, $data);
        return $this->response->setJSON(['status' => 'updated']);
    }

    // API: delete item
    public function apiDelete($id)
    {
        $this->inventoryTypeModel->delete($id);
        return $this->response->setJSON(['status' => 'deleted']);
    }


    public function export()
    {
        $inventoryTypes = $this->inventoryTypeModel->findAll();

        // Set the CSV file name
        $filename = 'inventory_types_' . date('Y-m-d_H-i-s') . '.csv';

        // Open the output stream to the browser
        $file = fopen('php://output', 'w');

        // Set the header row for CSV
        fputcsv($file, ['ID', 'Name', 'Description', 'Created At', 'Updated At']);

        // Write data rows to CSV
        foreach ($inventoryTypes as $inventoryType) {
            fputcsv($file, [
                $inventoryType['id'],
                $inventoryType['name'],
                $inventoryType['description'],
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
