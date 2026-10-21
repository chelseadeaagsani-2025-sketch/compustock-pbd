<?php

require __DIR__ . '/koneksi.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Margin Penjualan';
$activePage = 'margin';
$viewName = 'v_margin_penjualan';
$columns = [
    'idmargin_penjualan' => 'ID Margin',
    'persen' => 'Persentase',
    'status' => 'Status',
    'iduser' => 'ID User',
];
$rows = [];
$error = '';

try {
   
    $rows = $pdo->query('SELECT `idmargin_penjualan`, `persen`, `status`, `iduser` FROM `v_margin_penjualan` ORDER BY `idmargin_penjualan`')->fetchAll();
} catch (PDOException $e) {
    error_log('CompuStock margin view error: ' . $e->getMessage());
    $error = 'Data belum bisa ditampilkan. Periksa VIEW v_margin_penjualan di MySQL Workbench.';
}

require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/master_table.php';
require __DIR__ . '/includes/footer.php';
