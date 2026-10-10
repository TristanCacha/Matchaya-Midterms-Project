<?= $this->extend('layouts/auth') ?>
<?= $this->section('content') ?>
<p class="eyebrow">First setup</p><h1>Create the first staff account</h1><p>Use this account to add products and manage the counter. Setup closes after the account is created.</p>
<?php $errors = session()->getFlashdata('formErrors') ?? []; ?>
<?php if ($errors): ?><div class="form-errors" role="alert"><?php foreach ($errors as $error): ?><p><?= esc($error) ?></p><?php endforeach; ?></div><?php endif; ?>
<form class="form-stack" action="<?= esc(site_url('setup'), 'attr') ?>" method="post">
    <?= csrf_field() ?>
    <label for="full_name">Full name</label><input id="full_name" name="full_name" maxlength="100" value="<?= esc(old('full_name') ?? '', 'attr') ?>" required>
    <label for="username">Username</label><input id="username" name="username" minlength="3" maxlength="50" value="<?= esc(old('username') ?? '', 'attr') ?>" required>
    <label for="password">Password</label><input id="password" name="password" type="password" minlength="8" maxlength="72" autocomplete="new-password" required>
    <label for="password_confirm">Confirm password</label><input id="password_confirm" name="password_confirm" type="password" minlength="8" maxlength="72" autocomplete="new-password" required>
    <button class="button button-primary" type="submit">Create account</button>
</form>
<?= $this->endSection() ?>
