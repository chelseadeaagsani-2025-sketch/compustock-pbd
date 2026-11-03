<?php

$navItems = [
    'satuan' => ['Data Satuan', '▣', 'satuan.php'],
    'barang' => ['Data Barang', '▤', 'barang.php'],
    'vendor' => ['Data Vendor', '▥', 'vendor.php'],
    'user' => ['Data User', '◉', 'user.php'],
    'role' => ['Data Role', '◇', 'role.php'],
    'margin' => ['Margin Penjualan', '%', 'margin_penjualan.php'],
];

$viewItems = [
    'pengadaan' => ['Pengadaan', '▤'],
    'detail_pengadaan' => ['Detail Pengadaan', '▥'],
    'penerimaan' => ['Penerimaan', '▤'],
    'detail_penerimaan' => ['Detail Penerimaan', '▥'],
    'retur' => ['Retur', '▤'],
    'detail_retur' => ['Detail Retur', '▥'],
    'penjualan' => ['Penjualan', '▤'],
    'detail_penjualan' => ['Detail Penjualan', '▥'],
    'kartu_stok' => ['Kartu Stok', '▦'],
];
$activeView = $activePage === 'data_transaksi'
    ? (string) ($_GET['jenis'] ?? 'pengadaan')
    : '';
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($pageTitle) ?> - CompuStock</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="assets/addon.css">
    <style>
        
        .sidebar { overflow-y: auto; overscroll-behavior: contain; scrollbar-width: thin; }
    </style>
</head>
<body>
<div class="app">
    <aside class="sidebar">
        <a class="brand" href="index.php"><span class="brand-icon">▦</span><span>CompuStock<small>Manajemen Persediaan</small></span></a>
        <div class="nav-label">MENU UTAMA</div>
        <a class="nav-item <?= $activePage === 'dashboard' ? 'selected' : '' ?>" href="index.php"><span>◫</span> Dashboard</a>

        <div class="nav-label spaced">6 VIEW DATA MASTER</div>
        <?php foreach ($navItems as $key => $item): ?>
        <a class="nav-item <?= $activePage === $key ? 'selected' : '' ?>" href="<?= h($item[2]) ?>"><span><?= h($item[1]) ?></span><?= h($item[0]) ?></a>
        <?php endforeach; ?>

        <div class="nav-label spaced">9 VIEW TRANSAKSI PDM</div>
        <?php foreach ($viewItems as $key => $item): ?>
        <a class="nav-item <?= $activePage === 'data_transaksi' && $activeView === $key ? 'selected' : '' ?>" href="data_transaksi.php?jenis=<?= h($key) ?>"><span><?= h($item[1]) ?></span><?= h($item[0]) ?></a>
        <?php endforeach; ?>

        <div class="nav-label spaced">UJI INTEGRASI DATABASE</div>
        <a class="nav-item <?= $activePage === 'stored_procedure' ? 'selected' : '' ?>" href="sp.php"><span>⌕</span> Stored Procedure</a>
        <a class="nav-item <?= $activePage === 'function' ? 'selected' : '' ?>" href="function.php"><span>ƒ</span> Function</a>
        <a class="nav-item <?= $activePage === 'stok' ? 'selected' : '' ?>" href="transaksi_stok.php"><span>↕</span> Trigger Kartu Stok</a>

        <div class="sidebar-bottom"><span class="connection-dot"></span> Database: pbd_uts</div>
    </aside>
    <main class="content">
        <header class="topbar"><span>Proyek Praktikum Pemrograman Basis Data</span><span class="demo-label">15 VIEW PDM • MySQL</span></header>
        <section class="main-panel">
