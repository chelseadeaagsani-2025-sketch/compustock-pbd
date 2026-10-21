<?php

require __DIR__ . '/koneksi.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Dashboard';
$activePage = 'dashboard';
$masterCards = [
    ['title' => 'Data Satuan', 'view' => 'v_satuan', 'icon' => '▣', 'file' => 'satuan.php'],
    ['title' => 'Data Barang', 'view' => 'v_barang', 'icon' => '▤', 'file' => 'barang.php'],
    ['title' => 'Data Vendor', 'view' => 'v_vendor', 'icon' => '▥', 'file' => 'vendor.php'],
    ['title' => 'Data User', 'view' => 'v_user', 'icon' => '◉', 'file' => 'user.php'],
    ['title' => 'Data Role', 'view' => 'v_role', 'icon' => '◇', 'file' => 'role.php'],
    ['title' => 'Margin Penjualan', 'view' => 'v_margin_penjualan', 'icon' => '%', 'file' => 'margin_penjualan.php'],
];

$error = '';
try {
   
    foreach ($masterCards as &$card) {
        $card['count'] = (int) $pdo->query('SELECT COUNT(*) FROM `' . $card['view'] . '`')->fetchColumn();
    }
    unset($card);
} catch (PDOException $e) {
    error_log('CompuStock dashboard query error: ' . $e->getMessage());
    $error = 'Ringkasan belum bisa diambil. Periksa enam VIEW di database pbd_uts.';
}

require __DIR__ . '/includes/header.php';
?>
<div class="eyebrow">COMPUSTOCK / BERANDA</div>
<h1>Dashboard</h1>
<p class="intro">Ringkasan enam data master dari database pbd_uts.</p>
<?php if ($error !== ''): ?><div class="alert"><?= h($error) ?></div><?php endif; ?>
<div class="card-grid">
    <?php foreach ($masterCards as $card): ?>
        <a class="stat-card" href="<?= h($card['file']) ?>">
            <span class="stat-top"><span class="stat-icon"><?= h($card['icon']) ?></span><span class="stat-arrow">↗</span></span>
            <strong><?= isset($card['count']) ? h($card['count']) : '—' ?></strong>
            <span class="stat-label"><?= h($card['title']) ?></span>
            <small>Sumber: <?= h($card['view']) ?></small>
        </a>
    <?php endforeach; ?>
</div>
<div class="info-card"><strong>Informasi aplikasi</strong><p>Pilih menu di sebelah kiri untuk membuka halaman data master. Setiap halaman memiliki file PHP sendiri dan membaca VIEW MySQL yang sudah dibuat.</p></div>
<?php require __DIR__ . '/includes/footer.php'; ?>
