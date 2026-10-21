<?php

require __DIR__ . '/koneksi.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Data Satuan';
$activePage = 'satuan';
$viewName = 'v_satuan';
$columns = [
    'idsatuan' => 'ID Satuan',
    'nama_satuan' => 'Nama Satuan',
    'status' => 'Status',
];
$rows = [];
$error = '';

try {
    
    $rows = $pdo->query('SELECT `idsatuan`, `nama_satuan`, `status` FROM `v_satuan` ORDER BY `idsatuan`')->fetchAll();
} catch (PDOException $e) {
    error_log('CompuStock satuan view error: ' . $e->getMessage());
    $error = 'Data belum bisa ditampilkan. Periksa VIEW v_satuan di MySQL Workbench.';
}

require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/master_table.php';
require __DIR__ . '/includes/footer.php';
