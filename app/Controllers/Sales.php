<?php

namespace App\Controllers;

use App\Libraries\SaleService;
use App\Models\CustomerModel;
use App\Models\ProductModel;
use App\Models\SaleModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;
use DomainException;

class Sales extends BaseController
{
    public function index(): string
    {
        $filters = $this->filters();
        $page = max(1, (int) $this->request->getGet('page'));
        $perPage = 20;
        $sales = model(SaleModel::class);
        $count = $sales->historyCount($filters);
        return $this->renderPage('sales/index', [
            'title' => 'Sales history', 'activePage' => 'sales', 'rows' => $sales->history($filters, $perPage, ($page - 1) * $perPage),
            'filters' => $filters, 'count' => $count, 'pager' => service('pager')->makeLinks($page, $perPage, $count),
            'staff' => model(UserModel::class)->orderBy('full_name')->findAll(),
            'customers' => model(CustomerModel::class)->orderBy('full_name')->findAll(),
        ]);
    }

    public function newSale(): string
    {
        return $this->renderPage('sales/form', [
            'title' => 'Record sale', 'activePage' => 'sales', 'products' => model(ProductModel::class)->catalog(),
            'customers' => model(CustomerModel::class)->orderBy('full_name')->findAll(),
            'errors' => session()->getFlashdata('formErrors') ?? [],
        ]);
    }

    public function create(): RedirectResponse
    {
        $rules = ['product_id' => 'required|is_natural_no_zero', 'quantity' => 'required|is_natural_no_zero|less_than_equal_to[99]', 'customer_id' => 'permit_empty|is_natural_no_zero'];
        if (! $this->validate($rules)) return redirect()->to(site_url('sales/new'))->withInput()->with('formErrors', $this->validator->getErrors());
        $customer = $this->request->getPost('customer_id');
        try {
            $result = (new SaleService())->record([(int) $this->request->getPost('product_id') => (int) $this->request->getPost('quantity')], $customer === '' || $customer === null ? null : (int) $customer, (int) session()->get('userId'));
        } catch (DomainException $error) {
            return redirect()->to(site_url('sales/new'))->withInput()->with('formErrors', ['sale' => $error->getMessage()]);
        } catch (\Throwable $error) {
            log_message('error', 'Sale failed: {message}', ['message' => $error->getMessage()]);
            return redirect()->to(site_url('sales/new'))->withInput()->with('formErrors', ['sale' => 'The sale could not be saved. Try again.']);
        }
        return redirect()->to(site_url('sales/receipt/' . $result['sale_id']))->with('success', 'Charged ' . peso($result['total']) . '.');
    }

    public function receipt(int $id): string|RedirectResponse
    {
        $rows = model(SaleModel::class)->receipt($id);
        if ($rows === []) return redirect()->to(site_url('sales'))->with('error', 'Receipt not found.');
        $total = array_reduce($rows, static fn (float $sum, array $row): float => $sum + (float) $row['total_price'], 0.0);
        return $this->renderPage('sales/receipt', ['title' => 'Receipt', 'activePage' => 'sales', 'rows' => $rows, 'receiptId' => $id, 'receiptTotal' => $total]);
    }

    public function export(): ResponseInterface
    {
        $rows = model(SaleModel::class)->history($this->filters(), 50000);
        $stream = fopen('php://temp', 'w+');
        fputcsv($stream, ['Date', 'Product', 'Customer', 'Staff', 'Quantity', 'Total PHP']);
        foreach ($rows as $row) {
            $clean = static function ($value): string {
                $text = (string) $value;
                return preg_match('/^[=+\-@]/', $text) ? "'" . $text : $text;
            };
            fputcsv($stream, [$clean($row['created_at']), $clean($row['product_name']), $clean($row['customer_name'] ?? 'Walk-in'), $clean($row['staff_name']), (int) $row['quantity'], $row['total_price']]);
        }
        rewind($stream);
        $content = stream_get_contents($stream);
        fclose($stream);
        return $this->response->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="matchaya-sales.csv"')->setBody($content);
    }

    /** @return array<string, string> */
    private function filters(): array
    {
        $filters = [];
        foreach (['from', 'to'] as $key) {
            $value = (string) $this->request->getGet($key);
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) && \DateTimeImmutable::createFromFormat('!Y-m-d', $value)?->format('Y-m-d') === $value) $filters[$key] = $value;
        }
        foreach (['staff', 'customer'] as $key) {
            $value = filter_var($this->request->getGet($key), FILTER_VALIDATE_INT);
            if ($value !== false && $value !== null && $value > 0) $filters[$key] = (string) $value;
        }
        $product = trim((string) $this->request->getGet('product'));
        if ($product !== '') $filters['product'] = mb_substr($product, 0, 100);
        return $filters;
    }
}
