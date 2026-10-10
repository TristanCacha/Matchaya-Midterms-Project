<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php if ($errors): ?><div class="form-errors" role="alert"><?php foreach ($errors as $error): ?><p><?= esc($error) ?></p><?php endforeach; ?></div><?php endif; ?>
<div class="pos-layout" data-pos>
    <section class="menu-panel" aria-label="Menu">
        <div class="panel-heading"><div><h2>Menu</h2><p>Tap a product to add one.</p></div><a href="<?= esc(site_url('sales/new'), 'attr') ?>">Simple sale form</a></div>
        <label class="sr-only" for="pos-search">Search menu</label><input id="pos-search" class="menu-search" type="search" placeholder="Search menu" autocomplete="off">
        <div class="category-tabs" role="group" aria-label="Product categories"><button class="category-tab is-active" type="button" data-category="All" aria-pressed="true">All</button><?php foreach ($categories as $category): ?><button class="category-tab" type="button" data-category="<?= esc($category, 'attr') ?>" aria-pressed="false"><?= esc($category) ?></button><?php endforeach; ?></div>
        <div class="product-grid">
        <?php foreach ($products as $tileIndex => $product): ?><button type="button" class="product-tile" data-id="<?= (int) $product['id'] ?>" data-name="<?= esc($product['name'], 'attr') ?>" data-price="<?= esc($product['price'], 'attr') ?>" data-stock="<?= (int) $product['stock_quantity'] ?>" data-category="<?= esc($product['category'] ?? 'Other', 'attr') ?>" <?= (int) $product['stock_quantity'] === 0 ? 'disabled' : '' ?>><img src="<?= esc(media_url($product['image'], 'products'), 'attr') ?>" <?php if ($srcset = media_srcset($product['image'])): ?>srcset="<?= esc($srcset, 'attr') ?>" sizes="(max-width: 620px) 44vw, (max-width: 850px) 29vw, 190px"<?php endif; ?> alt="<?= esc(media_alt($product['image'], $product['name']), 'attr') ?>" width="640" height="640" loading="<?= $tileIndex < 4 ? 'eager' : 'lazy' ?>" decoding="async" <?= $tileIndex < 4 ? 'fetchpriority="high"' : '' ?>><span class="tile-copy"><?php if (! empty($product['is_featured'])): ?><span class="featured-pill">Featured</span><?php endif; ?><strong><?= esc($product['name']) ?></strong><span class="tile-price"><?= esc(peso($product['price'])) ?></span><small class="stock-tag <?= (int) $product['stock_quantity'] === 0 ? 'out' : ((int) $product['stock_quantity'] < $lowThreshold ? 'low' : '') ?>"><?= (int) $product['stock_quantity'] === 0 ? 'Sold out' : (int) $product['stock_quantity'] . ' left' ?></small></span></button><?php endforeach; ?>
        </div>
        <p class="no-menu-match" hidden>No products match. Try another search or category.</p>
    </section>
    <button class="button button-primary mobile-cart-toggle" type="button" data-cart-toggle aria-controls="cart-panel" aria-expanded="false">View order · ₱0.00</button>
    <aside class="cart-panel" id="cart-panel" aria-label="Current order">
        <div class="panel-heading"><div><h2>Current order</h2><p>Review before charging.</p></div><button class="text-button" type="button" data-undo disabled>Undo add</button></div>
        <form id="charge-form" action="<?= esc(site_url('pos/charge'), 'attr') ?>" method="post">
            <?= csrf_field() ?>
            <div class="cart-lines" data-cart-lines><p class="cart-empty">No items yet. Choose a product from the menu.</p></div>
            <label for="pos-customer">Customer</label><select id="pos-customer" name="customer_id"><option value="">Walk-in</option><?php foreach ($customers as $customer): ?><option value="<?= (int) $customer['id'] ?>"><?= esc($customer['full_name']) ?></option><?php endforeach; ?></select>
            <a class="cart-add-customer" href="<?= esc(site_url('customers/new'), 'attr') ?>">Add a customer</a>
            <div class="cart-bottom"><div class="cart-summary"><span>Total</span><strong data-cart-total aria-live="polite">₱0.00</strong></div><div data-cart-inputs></div><button class="button button-primary charge-button" type="submit" data-charge disabled>Charge ₱0.00</button></div>
        </form>
    </aside>
</div>
<?= $this->endSection() ?>
