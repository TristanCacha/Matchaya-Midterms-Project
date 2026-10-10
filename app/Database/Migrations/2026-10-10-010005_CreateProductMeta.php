<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductMeta extends Migration
{
    public function up(): void
    {
        $this->db->query("CREATE TABLE product_meta (
            product_id INT PRIMARY KEY,
            category VARCHAR(40) NOT NULL,
            description VARCHAR(160) NULL,
            is_featured TINYINT(1) NOT NULL DEFAULT 0,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }

    public function down(): void
    {
        $this->db->query('DROP TABLE product_meta');
    }
}
