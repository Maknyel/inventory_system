<?php

namespace App\Controllers;

use App\Models\NotificationModel;
use CodeIgniter\RESTful\ResourceController;

class NotificationController extends ResourceController
{
    protected $modelName = NotificationModel::class;
    protected $format = 'json';

    // Get all notifications
    public function index_page()
    {
        $builder = $this->model->builder(); // or db->table('notifications')

        $notifications = $builder
            ->select('
                notifications.*, 
                users.name as created_by_name, 
                inventory_history.name as inventory_name,
                inventory_history.description as inventory_description,
                inventory_history.quantity as inventory_quantity,
                inventory.unit as inventory_unit
            ')
            ->join('users', 'users.id = notifications.created_by')
            ->join('inventory_history', 'inventory_history.id = notifications.inventory_history_id', 'left')
            ->join('inventory', 'inventory.id = notifications.inventory_id', 'left')
            ->where('notifications.is_accepted', 0)
            ->orderBy('notifications.created_at', 'DESC')
            ->get()
            ->getResultArray();

        return view('notification', [
            'notifications' => $notifications
        ]);
    }

    public function accept($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'bad_request']);
        }

        $notificationModel = model('NotificationModel');
        $inventoryModel = model('InventoryModel');
        $inventoryHistoryModel = model('InventoryHistoryModel');

        // 1. Get notification
        $notification = $notificationModel->find($id);
        if (!$notification) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'notification_not_found']);
        }

        // 2. Get related inventory and inventory_history
        $inventory = $inventoryModel->find($notification['inventory_id']);
        $inventoryHistory = $inventoryHistoryModel->find($notification['inventory_history_id']);

        // 3. Only apply update if it's targeting 'quantity'
        $db = \Config\Database::connect();
        if ($notification['column_to_be_updated'] === 'quantity') {
            if (!$inventory || !$inventoryHistory) {
                return $this->response->setStatusCode(404)->setJSON(['status' => 'related_data_not_found']);
            }

            $change = (float)$notification['column_to_value'];

            // Ensure fields exist and are numeric
            $currentQty = isset($inventory['current_quantity']) ? (float)$inventory['current_quantity'] : null;
            $historyQty = isset($inventoryHistory['quantity']) ? (float)$inventoryHistory['quantity'] : null;

            if ($currentQty === null || $historyQty === null) {
                return $this->response->setStatusCode(500)->setJSON(['status' => 'invalid_data']);
            }

            $newInventoryQty = $currentQty + $change;
            $newHistoryQty = $historyQty + $change;

            // Only update if values are different to avoid "no data to update" error
            if ($newInventoryQty !== $currentQty) {
                $db->table('inventory')
                    ->where('id', $inventory['id'])
                    ->set('current_quantity', $newInventoryQty)
                    ->update();

            }

            if ($newHistoryQty !== $historyQty) {
                // $updatedHistory = $inventoryHistoryModel->update($inventoryHistory['id'], ['quantity' => $newHistoryQty]);
                $db->table('inventory_history')
                    ->where('id', $inventoryHistory['id'])
                    ->set('quantity', $newHistoryQty)
                    ->update();
            }
        }

        // 4. Mark notification as accepted
        $notificationModel->update($id, ['is_accepted' => 1]);

        return $this->response->setJSON([
            'status' => 'success',
            'inventory_new_quantity' => $newInventoryQty ?? null,
            'history_new_quantity' => $newHistoryQty ?? null
        ]);
    }

    public function cancel($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'bad_request']);
        }

        $notificationModel = model('NotificationModel');
        
        $notification = $notificationModel->find($id);
        if (!$notification) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'notification_not_found']);
        }

        // 4. Mark notification as accepted
        $notificationModel->update($id, ['is_accepted' => 1]);

        return $this->response->setJSON([
            'status' => 'success'
        ]);
    }


    public function index()
    {
        $notifications = $this->model->findAll();
        return $this->respond($notifications);
    }

    // Get one notification by id
    public function show($id = null)
    {
        $notification = $this->model->find($id);
        if (!$notification) {
            return $this->failNotFound('Notification not found');
        }
        return $this->respond($notification);
    }

    // Create new notification
    public function create()
    {
        $data = $this->request->getJSON(true);

        if (!$this->model->insert($data)) {
            return $this->failValidationErrors($this->model->errors());
        }

        return $this->respondCreated(['message' => 'Notification created']);
    }

    // Update notification
    public function update($id = null)
    {
        $data = $this->request->getJSON(true);

        if (!$this->model->find($id)) {
            return $this->failNotFound('Notification not found');
        }

        if (!$this->model->update($id, $data)) {
            return $this->failValidationErrors($this->model->errors());
        }

        return $this->respond(['message' => 'Notification updated']);
    }

    // Delete notification
    public function delete($id = null)
    {
        if (!$this->model->find($id)) {
            return $this->failNotFound('Notification not found');
        }

        $this->model->delete($id);
        return $this->respondDeleted(['message' => 'Notification deleted']);
    }

    public function markAsViewed($id)
    {
        if ($this->request->isAJAX()) {
            $notificationModel = model('NotificationModel');

            $updated = $notificationModel
                ->where('id', $id)
                ->set(['is_viewed' => 1])
                ->update();

            if ($updated) {
                return $this->response->setJSON(['status' => 'success']);
            }

            return $this->response->setStatusCode(500)->setJSON(['status' => 'error']);
        }

        return $this->response->setStatusCode(400)->setJSON(['status' => 'invalid_request']);
    }
}
