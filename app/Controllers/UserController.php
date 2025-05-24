<?php
namespace App\Controllers;

use CodeIgniter\Controller;

use App\Models\UserModel;
use App\Models\RoleModel;


class UserController extends Controller
{
    public function getRoles()
    {
        $roleModel = new RoleModel();
        $roles = $roleModel->where('id <>',4)->findAll();

        return $this->response->setJSON($roles);
    }

    public function index()
    {
        if (!session()->has('user_id')) {
            return redirect()->to(base_url('login'));
        }
        if (current_user()['role_id'] == 2 || current_user()['role_id'] == 3) {
            return redirect()->to(base_url('/'));
        }
        $userModel = new UserModel();
        $users = $userModel->select('users.*, roles.name as role_name, roles.description as role_description')
                    ->join('roles', 'roles.id = users.role_id')
                    ->where('users.id !=', session()->get('user_id'))
                    ->where('users.role_id <>', 4)
                    ->findAll();

        return view('user/index', ['users' => $users]);
    }

    public function api($id)
    {
        $userModel = new UserModel();
        $user = $userModel->find($id);

        if (!$user) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'User not found']);
        }

        return $this->response->setJSON($user);
    }

    public function store()
    {
        $data = $this->request->getJSON(true);

        if (!isset($data['name'], $data['email'], $data['password'], $data['role_id'])) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Missing required fields']);
        }

        $userModel = new UserModel();

        $userModel->insert([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'password'   => password_hash($data['password'], PASSWORD_DEFAULT),
            'role_id'    => $data['role_id'],
            'image_url'  => $data['image_url'] ?? null,
        ]);

        return $this->response->setJSON(['success' => true]);
    }

    public function update_user($id)
    {
        $data = $this->request->getJSON(true);

        $userModel = new UserModel();
        $user = $userModel->find($id);

        if (!$user) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'User not found']);
        }

        $updateData = [
            'name'      => $data['name'] ?? $user['name'],
            'email'     => $data['email'] ?? $user['email'],
            'role_id'   => $data['role_id'] ?? $user['role_id'],
            'image_url' => $data['image_url'] ?? $user['image_url'],
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $userModel->update($id, $updateData);

        return $this->response->setJSON(['success' => true]);
    }

    public function delete($id)
    {
        $userModel = new UserModel();

        if (!$userModel->find($id)) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'User not found']);
        }

        $userModel->delete($id);

        return $this->response->setJSON(['success' => true]);
    }

    public function profile()
    {
        if (!session()->has('user_id')) {
            return redirect()->to(base_url('login'));
        }
        $userModel = new UserModel();
        $user = $userModel->find(session()->get('user_id')); // Change to the logged-in user ID
        return view('user/profile', ['user' => $user]);
    }

    public function update()
    {
        $userModel = new UserModel();
        $id = $this->request->getPost('id');

        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
        ];

        if ($userModel->update($id, $data)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Profile updated.']);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to update.']);
    }

    public function uploadImage()
    {
        $id = $this->request->getPost('id');
        $image = $this->request->getFile('image');

        if ($image && $image->isValid() && !$image->hasMoved()) {
            $newName = $image->getRandomName();
            $image->move(FCPATH . 'uploads/user/'.$id, $newName);

            $model = new UserModel();
            $model->update($id, ['image_url' => '/uploads/user/'.$id.'/' . $newName]);

            return $this->response->setJSON(['status' => 'success', 'message' => 'Image uploaded successfully.']);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Image upload failed.']);
    }

    public function changePassword()
    {
        $id = $this->request->getPost('id');
        $current = $this->request->getPost('current_password');
        $new = $this->request->getPost('new_password');
        $confirm = $this->request->getPost('confirm_password');

        $model = new UserModel();
        $user = $model->find($id);

        if (!password_verify($current, $user['password'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Current password is incorrect.']);
        }

        if ($new !== $confirm) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Passwords do not match.']);
        }

        $model->update($id, ['password' => password_hash($new, PASSWORD_DEFAULT)]);
        return $this->response->setJSON(['status' => 'success', 'message' => 'Password updated successfully.']);
    }


}
