<?php

require __DIR__ . '/koneksi.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Data User';
$activePage = 'user';
$viewName = 'v_user';
$columns = [
    'iduser' => 'ID User',
    'username' => 'Username',
    'nama_role' => 'Role',
];
$rows = [];
$error = '';

try {
   
    $rows = $pdo->query('SELECT `iduser`, `username`, `nama_role` FROM `v_user` ORDER BY `iduser`')->fetchAll();
} catch (PDOException $e) {
    error_log('CompuStock user view error: ' . $e->getMessage());
    $error = 'Data belum bisa ditampilkan. Periksa VIEW v_user di MySQL Workbench.';
}

require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/master_table.php';
require __DIR__ . '/includes/footer.php';
