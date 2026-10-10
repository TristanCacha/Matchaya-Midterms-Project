<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSales extends Migration
{
    public function up(): void
    {
        $this->db->query("CREATE TABLE sales (
            id INT AUTO_INCREMENT PRIMARY KEY,
            product_id INT NOT NULL,
            customer_id INT NULL,
            sold_by INT NOT NULL,
            quantity INT NOT NULL,
            total_price DECIMAL(10,2) NOT NULL,
            created_at DATETIME NOT NULL,
            INDEX idx_sales_date (created_at),
            INDEX idx_sales_customer (customer_id),
            INDEX idx_sales_staff (sold_by),
            FOREIGN KEY (product_id) REFERENCES products(id),
            FOREIGN KEY (customer_id) REFERENCES customers(id),
            FOREIGN KEY (sold_by) REFERENCES users(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }

    public function down(): void
    {
        $this->db->query('DROP TABLE sales');
    }
}
