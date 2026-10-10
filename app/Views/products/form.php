<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-actions"><p>Keep prices, stock, and descriptions clear for staff.</p><a class="button" href="<?= esc(site_url('products'), 'attr') ?>">Back to products</a></div>
<?php if ($errors): ?><div class="form-errors" role="alert"><?php foreach ($errors as $error): ?><p><?= esc($error) ?></p><?php endforeach; ?></div><?php endif; ?>
<form class="editor-card form-stack" action="<?= esc($action, 'attr') ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <label for="name">Product name</label><input id="name" name="name" maxlength="100" value="<?= esc(old('name') ?? ($product['name'] ?? ''), 'attr') ?>" required>
    <div class="form-grid"><div><label for="price">Price in pesos</label><input id="price" name="price" type="number" min="0" max="99999999.99" step="0.01" value="<?= esc(old('price') ?? ($product['price'] ?? ''), 'attr') ?>" required></div><div><label for="stock_quantity">Stock quantity</label><input id="stock_quantity" name="stock_quantity" type="number" min="0" step="1" value="<?= esc(old('stock_quantity') ?? ($product['stock_quantity'] ?? '0'), 'attr') ?>" required></div></div>
    <div class="form-grid"><div><label for="category">Category</label><input id="category" name="category" maxlength="40" value="<?= esc(old('category') ?? ($product['category'] ?? 'Milk drinks'), 'attr') ?>" list="category-list"><datalist id="category-list"><option value="Straight"><option value="Milk drinks"><option value="Hojicha"><option value="Seasonal"><option value="Food"><option value="Retail"></datalist></div><div><label for="description">Short description</label><input id="description" name="description" maxlength="160" value="<?= esc(old('description') ?? ($product['description'] ?? ''), 'attr') ?>"></div></div>
    <label class="checkbox-line"><input type="checkbox" name="is_featured" value="1" <?= (old('is_featured') ?? ($product['is_featured'] ?? 0)) ? 'checked' : '' ?>> Feature on the counter</label>
    <?php if (! empty($product['image'])): ?><img class="current-image" src="<?= esc(media_url($product['image'], 'products'), 'attr') ?>" alt="Current product image"><?php endif; ?>
    <label for="image">Product image</label><input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp"><p class="field-note">JPEG, PNG, or WebP, up to 2 MB. A new image replaces the old one.</p>
    <button class="button button-primary" type="submit">Save product</button>
</form>
<?= $this->endSection() ?>
