<?php
namespace App\Controllers;

use CodeIgniter\Controller;

use App\Models\DistributorModel;
use CodeIgniter\HTTP\ResponseInterface;

class DistributorController extends Controller
{
    public function index()
    {
        $perPage = 10; // Number of records per page

        // Get paginated distributor
        $data['distributors'] = $this->distributorModel->paginate($perPage, 'distributor');

        // Pager instance
        $pager = $this->distributorModel->pager;

        // Current page (default to 1 if null)
        // $currentPage = $pager->getCurrentPage() ?? 1;
        $currentPage = $this->request->getVar('page_distributor') ?: 1;

        // Total items and pages
        // $totalItems = $this->distributorModel->countAllResults();
        // $totalPages = ceil($totalItems / $perPage);
        $data['totalItems'] = $this->distributorModel->pager->getTotal('distributor');
        $data['totalPages'] = $data['totalItems'] ? ceil($data['totalItems'] / $perPage) : 1;

        // Pass data to view
        $data['pager'] = $pager;
        $data['currentPage'] = $currentPage;
        // $data['totalItems'] = $totalItems;
        // $data['totalPages'] = $totalPages;
        if (!session()->has('user_id')) {
            return redirect()->to(base_url('login'));
        }
        return view('distributor/index', $data);
    }

    public function getDistributorList()
    {
        $model = new \App\Models\DistributorModel();
        $data = $model->select('id, name, type')->findAll();

        return $this->response->setJSON($data);
    }

    public function getAllDistributor()
    {
        $data['distributor'] = $this->distributorModel->findAll();
        echo json_encode($data);
    }

    protected $distributorModel;

    public function __construct()
    {
        $this->distributorModel = new DistributorModel();
    }


    public function api($id)
    {
        return $this->response->setJSON($this->distributorModel->find($id));
    }

    public function apiStore()
    {
        $data = $this->request->getJSON(true);
        $this->distributorModel->insert($data);
        return $this->response->setJSON(['status' => 'created']);
    }

    public function apiUpdate($id)
    {
        $data = $this->request->getJSON(true);
        $this->distributorModel->update($id, $data);
        return $this->response->setJSON(['status' => 'updated']);
    }

    public function apiDelete($id)
    {
        $this->distributorModel->delete($id);
        return $this->response->setJSON(['status' => 'deleted']);
    }

    public function export()
    {
        // Fetch all distributor from the database
        $distributor = $this->distributorModel->findAll();

        // Set the filename for the export
        $filename = 'distributor_' . date('Y-m-d_H-i-s') . '.csv';

        // Open the output stream to the browser
        $file = fopen('php://output', 'w');

        // Set the CSV header
        fputcsv($file, ['ID', 'Name', 'Description', 'Type', 'Created At', 'Updated At']);

        // Write each distributor row to the CSV file
        foreach ($distributor as $distributor) {
            fputcsv($file, [
                $distributor['id'],
                $distributor['name'],
                $distributor['description'],
                $distributor['type'],
                $distributor['created_at'],
                $distributor['updated_at']
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
