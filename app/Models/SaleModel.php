<?php

namespace App\Models;

use CodeIgniter\Model;

class SaleModel extends Model
{
    protected $table = 'sales';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['product_id', 'customer_id', 'sold_by', 'quantity', 'total_price', 'created_at'];
    protected $useTimestamps = false;

    /** @param array<string, mixed> $filters */
    public function historyQuery(array $filters): \CodeIgniter\Database\BaseBuilder
    {
        $builder = $this->db->table('sales s')
            ->select('s.*, p.name AS product_name, c.full_name AS customer_name, u.full_name AS staff_name')
            ->join('products p', 'p.id = s.product_id')
            ->join('customers c', 'c.id = s.customer_id', 'left')
            ->join('users u', 'u.id = s.sold_by');
        if (! empty($filters['from'])) $builder->where('s.created_at >=', $filters['from'] . ' 00:00:00');
        if (! empty($filters['to'])) $builder->where('s.created_at <=', $filters['to'] . ' 23:59:59');
        if (! empty($filters['staff'])) $builder->where('s.sold_by', (int) $filters['staff']);
        if (! empty($filters['customer'])) $builder->where('s.customer_id', (int) $filters['customer']);
        if (! empty($filters['product'])) $builder->like('p.name', (string) $filters['product']);
        return $builder;
    }

    /** @return array<int, array<string, mixed>> */
    public function history(array $filters, int $limit = 25, int $offset = 0): array
    {
        return $this->historyQuery($filters)->orderBy('s.created_at', 'DESC')->orderBy('s.id', 'DESC')
            ->limit($limit, $offset)->get()->getResultArray();
    }

    public function historyCount(array $filters): int
    {
        return $this->historyQuery($filters)->countAllResults();
    }

    /** @return array<int, array<string, mixed>> */
    public function receipt(int $saleId): array
    {
        $anchor = $this->find($saleId);
        if (! is_array($anchor)) return [];
        $builder = $this->historyQuery([])->where('s.created_at', $anchor['created_at'])->where('s.sold_by', $anchor['sold_by']);
        if ($anchor['customer_id'] === null) $builder->where('s.customer_id', null);
        else $builder->where('s.customer_id', $anchor['customer_id']);
        return $builder->orderBy('s.id', 'ASC')->get()->getResultArray();
    }

    /** @return array<string, mixed> */
    public function dashboard(): array
    {
        $today = date('Y-m-d');
        $from = date('Y-m-d', strtotime('-13 days'));
        $todayRows = $this->db->query('SELECT HOUR(created_at) hour, SUM(quantity) units, SUM(total_price) revenue, COUNT(DISTINCT CONCAT(sold_by, ":", COALESCE(customer_id, 0), ":", created_at)) orders FROM sales WHERE created_at >= ? AND created_at < DATE_ADD(?, INTERVAL 1 DAY) GROUP BY HOUR(created_at)', [$today, $today])->getResultArray();
        $hours = array_fill(0, 24, 0);
        $hourlyRevenue = array_fill(0, 24, 0.0);
        $hourlyUnits = array_fill(0, 24, 0);
        $revenue = 0.0;
        $orders = 0;
        $units = 0;
        foreach ($todayRows as $row) {
            $hours[(int) $row['hour']] = (int) $row['orders'];
            $hourlyRevenue[(int) $row['hour']] = (float) $row['revenue'];
            $hourlyUnits[(int) $row['hour']] = (int) $row['units'];
            $revenue += (float) $row['revenue'];
            $orders += (int) $row['orders'];
            $units += (int) $row['units'];
        }
        $top = $this->db->query('SELECT p.name, p.image, SUM(s.quantity) units FROM sales s JOIN products p ON p.id=s.product_id WHERE s.created_at >= ? GROUP BY p.id, p.name, p.image ORDER BY units DESC LIMIT 5', [date('Y-m-d', strtotime('-6 days'))])->getResultArray();
        $runway = $this->db->query('SELECT p.id, p.name, p.stock_quantity, COALESCE(SUM(s.quantity),0) units14 FROM products p LEFT JOIN sales s ON s.product_id=p.id AND s.created_at >= ? GROUP BY p.id, p.name, p.stock_quantity ORDER BY p.stock_quantity ASC', [$from])->getResultArray();
        foreach ($runway as &$item) {
            $daily = (int) $item['units14'] / 14;
            $item['days'] = $daily > 0 ? round((int) $item['stock_quantity'] / $daily, 1) : null;
        }
        unset($item);
        $visits = $this->db->query('SELECT customer_id, COUNT(DISTINCT CONCAT(sold_by, ":", created_at)) visits FROM sales WHERE customer_id IS NOT NULL GROUP BY customer_id')->getResultArray();
        $repeat = 0;
        foreach ($visits as $visit) if ((int) $visit['visits'] > 1) $repeat++;
        $attach = null;
        if ($this->db->tableExists('product_meta') && $this->db->table('product_meta')->countAllResults() > 0) {
            $rows = $this->db->query('SELECT s.created_at, s.sold_by, COALESCE(s.customer_id,0) customer_id, m.category FROM sales s LEFT JOIN product_meta m ON m.product_id=s.product_id WHERE s.created_at >= ?', [$from])->getResultArray();
            $groups = [];
            foreach ($rows as $row) {
                $key = $row['created_at'] . ':' . $row['sold_by'] . ':' . $row['customer_id'];
                $groups[$key][] = $row['category'];
            }
            $drinks = 0;
            $withFood = 0;
            foreach ($groups as $categories) {
                $hasFood = in_array('Food', $categories, true);
                $hasDrink = count(array_diff($categories, ['Food', 'Retail', null])) > 0;
                if ($hasDrink) {
                    $drinks++;
                    if ($hasFood) $withFood++;
                }
            }
            $attach = $drinks > 0 ? round($withFood * 100 / $drinks) : 0;
        }
        return [
            'revenue' => $revenue, 'orders' => $orders, 'units' => $units,
            'averageTicket' => $orders > 0 ? $revenue / $orders : 0,
            'hours' => $hours, 'hourlyRevenue' => $hourlyRevenue, 'hourlyUnits' => $hourlyUnits, 'top' => $top, 'runway' => $runway,
            'repeatShare' => count($visits) > 0 ? round($repeat * 100 / count($visits)) : 0,
            'foodAttach' => $attach,
        ];
    }
}
