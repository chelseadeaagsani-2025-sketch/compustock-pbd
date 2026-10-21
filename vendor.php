<?php

require __DIR__ . '/koneksi.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Data Vendor';
$activePage = 'vendor';
$viewName = 'v_vendor';
$columns = [
    'idvendor' => 'ID Vendor',
    'nama_vendor' => 'Nama Vendor',
    'badan_hukum' => 'Badan Hukum',
    'status' => 'Status',
];
$rows = [];
$error = '';

try {
   
    $rows = $pdo->query('SELECT `idvendor`, `nama_vendor`, `badan_hukum`, `status` FROM `v_vendor` ORDER BY `idvendor`')->fetchAll();
} catch (PDOException $e) {
    error_log('CompuStock vendor view error: ' . $e->getMessage());
    $error = 'Data belum bisa ditampilkan. Periksa VIEW v_vendor di MySQL Workbench.';
}

require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/master_table.php';
require __DIR__ . '/includes/footer.php';
