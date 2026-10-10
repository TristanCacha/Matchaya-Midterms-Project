<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-actions"><p>Manage the menu and what is ready to sell.</p><a class="button button-primary" href="<?= esc(site_url('products/new'), 'attr') ?>">Add product</a></div>
<form class="search-form" method="get" action="<?= esc(site_url('products'), 'attr') ?>"><label for="product-search">Search products</label><div><input id="product-search" name="q" value="<?= esc($search, 'attr') ?>" placeholder="Product name"><button class="button" type="submit">Search</button></div></form>
<?php if ($products === []): ?><div class="empty-state"><svg class="empty-illustration" aria-hidden="true"><use href="<?= esc(base_url('assets/brand/empty-set.svg'), 'attr') ?>#tin"/></svg><p>No products found. Add the first menu item.</p></div><?php else: ?>
<div class="table-wrap"><table class="data-table"><caption class="sr-only">Products and stock</caption><thead><tr><th scope="col">Product</th><th scope="col">Price</th><th scope="col">Stock</th><th scope="col">Category</th><th scope="col">Actions</th></tr></thead><tbody>
<?php foreach ($products as $product): ?>
<tr><th scope="row" data-label="Product"><span class="product-cell"><img src="<?= esc(media_url($product['image'], 'products'), 'attr') ?>" alt="<?= esc(media_alt($product['image'], $product['name']), 'attr') ?>" width="42" height="42" loading="lazy" decoding="async"><span><?= esc($product['name']) ?></span></span></th>
<td data-label="Price" class="money"><?= esc(peso($product['price'])) ?></td>
<td data-label="Stock"><span class="stock-tag <?= (int) $product['stock_quantity'] === 0 ? 'out' : ((int) $product['stock_quantity'] < $lowThreshold ? 'low' : '') ?>"><?= (int) $product['stock_quantity'] === 0 ? 'Sold out' : esc((string) $product['stock_quantity']) . ' left' ?></span></td>
<td data-label="Category"><?= esc($product['category'] ?? 'Other') ?></td>
<td data-label="Actions"><div class="row-actions"><a class="button button-small" href="<?= esc(site_url('products/' . $product['id'] . '/edit'), 'attr') ?>">Edit</a>
<form method="post" action="<?= esc(site_url('products/' . $product['id'] . '/restock'), 'attr') ?>" class="restock-form"><?= csrf_field() ?><label class="sr-only" for="restock-<?= (int) $product['id'] ?>">Restock <?= esc($product['name']) ?></label><input id="restock-<?= (int) $product['id'] ?>" type="number" name="amount" min="1" max="100000" value="1" required><button class="button button-small" type="submit">Restock</button></form>
<form method="post" action="<?= esc(site_url('products/' . $product['id'] . '/delete'), 'attr') ?>" data-confirm="Delete this product?"><?= csrf_field() ?><button class="button button-small button-danger" type="submit">Delete</button></form></div></td></tr>
<?php endforeach; ?></tbody></table></div>
<?php endif; ?>
<?= $this->endSection() ?>
