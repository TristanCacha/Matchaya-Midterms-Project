<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-actions"><p>Contact details are optional for walk-in sales.</p><a class="button" href="<?= esc(site_url('customers'), 'attr') ?>">Back to customers</a></div>
<?php if ($errors): ?><div class="form-errors" role="alert"><?php foreach ($errors as $error): ?><p><?= esc($error) ?></p><?php endforeach; ?></div><?php endif; ?>
<form class="editor-card form-stack" action="<?= esc($action, 'attr') ?>" method="post"><?= csrf_field() ?>
    <label for="full_name">Full name</label><input id="full_name" name="full_name" maxlength="100" value="<?= esc(old('full_name') ?? ($customer['full_name'] ?? ''), 'attr') ?>" required>
    <label for="email">Email</label><input id="email" name="email" type="email" maxlength="100" value="<?= esc(old('email') ?? ($customer['email'] ?? ''), 'attr') ?>" required>
    <label for="phone">Phone</label><input id="phone" name="phone" maxlength="20" value="<?= esc(old('phone') ?? ($customer['phone'] ?? ''), 'attr') ?>">
    <button class="button button-primary" type="submit">Save customer</button>
</form>
<?= $this->endSection() ?>
