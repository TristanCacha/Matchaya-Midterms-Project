<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-actions"><p>Use a unique username for each person.</p><a class="button" href="<?= esc(site_url('staff'), 'attr') ?>">Back to staff</a></div>
<?php if ($errors): ?><div class="form-errors" role="alert"><?php foreach ($errors as $error): ?><p><?= esc($error) ?></p><?php endforeach; ?></div><?php endif; ?>
<form class="editor-card form-stack" action="<?= esc($action, 'attr') ?>" method="post" enctype="multipart/form-data"><?= csrf_field() ?>
    <label for="full_name">Full name</label><input id="full_name" name="full_name" maxlength="100" value="<?= esc(old('full_name') ?? ($person['full_name'] ?? ''), 'attr') ?>" required>
    <label for="username">Username</label><input id="username" name="username" minlength="3" maxlength="50" value="<?= esc(old('username') ?? ($person['username'] ?? ''), 'attr') ?>" required>
    <label for="password">Password</label><input id="password" name="password" type="password" minlength="8" maxlength="72" autocomplete="new-password" <?= $person === null ? 'required' : '' ?>><p class="field-note"><?= $person === null ? 'Use at least eight characters.' : 'Leave blank to keep the current password.' ?></p>
    <?php if ($person && $person['avatar']): ?><img class="current-image avatar-preview" src="<?= esc(media_url($person['avatar'], 'avatars'), 'attr') ?>" alt="Current avatar"><?php endif; ?>
    <label for="avatar">Avatar</label><input id="avatar" name="avatar" type="file" accept="image/jpeg,image/png,image/webp"><p class="field-note">JPEG, PNG, or WebP, up to 2 MB. Initials appear when there is no avatar.</p>
    <button class="button button-primary" type="submit">Save staff member</button>
</form>
<?= $this->endSection() ?>
