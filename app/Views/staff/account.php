<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php if ($person): ?><section class="editor-card account-profile"><div class="account-head"><?php if ($person['avatar']): ?><img class="account-avatar" src="<?= esc(media_url($person['avatar'], 'avatars'), 'attr') ?>" alt=""><?php else: ?><span class="account-avatar initials"><?= esc(initials($person['full_name'])) ?></span><?php endif; ?><div><h2><?= esc($person['full_name']) ?></h2><p>@<?= esc($person['username']) ?></p></div></div><p>Staff since <?= esc(date('j F Y', strtotime($person['created_at']))) ?></p><a class="button" href="<?= esc(site_url('staff/' . $person['id'] . '/edit'), 'attr') ?>">Edit my account</a></section><?php endif; ?>
<?= $this->endSection() ?>
