<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductMetaModel extends Model
{
    protected $table = 'product_meta';
    protected $primaryKey = 'product_id';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $allowedFields = ['product_id', 'category', 'description', 'is_featured'];
    protected $useTimestamps = false;
}
