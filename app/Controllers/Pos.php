<?php

namespace App\Controllers;

use App\Libraries\SaleService;
use App\Models\CustomerModel;
use App\Models\ProductModel;
use CodeIgniter\HTTP\RedirectResponse;
use DomainException;
use Config\Shop;

class Pos extends BaseController
{
    public function index(): string
    {
        $products = model(ProductModel::class)->catalog();
        usort($products, static function ($a, $b): int {
            return ((int) ($b['is_featured'] ?? 0) <=> (int) ($a['is_featured'] ?? 0)) ?: strcmp($a['name'], $b['name']);
        });
        $categories = array_values(array_unique(array_filter(array_column($products, 'category'))));
        return $this->renderPage('pos/index', [
            'title' => 'New order', 'activePage' => 'pos', 'products' => $products, 'categories' => $categories,
            'customers' => model(CustomerModel::class)->orderBy('full_name')->findAll(), 'lowThreshold' => Shop::LOW_STOCK_THRESHOLD,
            'errors' => session()->getFlashdata('formErrors') ?? [],
        ]);
    }

    public function charge(): RedirectResponse
    {
        $submitted = $this->request->getPost('lines');
        $lines = [];
        if (is_array($submitted)) {
            foreach ($submitted as $id => $qty) {
                if (! ctype_digit((string) $id) || ! ctype_digit((string) $qty)) {
                    return redirect()->to(site_url('pos'))->with('formErrors', ['cart' => 'Review the cart quantities.']);
                }
                if ((int) $qty > 0) $lines[(int) $id] = (int) $qty;
            }
        }
        $customer = $this->request->getPost('customer_id');
        if ($customer !== null && $customer !== '' && (! ctype_digit((string) $customer) || (int) $customer < 1)) {
            return redirect()->to(site_url('pos'))->with('formErrors', ['cart' => 'Choose an existing customer or Walk-in.']);
        }
        try {
            $result = (new SaleService())->record($lines, $customer === null || $customer === '' ? null : (int) $customer, (int) session()->get('userId'));
        } catch (DomainException $error) {
            return redirect()->to(site_url('pos'))->with('formErrors', ['cart' => $error->getMessage()]);
        } catch (\Throwable $error) {
            log_message('error', 'Charge failed: {message}', ['message' => $error->getMessage()]);
            return redirect()->to(site_url('pos'))->with('formErrors', ['cart' => 'The order could not be saved. Try again.']);
        }
        return redirect()->to(site_url('sales/receipt/' . $result['sale_id']))->with('success', 'Charged ' . peso($result['total']) . '.');
    }
}
