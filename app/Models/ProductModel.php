<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['name', 'price', 'stock_quantity', 'image', 'created_at'];
    protected $useTimestamps = false;

    /** @return array<int, array<string, mixed>> */
    public function catalog(?string $search = null): array
    {
        $builder = $this->db->table('products p');
        if ($this->db->tableExists('product_meta')) {
            $builder->select('p.*, m.category, m.description, m.is_featured')
                ->join('product_meta m', 'm.product_id = p.id', 'left');
        } else {
            $builder->select('p.*');
        }
        if ($search !== null && $search !== '') {
            $builder->like('p.name', $search);
        }
        return $builder->orderBy('p.name', 'ASC')->get()->getResultArray();
    }

    /** @return array<string, mixed>|null */
    public function detail(int $id): ?array
    {
        $row = $this->find($id);
        if (! is_array($row)) return null;
        if ($this->db->tableExists('product_meta')) {
            $meta = $this->db->table('product_meta')->where('product_id', $id)->get()->getRowArray();
            if ($meta !== null) $row = array_merge($row, $meta);
        }
        return $row;
    }

    public function hasSales(int $id): bool
    {
        return $this->db->table('sales')->where('product_id', $id)->countAllResults() > 0;
    }
}
