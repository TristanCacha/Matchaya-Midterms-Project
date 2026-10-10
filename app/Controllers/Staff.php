<?php

namespace App\Controllers;

use App\Libraries\MediaStorage;
use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;

class Staff extends BaseController
{
    public function index(): string
    {
        $search = trim((string) $this->request->getGet('q'));
        $model = model(UserModel::class);
        if ($search !== '') $model->groupStart()->like('full_name', $search)->orLike('username', $search)->groupEnd();
        return $this->renderPage('staff/index', ['title' => 'Staff', 'activePage' => 'staff', 'staff' => $model->orderBy('full_name')->findAll(), 'search' => $search]);
    }

    public function newStaff(): string
    {
        return $this->renderPage('staff/form', ['title' => 'Add staff member', 'activePage' => 'staff', 'person' => null, 'action' => site_url('staff/create'), 'errors' => session()->getFlashdata('formErrors') ?? []]);
    }

    public function create(): RedirectResponse
    {
        return $this->save(null);
    }

    public function edit(int $id): string|RedirectResponse
    {
        $person = model(UserModel::class)->find($id);
        if (! is_array($person)) return redirect()->to(site_url('staff'))->with('error', 'Staff member not found.');
        return $this->renderPage('staff/form', ['title' => 'Edit staff member', 'activePage' => 'staff', 'person' => $person, 'action' => site_url('staff/' . $id . '/update'), 'errors' => session()->getFlashdata('formErrors') ?? []]);
    }

    public function update(int $id): RedirectResponse
    {
        if (model(UserModel::class)->find($id) === null) return redirect()->to(site_url('staff'))->with('error', 'Staff member not found.');
        return $this->save($id);
    }

    private function save(?int $id): RedirectResponse
    {
        $target = $id === null ? 'staff/new' : 'staff/' . $id . '/edit';
        $rules = [
            'username' => 'required|min_length[3]|max_length[50]|alpha_numeric_punct|is_unique[users.username,id,' . ($id ?? 0) . ']',
            'full_name' => 'required|max_length[100]',
            'password' => ($id === null ? 'required' : 'permit_empty') . '|min_length[8]|max_length[72]',
        ];
        if (! $this->validate($rules)) return redirect()->to(site_url($target))->withInput()->with('formErrors', $this->validator->getErrors());
        $upload = $this->request->getFile('avatar');
        $newAvatar = null;
        if ($upload !== null && $upload->getError() !== UPLOAD_ERR_NO_FILE) {
            $rule = 'uploaded[avatar]|is_image[avatar]|mime_in[avatar,image/jpeg,image/png,image/webp]|ext_in[avatar,jpg,jpeg,png,webp]|max_size[avatar,2048]|max_dims[avatar,4096,4096]';
            if (! $this->validateData([], ['avatar' => $rule])) return redirect()->to(site_url($target))->withInput()->with('formErrors', ['avatar' => $this->validator->getError('avatar')]);
            try {
                $newAvatar = (new MediaStorage())->save($upload, 'avatars');
            } catch (\Throwable $error) {
                return redirect()->to(site_url($target))->withInput()->with('formErrors', ['avatar' => $error->getMessage()]);
            }
        }
        $users = model(UserModel::class);
        $oldAvatar = $id === null ? null : ($users->find($id)['avatar'] ?? null);
        $data = ['username' => trim((string) $this->request->getPost('username')), 'full_name' => trim((string) $this->request->getPost('full_name'))];
        $password = (string) $this->request->getPost('password');
        if ($password !== '') $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        if ($newAvatar !== null) $data['avatar'] = $newAvatar;
        if ($id === null) {
            $data['created_at'] = date('Y-m-d H:i:s');
            $users->insert($data);
        } else $users->update($id, $data);
        if ($newAvatar !== null) (new MediaStorage())->delete($oldAvatar, 'avatars');
        return redirect()->to(site_url('staff'))->with('success', 'Staff member saved.');
    }

    public function delete(int $id): RedirectResponse
    {
        $users = model(UserModel::class);
        $person = $users->find($id);
        if (! is_array($person)) return redirect()->to(site_url('staff'))->with('error', 'Staff member not found.');
        if ($id === (int) session()->get('userId')) return redirect()->to(site_url('staff'))->with('error', 'You cannot delete your own account.');
        if ($users->countAllResults() <= 1) return redirect()->to(site_url('staff'))->with('error', 'The last staff account cannot be deleted.');
        if ($users->hasSales($id)) return redirect()->to(site_url('staff'))->with('error', 'This staff member has sales and cannot be deleted.');
        $users->delete($id);
        (new MediaStorage())->delete($person['avatar'], 'avatars');
        return redirect()->to(site_url('staff'))->with('success', 'Staff member deleted.');
    }

    public function account(): string
    {
        $person = model(UserModel::class)->currentPublicUser();
        return $this->renderPage('staff/account', ['title' => 'My account', 'activePage' => 'account', 'person' => $person]);
    }
}
