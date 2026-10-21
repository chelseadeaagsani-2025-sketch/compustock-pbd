<?php

$configFile = __DIR__ . '/config.php';
if (!is_file($configFile)) {
    http_response_code(500);
    exit('File config.php belum ditemukan. Gunakan config.php lokal yang sebelumnya sudah berhasil.');
}

$config = require $configFile;

try {
    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
        $config['host'],
        $config['port'],
        $config['database']
    );
    $pdo = new PDO($dsn, $config['username'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    error_log('CompuStock DB connection error: ' . $e->getMessage());
    http_response_code(500);
    exit('Koneksi database gagal. Periksa MySQL dan pengaturan config.php lokal.');
}
