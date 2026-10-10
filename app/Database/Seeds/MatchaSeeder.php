<?php

namespace App\Database\Seeds;

use CodeIgniter\CLI\CLI;
use CodeIgniter\Database\Seeder;
use DateTimeImmutable;
use DateTimeZone;

class MatchaSeeder extends Seeder
{
    public function run(): void
    {
        date_default_timezone_set('Asia/Manila');
        $now = date('Y-m-d H:i:s');
        $db = $this->db;

        if ($db->table('products')->countAllResults() === 0) {
            $manifest = json_decode((string) file_get_contents(ROOTPATH . 'database/seeds/image-manifest.json'), true) ?: [];
            $images = array_column($manifest, 'slug', 'product');
            $products = [
                ['Usucha', '190.00', 24, 'Straight', 'Whisked thin matcha, no milk, 80 ml.', 1],
                ['Koicha', '245.00', 9, 'Straight', 'Slow whisked thick matcha, no milk.', 0],
                ['Hojicha', '180.00', 18, 'Straight', 'Roasted green tea, served warm.', 0],
                ['Hot Matcha Latte', '205.00', 34, 'Milk drinks', 'Whisked matcha with steamed milk.', 1],
                ['Iced Matcha Latte', '220.00', 38, 'Milk drinks', 'Matcha and milk over ice.', 1],
                ['Strawberry Matcha', '260.00', 16, 'Milk drinks', 'Strawberry purée under iced matcha.', 0],
                ['Sea Salt Cream Matcha', '275.00', 11, 'Milk drinks', 'Iced matcha with a lightly salted cream.', 0],
                ['Hojicha Latte', '230.00', 21, 'Hojicha', 'Roasted hojicha with milk.', 1],
                ['Banana Pudding Matcha', '270.00', 7, 'Milk drinks', 'Matcha with banana pudding cream.', 0],
                ['Langka Matcha', '265.00', 3, 'Seasonal', 'Iced matcha with a little jackfruit.', 0],
                ['Coffee Matcha', '250.00', 0, 'Milk drinks', 'Espresso and matcha layered with milk.', 0],
                ['Matcha Basque Cheesecake', '215.00', 12, 'Food', 'Baked cheesecake with matcha.', 0],
                ['Matcha Mochi', '135.00', 20, 'Food', 'Soft rice cake with matcha filling.', 0],
                ['Hojicha Cookie', '95.00', 25, 'Food', 'Roasted tea cookie.', 0],
                ['Matcha Tin 30 g', '790.00', 8, 'Retail', 'Thirty grams of matcha for home.', 0],
                ['Chasen Bamboo Whisk', '680.00', 6, 'Retail', 'Bamboo whisk for preparing matcha.', 0],
                ['Chawan Bowl', '850.00', 5, 'Retail', 'Handmade handleless ceramic tea bowl.', 0],
            ];
            foreach ($products as [$name, $price, $stock, $category, $description, $featured]) {
                $image = isset($images[$name]) ? 'demo/products/' . $images[$name] . '.webp' : null;
                $db->table('products')->insert(['name' => $name, 'price' => $price, 'stock_quantity' => $stock, 'image' => $image, 'created_at' => $now]);
                $id = (int) $db->insertID();
                if ($db->tableExists('product_meta')) {
                    $db->table('product_meta')->insert(['product_id' => $id, 'category' => $category, 'description' => $description, 'is_featured' => $featured]);
                }
            }
        }

        if ($db->table('customers')->countAllResults() === 0) {
            foreach ([
                ['Mika Reyes', 'mika.reyes@example.test', '0917 120 3301'],
                ['Paolo Cruz', 'paolo.cruz@example.test', '0917 120 3302'],
                ['Aya Santos', 'aya.santos@example.test', '0917 120 3303'],
                ['Nico Lim', 'nico.lim@example.test', '0917 120 3304'],
                ['Sam Dela Rosa', 'sam.rosa@example.test', '0917 120 3305'],
                ['Jules Garcia', 'jules.garcia@example.test', '0917 120 3306'],
                ['Rin Tan', 'rin.tan@example.test', '0917 120 3307'],
                ['Alex Mendoza', 'alex.mendoza@example.test', '0917 120 3308'],
            ] as [$name, $email, $phone]) {
                $db->table('customers')->insert(['full_name' => $name, 'email' => $email, 'phone' => $phone, 'created_at' => $now]);
            }
        }

        if ($db->table('users')->countAllResults() === 0) {
            foreach ([['counter1', 'Counter Staff One'], ['counter2', 'Counter Staff Two']] as [$username, $name]) {
                $password = bin2hex(random_bytes(9));
                $db->table('users')->insert(['username' => $username, 'full_name' => $name, 'password' => password_hash($password, PASSWORD_DEFAULT), 'avatar' => null, 'created_at' => $now]);
                CLI::write($username . ' temporary password: ' . $password, 'yellow');
            }
            CLI::write('Save these one-time sample credentials; they are not written to the repository.', 'yellow');
        }

        if ($db->table('sales')->countAllResults() === 0) {
            $items = $db->table('products')->select('id, price')->orderBy('id')->get()->getResultArray();
            $customers = array_column($db->table('customers')->select('id')->get()->getResultArray(), 'id');
            $staff = array_column($db->table('users')->select('id')->get()->getResultArray(), 'id');
            if ($items !== [] && $staff !== []) {
                $tz = new DateTimeZone('Asia/Manila');
                for ($daysAgo = 13; $daysAgo >= 0; $daysAgo--) {
                    $day = (new DateTimeImmutable('today', $tz))->modify('-' . $daysAgo . ' days');
                    $weekend = in_array((int) $day->format('N'), [6, 7], true);
                    $orders = $weekend ? 8 : 5;
                    for ($order = 0; $order < $orders; $order++) {
                        $hour = [8, 9, 10, 14, 15, 16, 17, 18][$order];
                        $stamp = $day->setTime($hour, ($order * 7 + $daysAgo) % 60)->format('Y-m-d H:i:s');
                        $customerId = $order % 3 === 0 ? null : (int) $customers[($order + $daysAgo) % count($customers)];
                        $seller = (int) $staff[$order % count($staff)];
                        $lines = $order % 2 === 0 ? 2 : 1;
                        for ($line = 0; $line < $lines; $line++) {
                            $item = $items[($order * 3 + $daysAgo + $line) % 10];
                            $qty = $order % 5 === 0 ? 2 : 1;
                            $cents = (int) round((float) $item['price'] * 100) * $qty;
                            $db->table('sales')->insert(['product_id' => $item['id'], 'customer_id' => $customerId, 'sold_by' => $seller, 'quantity' => $qty, 'total_price' => number_format($cents / 100, 2, '.', ''), 'created_at' => $stamp]);
                        }
                    }
                }
            }
        }
    }
}
