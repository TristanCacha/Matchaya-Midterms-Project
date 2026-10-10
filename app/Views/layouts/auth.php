<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#F2F5EE">
    <title><?= esc($title) ?> | Matchaya</title>
    <link rel="icon" type="image/svg+xml" href="<?= esc(base_url('assets/brand/favicon.svg'), 'attr') ?>">
    <link rel="stylesheet" href="<?= esc(base_url('assets/css/style.css?v=20261010-2'), 'attr') ?>">
</head>
<body class="auth-page">
<main class="auth-shell"><section class="auth-content">
    <a class="brand" href="<?= esc(site_url('/'), 'attr') ?>"><?= $this->include('partials/brand') ?></a>
    <div class="auth-card"><?= $this->renderSection('content') ?></div>
    <p class="auth-note">Matchaya · Made for the daily ritual</p>
</section><aside class="auth-visual"><img src="<?= esc(base_url('assets/brand/sign-in-hero.webp'), 'attr') ?>" alt="A chawan of finely whisked matcha beside a bamboo chasen" width="1536" height="1024" decoding="async" fetchpriority="high"><div class="auth-visual-copy"><span>THE COUNTER, MADE CALM</span><p>Good tea starts with attention.</p></div></aside></main>
</body>
</html>
