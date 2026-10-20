<?php
// Salin file ini menjadi config.php untuk konfigurasi lokal Laragon.
// Jangan unggah config.php bila berisi password database asli.
return [
    'host' => '127.0.0.1',
    'port' => '3306',
    'database' => 'pbd_uts',
    'username' => 'root',
    'password' => '', // Laragon biasanya kosong; sesuaikan bila berbeda.
];
