<?php

namespace App\Controllers;

use Dompdf\Dompdf;
use App\Models\InventoryHistoryGroupModel;
use App\Models\InventoryHistoryModel;
use App\Models\InventoryTypeModel;
use App\Models\SubInventoryTypeModel;

class PrintController extends BaseController
{
    // purchase_order
    // 
    // 

    public function purchase_order($id)
    {
        
        $model = new \App\Models\InventoryHistoryGroupModel();
        $builder = $model->select('*')
            ->where('id', $id)
            ->first();

        $modelInvHistoryGroup = new \App\Models\InventoryHistoryGroupModel();
        $dataParse = $modelInvHistoryGroup
        ->select('
            inventory_history_group.id,
            inventory_history_group.dr_number,
            inventory_history_group.inventory_history_id,
            
            inventory_history.id,
            inventory_history.name,
            inventory_history.description,
            inventory_history.price,
            inventory_history.quantity,
            inventory.id,
            inventory.unit
        ')
        ->join('inventory_history', 'inventory_history.id = inventory_history_group.inventory_history_id')
        ->join('inventory', 'inventory.id = inventory_history.inventory_id')
        ->where('inventory_history_group.dr_number', $builder['dr_number'])
        ->findAll();

        $data = [
            'dr_number' => $builder['dr_number'],
            'discount' => $builder['discount'],
            'supplier' => $builder['name'],
            'attention' => $builder['address'],
            'ref_po'    => $builder['ref_po_number'],
            'orderDate' => date('Y-m-d', strtotime($builder['created_at'])),
            'cart' => $dataParse,
            'totalAmount' => $builder['discount_amount'] + $builder['grand_total_amount'],
            'discountComputation' => $builder['discount_amount'],
            'grandTotalAmount' => $builder['grand_total_amount'],
        ];

        $html = view('print/purchase_order', $data);

        // Setup Dompdf
        $dompdf = new Dompdf();
        $options = $dompdf->getOptions();
        $options->set('isRemoteEnabled', true); // allows images
        $dompdf->setOptions($options);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $this->response->setHeader('Content-Type', 'application/pdf')
            ->setBody($dompdf->output());
    }

    public function form_customer($id)
    {
        
        $model = new \App\Models\InventoryHistoryGroupModel();
        $builder = $model->select('*')
            ->where('id', $id)
            ->first();

        $modelInvHistoryGroup = new \App\Models\InventoryHistoryGroupModel();
        $dataParse = $modelInvHistoryGroup
        ->select('
            inventory_history_group.id,
            inventory_history_group.dr_number,
            inventory_history_group.inventory_history_id,
            
            inventory_history.id,
            inventory_history.name,
            inventory_history.description,
            inventory_history.price,
            inventory_history.quantity,
            inventory.id,
            inventory.unit
        ')
        ->join('inventory_history', 'inventory_history.id = inventory_history_group.inventory_history_id')
        ->join('inventory', 'inventory.id = inventory_history.inventory_id')
        ->where('inventory_history_group.dr_number', $builder['dr_number'])
        ->findAll();

        $data = [
            'dr_number' => $builder['dr_number'],
            'discount' => $builder['discount'],
            'supplier' => $builder['name'],
            'attention' => $builder['address'],
            'ref_po'    => $builder['ref_po_number'],
            'orderDate' => date('Y-m-d', strtotime($builder['created_at'])),
            'cart' => $dataParse,
            'totalAmount' => $builder['discount_amount'] + $builder['grand_total_amount'],
            'discountComputation' => $builder['discount_amount'],
            'grandTotalAmount' => $builder['grand_total_amount'],
        ];

        $html = view('print/receipt_customer', $data);

        // Setup Dompdf
        $dompdf = new Dompdf();
        $options = $dompdf->getOptions();
        $options->set('isRemoteEnabled', true); // allows images
        $dompdf->setOptions($options);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $this->response->setHeader('Content-Type', 'application/pdf')
            ->setBody($dompdf->output());
    }

    public function form_distributor($id)
    {
        
        $model = new \App\Models\InventoryHistoryGroupModel();
        $builder = $model->select('*')
            ->where('id', $id)
            ->first();

        $modelInvHistoryGroup = new \App\Models\InventoryHistoryGroupModel();
        $dataParse = $modelInvHistoryGroup
        ->select('
            inventory_history_group.id,
            inventory_history_group.dr_number,
            inventory_history_group.inventory_history_id,
            
            inventory_history.id,
            inventory_history.name,
            inventory_history.description,
            inventory_history.price,
            inventory_history.quantity,
            inventory.id,
            inventory.unit
        ')
        ->join('inventory_history', 'inventory_history.id = inventory_history_group.inventory_history_id')
        ->join('inventory', 'inventory.id = inventory_history.inventory_id')
        ->where('inventory_history_group.dr_number', $builder['dr_number'])
        ->findAll();

        $data = [
            'dr_number' => $builder['dr_number'],
            'supplier' => $builder['name'],
            'attention' => $builder['address'],
            'ref_po'    => $builder['ref_po_number'],
            'orderDate' => date('Y-m-d', strtotime($builder['created_at'])),
            'cart' => $dataParse,
            'totalAmount' => $builder['discount_amount'] + $builder['grand_total_amount'],
            'discountComputation' => $builder['discount_amount'],
            'grandTotalAmount' => $builder['grand_total_amount'],
        ];

        $html = view('print/receipt_distributors', $data);

        // Setup Dompdf
        $dompdf = new Dompdf();
        $options = $dompdf->getOptions();
        $options->set('isRemoteEnabled', true); // allows images
        $dompdf->setOptions($options);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $this->response->setHeader('Content-Type', 'application/pdf')
            ->setBody($dompdf->output());
    }
}
