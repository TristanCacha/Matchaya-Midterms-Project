<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-actions"><p><?= esc($customer['email']) ?> · <?= esc($customer['phone'] ?: 'No phone') ?></p><a class="button" href="<?= esc(site_url('customers/' . $customer['id'] . '/edit'), 'attr') ?>">Edit customer</a></div>
<section class="editor-card"><h2>Stamp card</h2><p>One welcome stamp plus one per drink. Redemption is not recorded.</p><p class="stamp-count"><strong><?= min(10, 1 + $units) ?></strong> of 10 stamps</p><div class="stamp-grid" aria-label="<?= min(10, 1 + $units) ?> of 10 stamps"><?php for ($i = 1; $i <= 10; $i++): ?><span class="stamp <?= $i <= min(10, 1 + $units) ? 'filled' : '' ?>"><?= $i === 1 ? 'W' : $i ?></span><?php endfor; ?></div><p class="field-note">W is the welcome stamp. Drink units: <?= (int) $units ?>.</p></section>
<?= $this->endSection() ?>
