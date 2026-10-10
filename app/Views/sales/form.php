<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-actions"><p>This single-item form works without JavaScript.</p><a class="button" href="<?= esc(site_url('pos'), 'attr') ?>">Open counter</a></div>
<?php if ($errors): ?><div class="form-errors" role="alert"><?php foreach ($errors as $error): ?><p><?= esc($error) ?></p><?php endforeach; ?></div><?php endif; ?>
<form class="editor-card form-stack" action="<?= esc(site_url('sales/create'), 'attr') ?>" method="post"><?= csrf_field() ?>
<label for="product_id">Product</label><select id="product_id" name="product_id" required><option value="">Choose product</option><?php foreach ($products as $product): ?><option value="<?= (int) $product['id'] ?>" <?= old('product_id') == $product['id'] ? 'selected' : '' ?> <?= (int) $product['stock_quantity'] === 0 ? 'disabled' : '' ?>><?= esc($product['name']) ?> — <?= esc(peso($product['price'])) ?> — <?= (int) $product['stock_quantity'] ?> left</option><?php endforeach; ?></select>
<label for="customer_id">Customer</label><select id="customer_id" name="customer_id"><option value="">Walk-in</option><?php foreach ($customers as $customer): ?><option value="<?= (int) $customer['id'] ?>" <?= old('customer_id') == $customer['id'] ? 'selected' : '' ?>><?= esc($customer['full_name']) ?></option><?php endforeach; ?></select>
<label for="quantity">Quantity</label><input id="quantity" name="quantity" type="number" min="1" max="99" value="<?= esc(old('quantity') ?? '1', 'attr') ?>" required>
<button class="button button-primary" type="submit">Charge sale</button>
</form>
<?= $this->endSection() ?>
