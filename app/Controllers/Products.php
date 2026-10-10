<?php

namespace App\Controllers;

use App\Libraries\MediaStorage;
use App\Models\ProductMetaModel;
use App\Models\ProductModel;
use CodeIgniter\HTTP\RedirectResponse;
use Config\Shop;

class Products extends BaseController
{
    public function index(): string
    {
        $search = trim((string) $this->request->getGet('q'));
        return $this->renderPage('products/index', [
            'title' => 'Products', 'activePage' => 'products', 'products' => model(ProductModel::class)->catalog($search), 'search' => $search, 'lowThreshold' => Shop::LOW_STOCK_THRESHOLD,
        ]);
    }

    public function newProduct(): string
    {
        return $this->renderPage('products/form', [
            'title' => 'Add product', 'activePage' => 'products', 'product' => null,
            'action' => site_url('products/create'), 'errors' => session()->getFlashdata('formErrors') ?? [],
        ]);
    }

    public function create(): RedirectResponse
    {
        return $this->save(null);
    }

    public function edit(int $id): string|RedirectResponse
    {
        $product = model(ProductModel::class)->detail($id);
        if ($product === null) return redirect()->to(site_url('products'))->with('error', 'Product not found.');
        return $this->renderPage('products/form', [
            'title' => 'Edit product', 'activePage' => 'products', 'product' => $product,
            'action' => site_url('products/' . $id . '/update'), 'errors' => session()->getFlashdata('formErrors') ?? [],
        ]);
    }

    public function update(int $id): RedirectResponse
    {
        if (model(ProductModel::class)->find($id) === null) return redirect()->to(site_url('products'))->with('error', 'Product not found.');
        return $this->save($id);
    }

    private function save(?int $id): RedirectResponse
    {
        $target = $id === null ? 'products/new' : 'products/' . $id . '/edit';
        $rules = [
            'name' => 'required|max_length[100]',
            'price' => 'required|decimal|greater_than_equal_to[0]|less_than[100000000]',
            'stock_quantity' => 'required|is_natural',
            'category' => 'permit_empty|max_length[40]',
            'description' => 'permit_empty|max_length[160]',
        ];
        if (! $this->validate($rules)) return redirect()->to(site_url($target))->withInput()->with('formErrors', $this->validator->getErrors());
        $upload = $this->request->getFile('image');
        $newImage = null;
        if ($upload !== null && $upload->getError() !== UPLOAD_ERR_NO_FILE) {
            $rule = 'uploaded[image]|is_image[image]|mime_in[image,image/jpeg,image/png,image/webp]|ext_in[image,jpg,jpeg,png,webp]|max_size[image,2048]|max_dims[image,4096,4096]';
            if (! $this->validateData([], ['image' => $rule])) {
                return redirect()->to(site_url($target))->withInput()->with('formErrors', ['image' => $this->validator->getError('image')]);
            }
            try {
                $newImage = (new MediaStorage())->save($upload, 'products');
            } catch (\Throwable $error) {
                return redirect()->to(site_url($target))->withInput()->with('formErrors', ['image' => $error->getMessage()]);
            }
        }
        $products = model(ProductModel::class);
        $oldImage = $id === null ? null : ($products->find($id)['image'] ?? null);
        $data = [
            'name' => trim((string) $this->request->getPost('name')),
            'price' => number_format((float) $this->request->getPost('price'), 2, '.', ''),
            'stock_quantity' => (int) $this->request->getPost('stock_quantity'),
        ];
        if ($newImage !== null) $data['image'] = $newImage;
        if ($id === null) {
            $data['created_at'] = date('Y-m-d H:i:s');
            $id = (int) $products->insert($data);
        } else {
            $products->update($id, $data);
        }
        if (db_connect()->tableExists('product_meta')) {
            $meta = [
                'category' => trim((string) ($this->request->getPost('category') ?: 'Other')),
                'description' => trim((string) $this->request->getPost('description')) ?: null,
                'is_featured' => $this->request->getPost('is_featured') === '1' ? 1 : 0,
            ];
            $model = model(ProductMetaModel::class);
            if ($model->find($id) === null) $model->insert(['product_id' => $id] + $meta);
            else $model->update($id, $meta);
        }
        if ($newImage !== null) (new MediaStorage())->delete($oldImage, 'products');
        return redirect()->to(site_url('products'))->with('success', 'Product saved.');
    }

    public function delete(int $id): RedirectResponse
    {
        $products = model(ProductModel::class);
        $product = $products->find($id);
        if ($product === null) return redirect()->to(site_url('products'))->with('error', 'Product not found.');
        if ($products->hasSales($id)) return redirect()->to(site_url('products'))->with('error', 'This product has sales. Set its stock to 0 instead.');
        $products->delete($id);
        (new MediaStorage())->delete($product['image'], 'products');
        return redirect()->to(site_url('products'))->with('success', 'Product deleted.');
    }

    public function restock(int $id): RedirectResponse
    {
        $amount = filter_var($this->request->getPost('amount'), FILTER_VALIDATE_INT);
        if ($amount === false || $amount < 1 || $amount > 100000) return redirect()->to(site_url('products'))->with('error', 'Enter a restock amount from 1 to 100000.');
        $db = db_connect();
        $db->query('UPDATE products SET stock_quantity = stock_quantity + ? WHERE id = ?', [$amount, $id]);
        if ($db->affectedRows() !== 1) return redirect()->to(site_url('products'))->with('error', 'Product not found.');
        return redirect()->to(site_url('products'))->with('success', 'Stock updated.');
    }
}
