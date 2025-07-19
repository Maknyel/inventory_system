<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\InventoryHistoryModel;
use App\Models\InventoryTypeModel;
use App\Models\SubInventoryTypeModel;
use App\Models\InventoryHistoryGroupModel;

class InventoryHistoryController extends Controller
{
    public function index()
    {
        if (!session()->has('user_id')) {
            return redirect()->to(base_url('login'));
        }
        $data = [];
        return view('inventory_history/index', $data);

    }

    public function getInventoryHistoryApi()
    {
        $group = new \App\Models\InventoryHistoryGroupModel();
        $model = new \App\Models\InventoryHistoryModel();

        // Apply filters from GET parameters
        $search = $this->request->getGet('search');
        $search_dr = $this->request->getGet('search_dr');
        $in_out = $this->request->getGet('in_out');
        $number_per_page = $this->request->getGet('number_per_page') ?? 10;
        $page = $this->request->getGet('page') ?? 1;
        $start_date = $this->request->getGet('start_date');
        $end_date = $this->request->getGet('end_date');
        $purpose = $this->request->getGet('purpose');
        

        $query = $group->select('*');
        if ($search_dr) {
            if (!empty($search_dr)) {
            $query->groupStart()
                    ->like('dr_number', $search_dr)
                    ->groupEnd();
            }
        }


        $query->groupBy('dr_number');
        $query->orderBy('dr_number');


        // Pagination
        $data = $query->paginate($number_per_page, 'inventory_history', $page);
        $pager = \Config\Services::pager();


        $fullData = [];

        // Fetch ungrouped inventory history
        if (empty($search_dr)) {
            $builder = $model->select('
                    inventory_history.*,
                    inventory_supplier.name as supplier_name,
                    distributor.name as distributor_name,
                    inventory_type.name as inventory_type_name,
                    sub_inventory_type.name as sub_inventory_type_name,
                    inventory.inventory_type,
                    inventory.sub_inventory_type
                ')
                ->join('inventory_supplier', 'inventory_history.supplier_id = inventory_supplier.id', 'left')
                ->join('distributor', 'inventory_history.distributor_id = distributor.id', 'left')
                ->join('inventory', 'inventory_history.inventory_id = inventory.id', 'left')
                ->join('inventory_type', 'inventory.inventory_type = inventory_type.id', 'left')
                ->join('sub_inventory_type', 'inventory.sub_inventory_type = sub_inventory_type.id', 'left')
                ->join('inventory_history_group', 'inventory_history.id = inventory_history_group.inventory_history_id', 'left')
                ->where('inventory_history_group.inventory_history_id IS NULL');
                
                
                if ($start_date && $end_date) {
                    $builder->where('inventory_history.created_at >=', $start_date.' 00:00:00')
                            ->where('inventory_history.created_at <=', $end_date.' 23:59:59');
                }

                if($purpose){
                    if($purpose == 'For Own Consumption'){
                        $builder->where('inventory_history.customer_own_distribution', 'For Own Consumption');
                        
                    }else{
                        $builder->where('inventory_history.distributor_id', $purpose);
                        
                    }
                }

                // 🔍 Run query
                $ungroupedItems = $builder->findAll();

            // Apply optional filters
            if ($search) {
                $ungroupedItems = array_filter($ungroupedItems, function ($item) use ($search) {
                    return stripos($item['name'], $search) !== false;
                });
            }

            if ($in_out) {
                $ungroupedItems = array_filter($ungroupedItems, function ($item) use ($in_out) {
                    return $item['in_out'] === $in_out;
                });
            }

            // Group ungrouped items under a fake dr_number
            if (!empty($ungroupedItems)) {
                $fullData[] = [
                    'dr_number' => 'UNGROUPED',
                    'sub' => array_values($ungroupedItems),
                ];
            }
        }

        foreach ($data as $groupItem) {
            // Step 2: Fetch sub records (detailed inventory history per dr_number)
            $builder = $model->select('
                    inventory_history.*,
                    inventory_supplier.name as supplier_name,
                    distributor.name as distributor_name,
                    inventory_type.name as inventory_type_name,
                    sub_inventory_type.name as sub_inventory_type_name,
                    inventory.inventory_type,
                    inventory.sub_inventory_type
                ')
                ->join('inventory_supplier', 'inventory_history.supplier_id = inventory_supplier.id', 'left')
                ->join('distributor', 'inventory_history.distributor_id = distributor.id', 'left')
                ->join('inventory', 'inventory_history.inventory_id = inventory.id', 'left')
                ->join('inventory_type', 'inventory.inventory_type = inventory_type.id', 'left')
                ->join('sub_inventory_type', 'inventory.sub_inventory_type = sub_inventory_type.id', 'left')
                ->join('inventory_history_group', 'inventory_history.id = inventory_history_group.inventory_history_id')
                ->where('inventory_history_group.dr_number', $groupItem['dr_number']);
                
                
                if ($start_date && $end_date) {
                    $builder->where('inventory_history.created_at >=', $start_date.' 00:00:00')
                            ->where('inventory_history.created_at <=', $end_date.' 23:59:59');
                }

                if($purpose){
                    if($purpose == 'For Own Consumption'){
                        $builder->where('inventory_history.customer_own_distribution', 'For Own Consumption');
                        
                    }
                }

                // 🔍 Run query
                $subQuery = $builder->findAll();

            // Optional filters if needed:
            if ($search) {
                $subQuery = array_filter($subQuery, function ($item) use ($search) {
                    return stripos($item['name'], $search) !== false;
                });
            }

            if ($in_out) {
                $subQuery = array_filter($subQuery, function ($item) use ($in_out) {
                    return $item['in_out'] === $in_out;
                });
            }

            // Add the sub data to the group item
            $groupItem['sub'] = array_values($subQuery); // Ensure it's a reindexed array

            $fullData[] = $groupItem;
        }

        


        return $this->response->setJSON([
            'data' => $fullData,
            'pagination' => [
                'total' => $pager->getTotal('inventory_history'),
                'current_page' => $pager->getCurrentPage('inventory_history'),
                'per_page' => $pager->getPerPage('inventory_history'),
                'total_pages' => $pager->getPageCount('inventory_history'),
            ]
        ]);
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
