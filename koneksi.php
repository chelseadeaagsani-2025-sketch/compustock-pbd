<?php
// File koneksi MySQL untuk seluruh halaman CompuStock.
$configFile = __DIR__ . '/config.php';
if (!is_file($configFile)) {
    die('Belum ada config.php. Salin config.example.php lalu beri nama config.php.');
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
    http_response_code(500);
    die('Error koneksi: ' . htmlspecialchars(
        $e->getMessage(),
        ENT_QUOTES,
        'UTF-8'
    ));
}
