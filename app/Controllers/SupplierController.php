<?php
namespace App\Controllers;

use CodeIgniter\Controller;

use App\Models\InventorySupplierModel;
use CodeIgniter\HTTP\ResponseInterface;

class SupplierController extends Controller
{
    public function index()
    {
        $perPage = 10; // Number of records per page

        // Get paginated suppliers
        $data['suppliers'] = $this->supplierModel->paginate($perPage, 'supplier');

        // Pager instance
        $pager = $this->supplierModel->pager;

        // Current page (default to 1 if null)
        // $currentPage = $pager->getCurrentPage() ?? 1;
        $currentPage = $this->request->getVar('page_supplier') ?: 1;

        // Total items and pages
        // $totalItems = $this->supplierModel->countAllResults();
        // $totalPages = ceil($totalItems / $perPage);
        $data['totalItems'] = $this->supplierModel->pager->getTotal('supplier');
        $data['totalPages'] = $data['totalItems'] ? ceil($data['totalItems'] / $perPage) : 1;

        // Pass data to view
        $data['pager'] = $pager;
        $data['currentPage'] = $currentPage;
        // $data['totalItems'] = $totalItems;
        // $data['totalPages'] = $totalPages;
        if (!session()->has('user_id')) {
            return redirect()->to(base_url('login'));
        }
        return view('supplier/index', $data);
    }

    public function getSupplierList()
    {
        $model = new \App\Models\InventorySupplierModel();
        $data = $model->select('id, name')->findAll();

        return $this->response->setJSON($data);
    }

    public function getAllSUpplier()
    {
        $data['suppliers'] = $this->supplierModel->findAll();
        echo json_encode($data);
    }

    protected $supplierModel;

    public function __construct()
    {
        $this->supplierModel = new InventorySupplierModel();
    }


    // Create
    public function create()
    {
        return view('suppliers/create');
    }

    // public function store()
    // {
    //     $this->supplierModel->save([
    //         'name' => $this->request->getPost('name'),
    //         'description' => $this->request->getPost('description')
    //     ]);

    //     return redirect()->to('/inventorysupplier');
    // }

    // // Edit
    // public function edit($id)
    // {
    //     $data['supplier'] = $this->supplierModel->find($id);
    //     return view('suppliers/edit', $data);
    // }

    // public function update($id)
    // {
    //     $this->supplierModel->update($id, [
    //         'name' => $this->request->getPost('name'),
    //         'description' => $this->request->getPost('description')
    //     ]);

    //     return redirect()->to('/inventorysupplier');
    // }

    // // Delete
    // public function delete($id)
    // {
    //     $this->supplierModel->delete($id);
    //     return redirect()->to('/inventorysupplier');
    // }




    public function api($id)
    {
        return $this->response->setJSON($this->supplierModel->find($id));
    }

    public function apiStore()
    {
        $data = $this->request->getJSON(true);
        $this->supplierModel->insert($data);
        return $this->response->setJSON(['status' => 'created']);
    }

    public function apiUpdate($id)
    {
        $data = $this->request->getJSON(true);
        $this->supplierModel->update($id, $data);
        return $this->response->setJSON(['status' => 'updated']);
    }

    public function apiDelete($id)
    {
        $this->supplierModel->delete($id);
        return $this->response->setJSON(['status' => 'deleted']);
    }

    public function export()
    {
        // Fetch all suppliers from the database
        $suppliers = $this->supplierModel->findAll();

        // Set the filename for the export
        $filename = 'suppliers_' . date('Y-m-d_H-i-s') . '.csv';

        // Open the output stream to the browser
        $file = fopen('php://output', 'w');

        // Set the CSV header
        fputcsv($file, ['ID', 'Name', 'Description', 'Created At', 'Updated At']);

        // Write each supplier row to the CSV file
        foreach ($suppliers as $supplier) {
            fputcsv($file, [
                $supplier['id'],
                $supplier['name'],
                $supplier['description'],
                $supplier['created_at'],
                $supplier['updated_at']
            ]);
        }

        // Set the headers to download the file
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Close the file after writing
        fclose($file);

        // Prevent further rendering
        exit;
    }
}
