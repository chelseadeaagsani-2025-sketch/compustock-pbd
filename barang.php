<?php

require __DIR__ . '/koneksi.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Data Barang';
$activePage = 'barang';
$viewName = 'v_barang';
$columns = [
    'idbarang' => 'ID Barang',
    'jenis' => 'Jenis',
    'nama' => 'Nama Barang',
    'nama_satuan' => 'Satuan',
    'status' => 'Status',
    'harga' => 'Harga',
];
$rows = [];
$error = '';

try {
    
    $rows = $pdo->query('SELECT `idbarang`, `jenis`, `nama`, `nama_satuan`, `status`, `harga` FROM `v_barang` ORDER BY `idbarang`')->fetchAll();
} catch (PDOException $e) {
    error_log('CompuStock barang view error: ' . $e->getMessage());
    $error = 'Data belum bisa ditampilkan. Periksa VIEW v_barang di MySQL Workbench.';
}

require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/master_table.php';
require __DIR__ . '/includes/footer.php';
