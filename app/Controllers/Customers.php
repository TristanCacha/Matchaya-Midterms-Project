<?php

namespace App\Controllers;

use App\Models\CustomerModel;
use CodeIgniter\HTTP\RedirectResponse;

class Customers extends BaseController
{
    public function index(): string
    {
        $search = trim((string) $this->request->getGet('q'));
        $model = model(CustomerModel::class);
        if ($search !== '') $model->groupStart()->like('full_name', $search)->orLike('email', $search)->groupEnd();
        return $this->renderPage('customers/index', ['title' => 'Customers', 'activePage' => 'customers', 'customers' => $model->orderBy('full_name')->findAll(), 'search' => $search]);
    }

    public function newCustomer(): string
    {
        return $this->renderPage('customers/form', ['title' => 'Add customer', 'activePage' => 'customers', 'customer' => null, 'action' => site_url('customers/create'), 'errors' => session()->getFlashdata('formErrors') ?? []]);
    }

    public function create(): RedirectResponse
    {
        return $this->save(null);
    }

    public function edit(int $id): string|RedirectResponse
    {
        $customer = model(CustomerModel::class)->find($id);
        if (! is_array($customer)) return redirect()->to(site_url('customers'))->with('error', 'Customer not found.');
        return $this->renderPage('customers/form', ['title' => 'Edit customer', 'activePage' => 'customers', 'customer' => $customer, 'action' => site_url('customers/' . $id . '/update'), 'errors' => session()->getFlashdata('formErrors') ?? []]);
    }

    public function update(int $id): RedirectResponse
    {
        if (model(CustomerModel::class)->find($id) === null) return redirect()->to(site_url('customers'))->with('error', 'Customer not found.');
        return $this->save($id);
    }

    private function save(?int $id): RedirectResponse
    {
        $target = $id === null ? 'customers/new' : 'customers/' . $id . '/edit';
        $rules = ['full_name' => 'required|max_length[100]', 'email' => 'required|valid_email|max_length[100]', 'phone' => 'permit_empty|max_length[20]|regex_match[/^[0-9+() -]*$/]'];
        if (! $this->validate($rules)) return redirect()->to(site_url($target))->withInput()->with('formErrors', $this->validator->getErrors());
        $data = [
            'full_name' => trim((string) $this->request->getPost('full_name')),
            'email' => trim((string) $this->request->getPost('email')),
            'phone' => trim((string) $this->request->getPost('phone')) ?: null,
        ];
        $model = model(CustomerModel::class);
        if ($id === null) {
            $data['created_at'] = date('Y-m-d H:i:s');
            $model->insert($data);
        } else $model->update($id, $data);
        return redirect()->to(site_url('customers'))->with('success', 'Customer saved.');
    }

    public function delete(int $id): RedirectResponse
    {
        $model = model(CustomerModel::class);
        if ($model->find($id) === null) return redirect()->to(site_url('customers'))->with('error', 'Customer not found.');
        if ($model->hasSales($id)) return redirect()->to(site_url('customers'))->with('error', 'This customer has sales and cannot be deleted.');
        $model->delete($id);
        return redirect()->to(site_url('customers'))->with('success', 'Customer deleted.');
    }

    public function show(int $id): string|RedirectResponse
    {
        $model = model(CustomerModel::class);
        $customer = $model->find($id);
        if (! is_array($customer)) return redirect()->to(site_url('customers'))->with('error', 'Customer not found.');
        return $this->renderPage('customers/show', ['title' => $customer['full_name'], 'activePage' => 'customers', 'customer' => $customer, 'units' => $model->drinkUnits($id)]);
    }
}
