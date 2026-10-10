<nav class="mobile-nav" aria-label="Mobile navigation">
    <?php foreach ([['pos', 'Counter', 'pos'], ['dashboard', 'Dashboard', 'dashboard'], ['products', 'Products', 'products'], ['customers', 'Customers', 'customers'], ['sales', 'Sales', 'sales']] as [$path, $label, $page]): ?>
        <a class="mobile-nav-link <?= $activePage === $page ? 'is-active' : '' ?>" href="<?= esc(site_url($path), 'attr') ?>" <?= $activePage === $page ? 'aria-current="page"' : '' ?>><?= esc($label) ?></a>
    <?php endforeach; ?>
</nav>
