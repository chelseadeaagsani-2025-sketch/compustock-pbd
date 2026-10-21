<?php

require __DIR__ . '/koneksi.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Data Role';
$activePage = 'role';
$viewName = 'v_role';
$columns = [
    'idrole' => 'ID Role',
    'nama_role' => 'Nama Role',
];
$rows = [];
$error = '';

try {
    
    $rows = $pdo->query('SELECT `idrole`, `nama_role` FROM `v_role` ORDER BY `idrole`')->fetchAll();
} catch (PDOException $e) {
    error_log('CompuStock role view error: ' . $e->getMessage());
    $error = 'Data belum bisa ditampilkan. Periksa VIEW v_role di MySQL Workbench.';
}

require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/master_table.php';
require __DIR__ . '/includes/footer.php';
