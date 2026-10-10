<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomerModel extends Model
{
    protected $table = 'customers';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['full_name', 'email', 'phone', 'created_at'];
    protected $useTimestamps = false;

    public function hasSales(int $id): bool
    {
        return $this->db->table('sales')->where('customer_id', $id)->countAllResults() > 0;
    }

    public function drinkUnits(int $id): int
    {
        $builder = $this->db->table('sales s')->selectSum('s.quantity', 'units')->where('s.customer_id', $id);
        if ($this->db->tableExists('product_meta') && $this->db->table('product_meta')->countAllResults() > 0) {
            $builder->join('product_meta m', 'm.product_id = s.product_id', 'left')
                ->whereNotIn('m.category', ['Food', 'Retail']);
        }
        return (int) ($builder->get()->getRowArray()['units'] ?? 0);
    }
}
