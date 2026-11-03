<?php
require __DIR__ . '/koneksi.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Stored Procedure';
$activePage = 'stored_procedure';
$barangSemua = [];
$barangSatuan = [];
$satuanList = [];
$error = '';
$selectedSatuan = trim((string) ($_GET['satuan'] ?? 'PCS'));

try {
    // Isi dropdown satuan dibaca dari VIEW master.
    $satuanList = $pdo->query('SELECT nama_satuan FROM v_satuan WHERE status = 1 ORDER BY idsatuan')->fetchAll();
    $allowed = array_column($satuanList, 'nama_satuan');
    if (!in_array($selectedSatuan, $allowed, true)) {
        $selectedSatuan = $allowed[0] ?? '';
    }

    // Stored Procedure 1: seluruh barang, dibaca oleh SP dari v_barang.
    $stmt = $pdo->query('CALL sp_tampil_barang()');
    $barangSemua = $stmt->fetchAll();
    $stmt->closeCursor();

    // Stored Procedure 2: barang berdasarkan satuan terpilih.
    if ($selectedSatuan !== '') {
        $stmt = $pdo->prepare('CALL sp_barang_per_satuan(?)');
        $stmt->execute([$selectedSatuan]);
        $barangSatuan = $stmt->fetchAll();
        $stmt->closeCursor();
    }
} catch (Throwable $e) {
    error_log('CompuStock Stored Procedure: ' . $e->getMessage());
    $error = $e instanceof PDOException
        ? 'Stored Procedure belum tersedia atau query gagal. Periksa SP dan koneksi MySQL.'
        : $e->getMessage();
}

require __DIR__ . '/includes/header.php';
?>
<div class="eyebrow">COMPUSTOCK / STORED PROCEDURE</div>
<h1>Stored Procedure</h1>
<p class="intro">Pengujian 2 Stored Procedure MySQL yang membaca data barang melalui VIEW.</p>
<?php if ($error): ?><div class="alert"><?= h($error) ?></div><?php endif; ?>

<div class="table-card demo-card">
    <div class="table-header">
        <div><strong>SP 1 · sp_tampil_barang()</strong><small>CALL sp_tampil_barang() — menggunakan v_barang</small></div>
        <span class="count"><?= count($barangSemua) ?> barang</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>ID</th><th>Nama barang</th><th>Satuan</th><th>Harga</th></tr></thead>
            <tbody>
            <?php foreach ($barangSemua as $b): ?>
                <tr><td><?= h($b['idbarang']) ?></td><td><?= h($b['nama']) ?></td><td><?= h($b['nama_satuan']) ?></td><td>Rp <?= h(number_format((int) $b['harga'], 0, ',', '.')) ?></td></tr>
            <?php endforeach; ?>
            <?php if (!$barangSemua): ?><tr><td colspan="4">Belum ada data barang untuk ditampilkan.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="table-card demo-card">
    <div class="table-header"><div><strong>SP 2 · sp_barang_per_satuan()</strong><small>CALL sp_barang_per_satuan(?) — menggunakan v_barang</small></div></div>
    <form class="demo-form" method="get" action="sp.php">
        <label>Satuan
            <select name="satuan">
                <?php foreach ($satuanList as $s): ?>
                    <option value="<?= h($s['nama_satuan']) ?>" <?= $selectedSatuan === $s['nama_satuan'] ? 'selected' : '' ?>><?= h($s['nama_satuan']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <button type="submit">Tampilkan barang</button>
    </form>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Nama barang</th><th>Satuan</th><th>Harga</th></tr></thead>
            <tbody>
            <?php foreach ($barangSatuan as $b): ?>
                <tr><td><?= h($b['nama']) ?></td><td><?= h($b['nama_satuan']) ?></td><td>Rp <?= h(number_format((int) $b['harga'], 0, ',', '.')) ?></td></tr>
            <?php endforeach; ?>
            <?php if (!$barangSatuan): ?><tr><td colspan="3">Belum ada barang untuk satuan ini.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
