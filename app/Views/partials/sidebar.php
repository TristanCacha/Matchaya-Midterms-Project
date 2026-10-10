<aside class="sidebar" aria-label="Main navigation">
    <a class="brand" href="<?= esc(site_url('pos'), 'attr') ?>"><?= $this->include('partials/brand') ?></a>
    <nav class="side-nav">
        <?php foreach ([['pos', 'Counter', 'pos', 'counter'], ['dashboard', 'Dashboard', 'dashboard', 'chart'], ['products', 'Products', 'products', 'box'], ['customers', 'Customers', 'customers', 'users'], ['sales', 'Sales history', 'sales', 'receipt'], ['staff', 'Staff', 'staff', 'staff'], ['about', 'About', 'about', 'info']] as [$path, $label, $page, $icon]): ?>
            <a class="nav-link <?= $activePage === $page ? 'is-active' : '' ?>" href="<?= esc(site_url($path), 'attr') ?>" <?= $activePage === $page ? 'aria-current="page"' : '' ?>><svg class="icon" aria-hidden="true"><use href="#i-<?= esc($icon, 'attr') ?>"/></svg><?= esc($label) ?></a>
        <?php endforeach; ?>
    </nav>
    <div class="sidebar-bottom">
        <?php if ($sidebarUser): ?>
            <a class="account-link" href="<?= esc(site_url('account'), 'attr') ?>">
                <?php if (! empty($sidebarUser['avatar'])): ?><img class="avatar" src="<?= esc(media_url($sidebarUser['avatar'], 'avatars'), 'attr') ?>" alt=""><?php else: ?><span class="avatar initials"><?= esc(initials($sidebarUser['full_name'])) ?></span><?php endif; ?>
                <span><strong><?= esc($sidebarUser['full_name']) ?></strong><small>My account</small></span>
            </a>
        <?php endif; ?>
        <form action="<?= esc(site_url('logout'), 'attr') ?>" method="post"><?= csrf_field() ?><button class="text-button" type="submit">Sign out</button></form>
    </div>
</aside>
