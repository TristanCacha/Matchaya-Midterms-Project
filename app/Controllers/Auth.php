<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;

class Auth extends BaseController
{
    public function setup(): string|RedirectResponse
    {
        if (model(UserModel::class)->hasConfiguredAccount()) return redirect()->to(site_url('login'));
        return view('auth/setup', ['title' => 'Create the first staff account']);
    }

    public function createAccount(): RedirectResponse
    {
        $users = model(UserModel::class);
        if ($users->hasConfiguredAccount()) return redirect()->to(site_url('login'));
        $rules = [
            'full_name' => 'required|max_length[100]',
            'username' => 'required|min_length[3]|max_length[50]|alpha_numeric_punct|is_unique[users.username]',
            'password' => 'required|min_length[8]|max_length[72]',
            'password_confirm' => 'required|matches[password]',
        ];
        if (! $this->validate($rules)) return redirect()->to(site_url('setup'))->withInput()->with('formErrors', $this->validator->getErrors());
        $id = $users->insert([
            'username' => trim((string) $this->request->getPost('username')),
            'full_name' => trim((string) $this->request->getPost('full_name')),
            'password' => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        session()->regenerate(true);
        session()->set('userId', (int) $id);
        return redirect()->to(site_url('pos'))->with('success', 'Account created.');
    }

    public function login(): string|RedirectResponse
    {
        if (! model(UserModel::class)->hasConfiguredAccount()) return redirect()->to(site_url('setup'));
        if (is_numeric(session()->get('userId'))) return redirect()->to(site_url('pos'));
        return view('auth/login', ['title' => 'Sign in']);
    }

    public function authenticate(): RedirectResponse
    {
        $throttler = service('throttler');
        $key = 'login_' . hash('sha256', $this->request->getIPAddress());
        if (! $throttler->check($key, 5, 60)) {
            return redirect()->to(site_url('login'))->with('authError', 'Too many attempts. Try again in a minute.');
        }
        $username = trim((string) $this->request->getPost('username'));
        $password = (string) $this->request->getPost('password');
        $users = model(UserModel::class);
        $user = $users->getByUsername($username);
        if ($user === null || ! password_verify($password, (string) $user['password'])) {
            return redirect()->to(site_url('login'))->withInput()->with('authError', 'The username or password was not recognized.');
        }
        if (password_needs_rehash((string) $user['password'], PASSWORD_DEFAULT)) {
            $users->update((int) $user['id'], ['password' => password_hash($password, PASSWORD_DEFAULT)]);
        }
        session()->regenerate(true);
        session()->set('userId', (int) $user['id']);
        return redirect()->to(site_url('pos'));
    }

    public function logout(): RedirectResponse
    {
        session()->destroy();
        return redirect()->to(site_url('login'));
    }
}
