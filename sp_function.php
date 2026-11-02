<?php

require __DIR__ . '/koneksi.php';
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'SP & Function';
$activePage = 'routines';
$barangSemua = [];
$barangSatuan = [];
$satuanList = [];
$hasilHitung = null;
$error = '';
$selectedSatuan = trim((string) ($_GET['satuan'] ?? 'PCS'));
$harga = (int) ($_GET['harga'] ?? 750000);
$jumlah = (int) ($_GET['jumlah'] ?? 2);

try {
    $satuanList = $pdo->query('SELECT nama_satuan FROM v_satuan WHERE status = 1 ORDER BY idsatuan')->fetchAll();
    $allowed = array_column($satuanList, 'nama_satuan');
    if (!in_array($selectedSatuan, $allowed, true)) {
        $selectedSatuan = $allowed[0] ?? '';
    }

    // Stored Procedure ke-1: berdasarkan VIEW v_barang.
    $stmt = $pdo->query('CALL sp_tampil_barang()');
    $barangSemua = $stmt->fetchAll();
    $stmt->closeCursor();

    // Stored Procedure ke-2: filter satuan.
    if ($selectedSatuan !== '') {
        $stmt = $pdo->prepare('CALL sp_barang_per_satuan(?)');
        $stmt->execute([$selectedSatuan]);
        $barangSatuan = $stmt->fetchAll();
        $stmt->closeCursor();
    }

    if (isset($_GET['hitung'])) {
        if ($harga <= 0 || $harga > 200000000 || $jumlah < 1 || $jumlah > 100) {
            throw new RuntimeException('Harga harus positif dan jumlah 1–100.');
        }
        $marginRows = $pdo->query('SELECT idmargin_penjualan, persen FROM v_margin_penjualan WHERE status = 1')->fetchAll();
        if (count($marginRows) !== 1) {
            throw new RuntimeException('Harus ada tepat satu margin aktif di database.');
        }
        $persen = (float) $marginRows[0]['persen'];
        // Function lama dan Function baru benar-benar dihitung oleh database.
        $stmt = $pdo->prepare('SELECT fn_total_harga(?, ?) AS total_awal, fn_harga_jual(?, ?) AS harga_jual');
        $stmt->execute([$harga, $jumlah, $harga, $persen]);
        $hasilHitung = $stmt->fetch();
        $hasilHitung['margin'] = $persen;
    }
} catch (Throwable $e) {
    error_log('CompuStock SP/Function: ' . $e->getMessage());
    $error = $e instanceof PDOException ? 'SP / Function belum tersedia atau query gagal. Jalankan SQL lanjutan di MySQL Workbench.' : $e->getMessage();
}

require __DIR__ . '/includes/header.php';
?>
<div class="eyebrow">COMPUSTOCK / ROUTINE MYSQL</div>
<h1>Stored Procedure &amp; Function</h1>
<p class="intro">Demo 2 Stored Procedure dan 2 Function yang dijalankan langsung oleh MySQL.</p>
<?php if ($error): ?><div class="alert"><?= h($error) ?></div><?php endif; ?>
<div class="table-card demo-card">
  <div class="table-header"><div><strong>SP 1 · sp_tampil_barang()</strong><small>CALL sp_tampil_barang() — sumber v_barang</small></div><span class="count"><?= count($barangSemua) ?> barang</span></div>
  <div class="table-wrap"><table><thead><tr><th>ID</th><th>Nama barang</th><th>Satuan</th><th>Harga</th></tr></thead><tbody>
  <?php foreach ($barangSemua as $b): ?><tr><td><?= h($b['idbarang']) ?></td><td><?= h($b['nama']) ?></td><td><?= h($b['nama_satuan']) ?></td><td>Rp <?= h(number_format((int)$b['harga'],0,',','.')) ?></td></tr><?php endforeach; ?>
  </tbody></table></div>
</div>
<div class="table-card demo-card">
  <div class="table-header"><div><strong>SP 2 · sp_barang_per_satuan()</strong><small>CALL sp_barang_per_satuan(?) — sumber v_barang</small></div></div>
  <form class="demo-form" method="get">
    <label>Satuan <select name="satuan"><?php foreach ($satuanList as $s): ?><option value="<?= h($s['nama_satuan']) ?>" <?= $selectedSatuan===$s['nama_satuan']?'selected':'' ?>><?= h($s['nama_satuan']) ?></option><?php endforeach; ?></select></label>
    <button type="submit">Tampilkan barang</button>
  </form>
  <div class="table-wrap"><table><thead><tr><th>Nama barang</th><th>Satuan</th><th>Harga</th></tr></thead><tbody>
  <?php foreach ($barangSatuan as $b): ?><tr><td><?= h($b['nama']) ?></td><td><?= h($b['nama_satuan']) ?></td><td>Rp <?= h(number_format((int)$b['harga'],0,',','.')) ?></td></tr><?php endforeach; ?>
  <?php if (!$barangSatuan): ?><tr><td colspan="3">Belum ada barang untuk satuan ini.</td></tr><?php endif; ?>
  </tbody></table></div>
</div>
<div class="table-card demo-card">
  <div class="table-header"><div><strong>Function 1 & 2 · Simulasi harga</strong><small>fn_total_harga(harga, jumlah) dan fn_harga_jual(harga, margin)</small></div></div>
  <form class="demo-form" method="get">
    <input type="hidden" name="satuan" value="<?= h($selectedSatuan) ?>">
    <input type="hidden" name="hitung" value="1">
    <label>Harga satuan (Rp) <input name="harga" type="number" min="1" max="200000000" value="<?= h($harga) ?>" required></label>
    <label>Jumlah <input name="jumlah" type="number" min="1" max="100" value="<?= h($jumlah) ?>" required></label>
    <button type="submit">Hitung lewat MySQL</button>
  </form>
  <?php if ($hasilHitung): ?>
  <div class="demo-result">Total sebelum margin (Function 1): <strong>Rp <?= h(number_format((int)$hasilHitung['total_awal'],0,',','.')) ?></strong><br>
  Harga jual setelah margin <?= h($hasilHitung['margin']) ?>% (Function 2): <strong>Rp <?= h(number_format((int)$hasilHitung['harga_jual'],0,',','.')) ?></strong></div>
  <?php endif; ?>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
