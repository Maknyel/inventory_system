<?php

namespace App\Controllers;

use App\Models\ExcessInvModel;
use CodeIgniter\RESTful\ResourceController;

class ExcessStockApi extends ResourceController
{
    protected $modelName = 'App\Models\ExcessInvModel';
    protected $format = 'json';

    public function __construct()
    {
        // Connect to database and assign to $this->db
        $this->db = \Config\Database::connect();
    }
    // Get one excess stock entry by id
    public function show($id = null)
    {
        $item = $this->model->find($id);
        if (!$item) {
            return $this->failNotFound('Excess stock not found');
        }
        return $this->respond($item);
    }

    // Store new excess stock
    public function create()
    {
        $data = $this->request->getPost();

        if (!$this->validate([
            'inventory_id' => 'required|integer',
            'quantity'     => 'required|numeric',
        ])) {
            return $this->fail($this->validator->getErrors());
        }

        // Get current user
        $user = current_user();
        $username = $user['name'] ?? 'Unknown';

        // Get inventory name from DB
        $inventory = $this->db->table('inventory')->where('id', $data['inventory_id'])->get()->getRow();
        $inventoryName = $inventory ? $inventory->name : 'Unknown Item';
        $reason = trim($data['reason']);
        // Build history message
        $history = "Added by: {$username}, Inventory: {$inventoryName}, Quantity: {$data['quantity']}, Reason: {$reason}";

        // Insert into model
        $this->model->insert([
            'inventory_id' => $data['inventory_id'],
            'quantity'     => $data['quantity'],
            'history'      => $history,
        ]);

        return $this->respondCreated(['message' => 'Excess stock created']);
    }

    // Update excess stock by id
    public function update($id = null)
    {
        $item = $this->model->find($id);
        if (!$item) {
            return $this->failNotFound('Excess stock not found');
        }

        $data = $this->request->getPost();

        if (!$this->validate([
            'quantity' => 'required|numeric',
        ])) {
            return $this->fail($this->validator->getErrors());
        }

        // Get current user
        $user = current_user();
        $username = $user['name'] ?? 'Unknown';

        // Old quantity
        $oldQuantity = $item['quantity'];
        $newQuantity = $data['quantity'];

        // Build new history entry
        $reason = trim($data['reason']);
        $newHistoryEntry = "<br/><br/>Updated by: {$username}, Quantity from: {$oldQuantity} to: {$newQuantity}, Reason: {$reason}";

        // Existing history
        $historyLog = $item['history'] ?? '';

        // Combine old + new history
        $combinedHistory = $historyLog ? $historyLog . "\n" . $newHistoryEntry : $newHistoryEntry;

        // Define max length limit (e.g. 65535 for TEXT, 16777215 for MEDIUMTEXT, adjust as needed)
        $maxLength = 65535; // example max length for TEXT

        if (strlen($combinedHistory) > $maxLength) {
            // If exceeded, skip adding the new history entry
            $finalHistory = $historyLog; // keep old history as is
        } else {
            $finalHistory = $combinedHistory;
        }

        // Update with new quantity and validated history
        $this->model->update($id, [
            'quantity' => $newQuantity,
            'history'  => $finalHistory,
        ]);

        return $this->respond(['message' => 'Excess stock updated']);
    }

    public function delete($id = null)
    {
        $item = $this->model->find($id);
        if (!$item) {
            return $this->failNotFound('Excess stock not found');
        }

        $this->model->delete($id);

        return $this->respondDeleted(['success' => true, 'message' => 'Excess stock deleted successfully']);
    }
}
