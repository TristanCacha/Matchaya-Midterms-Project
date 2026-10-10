<?= $this->extend('layouts/auth') ?>
<?= $this->section('content') ?>
<p class="eyebrow">Staff access</p><h1>Sign in</h1><p>Enter your staff account to open the counter.</p>
<?php if ($message = session()->getFlashdata('authError')): ?><div class="flash-message flash-error" role="alert"><?= esc($message) ?></div><?php endif; ?>
<form class="form-stack" action="<?= esc(site_url('login'), 'attr') ?>" method="post">
    <?= csrf_field() ?>
    <label for="username">Username</label><input id="username" name="username" type="text" autocomplete="username" value="<?= esc(old('username') ?? '', 'attr') ?>" required autofocus>
    <label for="password">Password</label><input id="password" name="password" type="password" autocomplete="current-password" required>
    <button class="button button-primary" type="submit">Sign in</button>
</form>
<?= $this->endSection() ?>
