<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\InventoryHistoryModel;
use App\Models\InventoryTypeModel;
use App\Models\SubInventoryTypeModel;

class InventoryHistoryController extends Controller
{
    public function index()
    {
        $inventoryHistoryModel = new InventoryHistoryModel();
        
        
        // Get the current page number from the URL, default to 1
        $currentPage = $this->request->getVar('page_inventory_history') ?: 1;
        $search = $this->request->getGet('search');
        $inventory_type = $this->request->getGet('inventory_type');
        $orderBy = $this->request->getGet('orderby') ?? 'inventory_history.name';
        $orderDir = $this->request->getGet('orderdir') ?? 'asc';
        $perPage = $this->request->getGet('number_per_page') ?? 10;
        $inOutFilter = $this->request->getGet('in_out') ?? '';

        // Fetch paginated inventory data
        $inventoryHistoryModel->select('inventory_history.*, inventory_supplier.name as supplier_name, inventory_type.name as inventory_type_name')
            ->join('inventory_supplier', 'inventory_history.supplier_id = inventory_supplier.id','left')
            ->join('inventory', 'inventory_history.inventory_id = inventory.id')
            ->join('inventory_type', 'inventory.inventory_type = inventory_type.id');

        // Apply search
        if (!empty($search)) {
            $inventoryHistoryModel->groupStart()
                ->like('inventory_history.name', $search)
                ->orLike('inventory_history.description', $search)
                ->orLike('inventory_history.in_out', $search)
                ->orLike('inventory_history.remarks', $search)
                ->groupEnd();
        }

        if (!empty($inventory_type)) {
            $inventoryHistoryModel->groupStart()
                ->like('inventory_type.id', $inventory_type)
                ->groupEnd();
        }

        if (!empty($inOutFilter)) {
            $inventoryHistoryModel->groupStart()
                ->like('inventory_history.in_out', $inOutFilter)
                ->groupEnd();
        }

        // Apply ordering
        $inventoryHistoryModel->orderBy($orderBy, $orderDir);

        // Get paginated results
        $data['inventory_history'] = $inventoryHistoryModel->paginate($perPage, 'inventory_history');


        // Get pager instance to render pagination links
        $data['pager'] = $inventoryHistoryModel->pager;

        // Current page
        $data['currentPage'] = $currentPage;

        // Total items (count of all inventory records)
        // $data['totalItems'] = $inventoryHistoryModel->countAllResults();

        // Total pages based on the per page count
        // $data['totalPages'] = ceil($data['totalItems'] / $perPage);
        $data['totalItems'] = $inventoryHistoryModel->pager->getTotal('inventory_history');
        $data['totalPages'] = $data['totalItems'] ? ceil($data['totalItems'] / $perPage) : 1;
        $inventoryTypeModel = new InventoryTypeModel();
        $data['inventory_type_data'] = $inventoryTypeModel->findAll();
        
        $data['search'] = $search;
        $data['orderby'] = $orderBy;
        $data['orderdir'] = $orderDir;
        $data['inventory_type'] = $inventory_type;
        $data['number_per_page'] = $perPage;
        $data['in_out'] = $inOutFilter;
        if (!session()->has('user_id')) {
            return redirect()->to(base_url('login'));
        }
        return view('inventory_history/index', $data);

    }





    public function filter()
    {
        $inventoryHistoryModel = new InventoryHistoryModel();
        $subInventoryType = $this->request->getGet('sub_inventory_type');
        
        // Get the current page number from the URL, default to 1
        $currentPage = $this->request->getVar('page_inventory_history') ?: 1;
        $search = $this->request->getGet('search');
        $inventory_type = $this->request->getGet('inventory_type');
        $orderBy = $this->request->getGet('orderby') ?? 'inventory_history.name';
        $orderDir = $this->request->getGet('orderdir') ?? 'asc';
        $perPage = $this->request->getGet('number_per_page') ?? 10;
        $inOutFilter = $this->request->getGet('in_out') ?? '';

        // Fetch paginated inventory data
        $inventoryHistoryModel->select('inventory_history.*, inventory_supplier.name as supplier_name, distributor.name as distributor_name, inventory_type.name as inventory_type_name, sub_inventory_type.name as sub_inventory_type_name, inventory.inventory_type, inventory.sub_inventory_type')
            ->join('inventory_supplier', 'inventory_history.supplier_id = inventory_supplier.id','left')
            ->join('distributor', 'inventory_history.distributor_id = distributor.id','left')
            ->join('inventory', 'inventory_history.inventory_id = inventory.id')
            ->join('inventory_type', 'inventory.inventory_type = inventory_type.id')
            ->join('sub_inventory_type', 'inventory.sub_inventory_type = sub_inventory_type.id');

        // Apply search
        if (!empty($search)) {
            $inventoryHistoryModel->groupStart()
                ->like('inventory_history.name', $search)
                ->orLike('inventory_history.description', $search)
                ->orLike('inventory_history.in_out', $search)
                ->orLike('inventory_history.remarks', $search)
                ->groupEnd();
        }

        if (!empty($inventory_type)) {
            $inventoryHistoryModel->groupStart()
                ->like('inventory_type.id', $inventory_type)
                ->groupEnd();
        }

        if (!empty($subInventoryType)) {
            $inventoryHistoryModel->groupStart()
                ->like('inventory.sub_inventory_type', $subInventoryType)
                ->groupEnd();
        }

        if (!empty($inOutFilter)) {
            $inventoryHistoryModel->groupStart()
                ->like('inventory_history.in_out', $inOutFilter)
                ->groupEnd();
        }

        // Apply ordering
        $inventoryHistoryModel->orderBy($orderBy, $orderDir);

        // Get paginated results
        $data['inventory_history'] = $inventoryHistoryModel->paginate($perPage, 'inventory_history');


        // Get pager instance to render pagination links
        $data['pager'] = $inventoryHistoryModel->pager;

        // Current page
        $data['currentPage'] = $currentPage;

        // Total items (count of all inventory records)
        // $data['totalItems'] = $inventoryHistoryModel->countAllResults();

        // Total pages based on the per page count
        // $data['totalPages'] = ceil($data['totalItems'] / $perPage);
        $data['totalItems'] = $inventoryHistoryModel->pager->getTotal('inventory_history');
        $data['totalPages'] = $data['totalItems'] ? ceil($data['totalItems'] / $perPage) : 1;
        $inventoryTypeModel = new InventoryTypeModel();
        $data['inventory_type_data'] = $inventoryTypeModel->findAll();
        
        $data['search'] = $search;
        $data['orderby'] = $orderBy;
        $data['orderdir'] = $orderDir;
        $data['inventory_type'] = $inventory_type;
        $data['number_per_page'] = $perPage;
        $data['in_out'] = $inOutFilter;
        if (!session()->has('user_id')) {
            return redirect()->to(base_url('login'));
        }

        
        $inventoryTypeModel = new InventoryTypeModel();
        $data['inventory_type_parse'] = $inventoryTypeModel->find($inventory_type);

        $subInventoryTypeModel = new SubInventoryTypeModel();
        $data['sub_inventory_type_parse'] = $subInventoryTypeModel->find($subInventoryType);
        return view('inventory_history/filter', $data);
    }

    
}
