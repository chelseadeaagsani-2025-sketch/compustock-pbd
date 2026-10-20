<?php
require __DIR__ . '/koneksi.php';

// Daftar halaman dan query dibuat tetap (whitelist), bukan dari input pengguna.
// Dengan begitu nama tabel/VIEW tidak bisa disisipkan lewat URL.
$masters = [
    'satuan' => [
        'judul' => 'Data Satuan', 'view' => 'v_satuan', 'ikon' => '▣',
        'kolom' => ['idsatuan' => 'ID Satuan', 'nama_satuan' => 'Nama Satuan', 'status' => 'Status'],
    ],
    'barang' => [
        'judul' => 'Data Barang', 'view' => 'v_barang', 'ikon' => '▤',
        'kolom' => ['idbarang' => 'ID Barang', 'jenis' => 'Jenis', 'nama' => 'Nama Barang',
                    'nama_satuan' => 'Satuan', 'status' => 'Status', 'harga' => 'Harga'],
    ],
    'vendor' => [
        'judul' => 'Data Vendor', 'view' => 'v_vendor', 'ikon' => '▥',
        'kolom' => ['idvendor' => 'ID Vendor', 'nama_vendor' => 'Nama Vendor',
                    'badan_hukum' => 'Badan Hukum', 'status' => 'Status'],
    ],
    'user' => [
        'judul' => 'Data User', 'view' => 'v_user', 'ikon' => '◉',
        // PENTING: password TIDAK diambil dan TIDAK ditampilkan.
        'kolom' => ['iduser' => 'ID User', 'username' => 'Username', 'nama_role' => 'Role'],
    ],
    'role' => [
        'judul' => 'Data Role', 'view' => 'v_role', 'ikon' => '◇',
        'kolom' => ['idrole' => 'ID Role', 'nama_role' => 'Nama Role'],
    ],
    'margin' => [
        'judul' => 'Margin Penjualan', 'view' => 'v_margin_penjualan', 'ikon' => '%',
        'kolom' => ['idmargin_penjualan' => 'ID Margin', 'persen' => 'Persen',
                    'status' => 'Status', 'iduser' => 'ID User'],
    ],
];

function e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function tampilkanNilai($namaKolom, $nilai, $halaman) {
    if ($nilai === null) return '<span class="muted">—</span>';
    if ($namaKolom === 'status') {
        $aktif = ($halaman === 'vendor') ? (strtoupper((string)$nilai) === 'A') : ((string)$nilai === '1');
        return '<span class="badge ' . ($aktif ? 'aktif' : 'nonaktif') . '">' . ($aktif ? 'Aktif' : 'Tidak aktif') . '</span>';
    }
    if ($namaKolom === 'harga') return 'Rp ' . e(number_format((float)$nilai, 0, ',', '.'));
    if ($namaKolom === 'persen') return e(number_format((float)$nilai, 0, ',', '.')) . '%';
    if ($namaKolom === 'badan_hukum') {
        return e(['P' => 'PT', 'C' => 'CV'][$nilai] ?? $nilai);
    }
    return e($nilai);
}

$halaman = $_GET['page'] ?? 'dashboard';
if ($halaman !== 'dashboard' && !array_key_exists($halaman, $masters)) {
    http_response_code(404);
    $halaman = 'dashboard';
}

