<?php

namespace App\Controllers;

use App\Models\NotificationModel;
use CodeIgniter\RESTful\ResourceController;

class NotificationController extends ResourceController
{
    protected $modelName = NotificationModel::class;
    protected $format = 'json';

    // Get all notifications
    public function index_page(){
        $builder = $this->model->builder(); // or db->table('notifications') if you're not using a model

        $notifications = $builder
            ->select('notifications.*, users.name as created_by_name')
            ->join('users', 'users.id = notifications.created_by')
            ->where('notifications.is_accepted', 0)
            ->orderBy('notifications.created_at', 'DESC')
            ->get()
            ->getResultArray();

        return view('notification', [
            'notifications' => $notifications
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
