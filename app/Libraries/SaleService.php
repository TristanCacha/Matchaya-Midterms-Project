<?php

namespace App\Libraries;

use DomainException;
use RuntimeException;

class SaleService
{
    /** @param array<int, int> $lines @return array{sale_id:int,total:string} */
    public function record(array $lines, ?int $customerId, int $staffId): array
    {
        if ($lines === [] || count($lines) > 30) throw new DomainException('Choose at least one product.');
        if ($customerId !== null && db_connect()->table('customers')->where('id', $customerId)->countAllResults() === 0) {
            throw new DomainException('Choose an existing customer or Walk-in.');
        }
        $db = db_connect();
        ksort($lines, SORT_NUMERIC);
        $db->transBegin();
        $totalCents = 0;
        $firstSaleId = 0;
        $stamp = date('Y-m-d H:i:s');
        try {
            foreach ($lines as $productId => $qty) {
                if ($productId < 1 || $qty < 1 || $qty > 99) throw new DomainException('Choose a quantity from 1 to 99.');
                $product = $db->query('SELECT id, name, price, stock_quantity FROM products WHERE id = ? FOR UPDATE', [$productId])->getRowArray();
                if ($product === null) throw new DomainException('A selected product no longer exists.');
                $available = (int) $product['stock_quantity'];
                if ($qty > $available) throw new DomainException('Only ' . $available . ' ' . $product['name'] . ' left. You asked for ' . $qty . '.');
                $db->query('UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ? AND stock_quantity >= ?', [$qty, $productId, $qty]);
                if ($db->affectedRows() !== 1) throw new DomainException('Stock changed. Review the cart and try again.');
                $cents = (int) round((float) $product['price'] * 100) * $qty;
                $totalCents += $cents;
                $db->table('sales')->insert([
                    'product_id' => $productId,
                    'customer_id' => $customerId,
                    'sold_by' => $staffId,
                    'quantity' => $qty,
                    'total_price' => number_format($cents / 100, 2, '.', ''),
                    'created_at' => $stamp,
                ]);
                if ($firstSaleId === 0) $firstSaleId = (int) $db->insertID();
            }
            if ($db->transStatus() === false || ! $db->transCommit()) throw new RuntimeException('The sale could not be saved.');
            return ['sale_id' => $firstSaleId, 'total' => number_format($totalCents / 100, 2, '.', '')];
        } catch (\Throwable $error) {
            $db->transRollback();
            throw $error;
        }
    }
}
