<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\InventoryHistoryGroupModel;

class InventoryHistoryGroupController extends Controller
{
    public function updateGroup($id)
    {
        try {
            $json = $this->request->getJSON(true); // decode JSON into array

            $data = [
                'name'         => $json['name'],
                'address'      => $json['address'],
                'ref_po_number' => $json['ref_po_number'],
            ];

            $db = \Config\Database::connect();
            $db->table('inventory_history_group')
                ->where('dr_number', $json['dr_number']) // update all rows with same dr_number
                ->update($data);

            return $this->response->setJSON([
                'success'      => true,
                'updated_data' => $data,
                'dr_number'    => $json['dr_number']
            ]);
        } catch (\Throwable $th) {
            return $this->response->setStatusCode(500)->setJSON([
                'error' => $th->getMessage()
            ]);
        }
    }
}
