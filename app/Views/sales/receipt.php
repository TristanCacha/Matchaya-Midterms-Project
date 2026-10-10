<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div data-clear-cart></div><div class="page-actions no-print"><p>Sale recorded and stock updated.</p><div class="button-row"><a class="button" href="<?= esc(site_url('pos'), 'attr') ?>">New order</a><button class="button button-primary" type="button" onclick="window.print()">Print receipt</button></div></div>
<article class="receipt"><header><div class="receipt-brand"><?= $this->include('partials/brand') ?></div><p>Receipt #<?= (int) $receiptId ?></p><p><?= esc(date('j M Y, g:i a', strtotime($rows[0]['created_at']))) ?></p></header>
<div class="receipt-lines"><?php foreach ($rows as $row): ?><div><span><?= (int) $row['quantity'] ?> × <?= esc($row['product_name']) ?></span><strong><?= esc(peso($row['total_price'])) ?></strong></div><?php endforeach; ?></div>
<div class="receipt-total"><span>Total</span><strong><?= esc(peso($receiptTotal)) ?></strong></div>
<p>Customer: <?= esc($rows[0]['customer_name'] ?? 'Walk-in') ?></p><p>Served by <?= esc($rows[0]['staff_name']) ?></p><footer>Thank you. Come again.</footer></article>
<?= $this->endSection() ?>
