<?php
// Diperlukan: $pageTitle, $activePage, dan fungsi h().
$navItems = [
    'satuan' => ['Data Satuan', '▣', 'satuan.php'],
    'barang' => ['Data Barang', '▤', 'barang.php'],
    'vendor' => ['Data Vendor', '▥', 'vendor.php'],
    'user' => ['Data User', '◉', 'user.php'],
    'role' => ['Data Role', '◇', 'role.php'],
    'margin' => ['Margin Penjualan', '%', 'margin_penjualan.php'],
];
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($pageTitle) ?> - CompuStock</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="app">
    <aside class="sidebar">
        <a class="brand" href="index.php"><span class="brand-icon">▦</span><span>CompuStock<small>Manajemen Persediaan</small></span></a>
        <div class="nav-label">MENU UTAMA</div>
        <a class="nav-item <?= $activePage === 'dashboard' ? 'selected' : '' ?>" href="index.php"><span>◫</span> Dashboard</a>
        <div class="nav-label spaced">ENAM DATA MASTER</div>
        <?php foreach ($navItems as $key => $item): ?>
        <a class="nav-item <?= $activePage === $key ? 'selected' : '' ?>" href="<?= h($item[2]) ?>"><span><?= h($item[1]) ?></span><?= h($item[0]) ?></a>
        <?php endforeach; ?>
        <div class="sidebar-bottom"><span class="connection-dot"></span> Database: pbd_uts</div>
    </aside>
    <main class="content">
        <header class="topbar"><span>Proyek Praktikum Pemrograman Basis Data</span><span class="demo-label">6 Data Master • VIEW MySQL</span></header>
        <section class="main-panel">
