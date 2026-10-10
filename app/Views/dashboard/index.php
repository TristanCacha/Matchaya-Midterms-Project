<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php
$spark = static function (array $values): string {
    $max = max(1, ...$values);
    $last = max(1, count($values) - 1);
    return implode(' ', array_map(static fn ($index, $value): string => round($index * 100 / $last, 1) . ',' . round(27 - $value * 23 / $max, 1), array_keys($values), $values));
};
$hourMax = max(1, ...$metrics['hours']);
$points = [];
foreach ($metrics['hours'] as $hour => $count) $points[] = round(12 + $hour * 29.7, 1) . ',' . round(172 - $count * 140 / $hourMax, 1);
$line = implode(' ', $points);
$peak = (int) array_search(max($metrics['hours']), $metrics['hours'], true);
$topMax = max(1, ...array_map(static fn ($row): int => (int) $row['units'], $metrics['top']));
?>
<div class="page-actions"><p>Today in Manila time. Revenue and volume are recorded sales.</p><a class="button button-primary" href="<?= esc(site_url('pos'), 'attr') ?>">New order</a></div>
<section class="metric-grid" aria-label="Today metrics">
    <?php foreach ([
        ['Revenue', peso($metrics['revenue']), $metrics['hourlyRevenue']],
        ['Orders', (string) $metrics['orders'], $metrics['hours']],
        ['Items sold', (string) $metrics['units'], $metrics['hourlyUnits']],
        ['Average ticket', peso($metrics['averageTicket']), array_map(static fn ($revenue, $orders) => $orders > 0 ? $revenue / $orders : 0, $metrics['hourlyRevenue'], $metrics['hours'])],
    ] as [$label, $value, $activity]): ?>
    <article><span><?= esc($label) ?></span><strong><?= esc($value) ?></strong><svg viewBox="0 0 100 30" class="sparkline" aria-hidden="true"><polyline points="<?= esc($spark($activity), 'attr') ?>"/></svg></article>
    <?php endforeach; ?>
</section>
<div class="dashboard-grid"><section class="data-panel"><h2>Orders by hour</h2><p>Today, 00:00–23:00 · Peak at <?= str_pad((string) $peak, 2, '0', STR_PAD_LEFT) ?>:00</p>
<div class="area-chart" role="img" aria-label="Orders by hour: <?php foreach ($metrics['hours'] as $hour => $count) echo $hour . 'h ' . $count . '; '; ?>"><svg viewBox="0 0 720 200" preserveAspectRatio="none"><path d="M12 172 L<?= esc(str_replace(' ', ' L', $line), 'attr') ?> L695 172 Z" fill="var(--fresh)" fill-opacity=".35"/><polyline points="<?= esc($line, 'attr') ?>" fill="none" stroke="var(--shade)" stroke-width="2" vector-effect="non-scaling-stroke"/><circle cx="<?= round(12 + $peak * 29.7, 1) ?>" cy="<?= round(172 - $metrics['hours'][$peak] * 140 / $hourMax, 1) ?>" r="5" fill="var(--shade)"/><text x="<?= round(12 + $peak * 29.7 + 12, 1) ?>" y="<?= round(172 - $metrics['hours'][$peak] * 140 / $hourMax - 10, 1) ?>" fill="var(--shade)" font-size="12" font-weight="700"><?= str_pad((string) $peak, 2, '0', STR_PAD_LEFT) ?>:00 peak</text></svg><div class="chart-labels"><span>00:00</span><span>06:00</span><span>12:00</span><span>18:00</span><span>23:00</span></div></div></section>
<section class="data-panel"><h2>Top sellers</h2><p>Last seven days</p><?php if ($metrics['top'] === []): ?><p>No sales yet.</p><?php else: ?><ol class="rank-list"><?php foreach ($metrics['top'] as $item): ?><li><img src="<?= esc(media_url($item['image'], 'products'), 'attr') ?>" alt="" width="36" height="36" loading="lazy" decoding="async"><span class="rank-info"><span><?= esc($item['name']) ?></span><span class="rank-bar"><span style="width: <?= (int) round((int) $item['units'] * 100 / $topMax) ?>%"></span></span></span><strong><?= (int) $item['units'] ?></strong></li><?php endforeach; ?></ol><?php endif; ?></section></div>
<div class="dashboard-grid"><section class="data-panel"><h2>Inventory runway</h2><p>Current stock divided by average daily units over 14 days.</p><ul class="runway-list"><?php foreach ($metrics['runway'] as $item): ?><li><span><?= esc($item['name']) ?></span><strong class="<?= $item['days'] !== null && $item['days'] < 3 ? 'risk-text' : '' ?>"><?= $item['days'] === null ? 'No recent sales' : esc((string) $item['days']) . ' days' ?></strong></li><?php endforeach; ?></ul></section><section class="data-panel"><h2>Customer patterns</h2><dl class="simple-stats"><div><dt>Repeat customer share</dt><dd><?= (int) $metrics['repeatShare'] ?>%</dd></div><div><dt>Food attach rate</dt><dd><?= $metrics['foodAttach'] === null ? 'Not available' : (int) $metrics['foodAttach'] . '%' ?></dd></div></dl><p class="field-note">Based on saved customers and grouped sale lines. Walk-ins are excluded from repeat share.</p></section></div>
<?= $this->endSection() ?>