$error = '';
$rows = [];
$counts = [];
try {
    if ($halaman === 'dashboard') {
        foreach ($masters as $kode => $master) {
            $counts[$kode] = (int) $pdo->query('SELECT COUNT(*) FROM `' . $master['view'] . '`')->fetchColumn();
        }
    } else {
        $master = $masters[$halaman];
        // SELECT hanya kolom yang akan terlihat. Khusus user: tidak ada password.
        $columns = implode(', ', array_map(static fn($key) => '`' . $key . '`', array_keys($master['kolom'])));
        $rows = $pdo->query('SELECT ' . $columns . ' FROM `' . $master['view'] . '`')->fetchAll();
    }
} catch (PDOException $e) {
    $error = 'Data belum bisa ditampilkan. Periksa apakah enam VIEW sudah dibuat dan query-nya dapat dijalankan di HeidiSQL.';
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($halaman === 'dashboard' ? 'Dashboard' : $masters[$halaman]['judul']) ?> - CompuStock</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="app">
    <aside class="sidebar">
        <a class="brand" href="index.php"><span class="brand-icon">▦</span><span>CompuStock<small>Manajemen Persediaan</small></span></a>
        <div class="nav-label">MENU UTAMA</div>
        <a class="nav-item <?= $halaman === 'dashboard' ? 'selected' : '' ?>" href="index.php"><span>◫</span> Dashboard</a>
        <div class="nav-label spaced">ENAM DATA MASTER</div>
        <?php foreach ($masters as $kode => $info): ?>
        <a class="nav-item <?= $halaman === $kode ? 'selected' : '' ?>" href="?page=<?= e($kode) ?>"><span><?= e($info['ikon']) ?></span> <?= e($info['judul']) ?></a>
        <?php endforeach; ?>
        <div class="sidebar-bottom"><span class="connection-dot"></span> Database: pbd_uts</div>
    </aside>
    <main class="content">
        <header class="topbar"><span>Proyek Praktikum Pemrograman Basis Data</span><span class="demo-label">Versi awal • Data Master</span></header>
        <section class="main-panel">
            <div class="eyebrow">COMPUSTOCK / <?= $halaman === 'dashboard' ? 'BERANDA' : 'DATA MASTER' ?></div>
            <h1><?= e($halaman === 'dashboard' ? 'Dashboard' : $masters[$halaman]['judul']) ?></h1>
            <p class="intro"><?= $halaman === 'dashboard' ? 'Ringkasan enam data master dari database pbd_uts.' : 'Data berikut diambil langsung dari VIEW MySQL, bukan diketik manual di website.' ?></p>
            <?php if ($error !== ''): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
            <?php if ($halaman === 'dashboard'): ?>
              <div class="card-grid">
                  <?php foreach ($masters as $kode => $info): ?>
                    <a class="stat-card" href="?page=<?= e($kode) ?>">
                        <span class="stat-top"><span class="stat-icon"><?= e($info['ikon']) ?></span><span class="stat-arrow">↗</span></span>
                        <strong><?= isset($counts[$kode]) ? e($counts[$kode]) : '—' ?></strong>
                        <span class="stat-label"><?= e($info['judul']) ?></span>
                        <small>Sumber: <?= e($info['view']) ?></small>
                    </a>
                  <?php endforeach; ?>
              </div>
              <div class="info-card"><strong>Cara mencoba aplikasi</strong><p>Klik salah satu menu di sebelah kiri. Data akan diambil dari VIEW <code>v_...</code> yang sudah dibuat saat UTS.</p></div>
            <?php else: ?>
              <div class="table-card">
                <div class="table-header"><div><strong>Daftar <?= e($masters[$halaman]['judul']) ?></strong><small>VIEW: <?= e($masters[$halaman]['view']) ?></small></div><span class="count"><?= count($rows) ?> data</span></div>
                <div class="table-wrap"><table><thead><tr>
                  <?php foreach ($masters[$halaman]['kolom'] as $nama): ?><th><?= e($nama) ?></th><?php endforeach; ?>
                </tr></thead><tbody>
                  <?php if (count($rows) === 0): ?><tr><td colspan="<?= count($masters[$halaman]['kolom']) ?>" class="empty">Belum ada data untuk ditampilkan.</td></tr><?php endif; ?>
                  <?php foreach ($rows as $row): ?><tr>
                    <?php foreach ($masters[$halaman]['kolom'] as $key => $nama): ?><td><?= tampilkanNilai($key, $row[$key] ?? null, $halaman) ?></td><?php endforeach; ?>
                  </tr><?php endforeach; ?>
                </tbody></table></div>
              </div>
              <div class="hint">Halaman ini baru menampilkan data (Read). Tombol Tambah, Edit, dan Hapus belum dibuat.</div>
            <?php endif; ?>
        </section>
    </main>
</div>
</body>
</html>
