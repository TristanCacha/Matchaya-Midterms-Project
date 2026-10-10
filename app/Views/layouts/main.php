<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#F2F5EE">
    <title><?= esc($title) ?> | Matchaya</title>
    <link rel="icon" type="image/svg+xml" href="<?= esc(base_url('assets/brand/favicon.svg'), 'attr') ?>">
    <link rel="stylesheet" href="<?= esc(base_url('assets/css/style.css?v=20261010-2'), 'attr') ?>">
    <script src="<?= esc(base_url('assets/js/app.js'), 'attr') ?>" defer></script>
</head>
<body>
<?= $this->include('partials/icons') ?>
<a class="skip-link" href="#main-content">Skip to content</a>
<div class="app-shell">
    <?= $this->include('partials/sidebar') ?>
    <div class="workspace">
        <?= $this->include('partials/header') ?>
        <main id="main-content" class="main-content" tabindex="-1">
            <?= $this->include('partials/flash') ?>
            <?= $this->renderSection('content') ?>
        </main>
        <?= $this->include('partials/footer') ?>
    </div>
</div>
<?= $this->include('partials/mobile_nav') ?>
</body>
</html>
