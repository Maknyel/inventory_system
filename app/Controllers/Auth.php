<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        // Check if the user is already logged in
        if (session()->has('user_id')) {
            // If logged in, redirect to the home page
            return redirect()->to(base_url(''));
        }

        // If the form is submitted
        if ($this->request->getMethod() === 'post') {
            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');

            // Check if user exists in the database
            $userModel = new UserModel();
            $user = $userModel->where('email', $email)->first();

            if ($user && password_verify($password, $user['password'])) {
                // Store the user ID in the session to track the login status
                session()->set('user_id', $user['id']);
                // Redirect to the homepage
                return redirect()->to(base_url(''));
            } else {
                // Invalid login credentials
                session()->setFlashdata('error', 'Invalid login credentials');
                return redirect()->to(base_url('login'));
            }
        }

        return view('login'); // Show the login form
    }

    // Logout function to destroy session and redirect to login
    public function logout()
    {
        session()->remove('user_id'); // Remove the user ID from the session
        return redirect()->to(base_url('login')); // Redirect to login page
    }
}
