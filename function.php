<?php
require __DIR__ . '/koneksi.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Function';
$activePage = 'function';
$harga = (int) ($_GET['harga'] ?? 750000);
$jumlah = (int) ($_GET['jumlah'] ?? 2);
$hasilHitung = null;
$marginAktif = null;
$error = '';

try {
   
    $marginRows = $pdo->query('SELECT idmargin_penjualan, persen FROM v_margin_penjualan WHERE status = 1')->fetchAll();
    if (count($marginRows) !== 1) {
        throw new RuntimeException('Harus ada tepat satu margin aktif di database.');
    }
    $marginAktif = (float) $marginRows[0]['persen'];

    if (isset($_GET['hitung'])) {
        if ($harga <= 0 || $harga > 200000000 || $jumlah < 1 || $jumlah > 100) {
            throw new RuntimeException('Harga harus positif (maksimal Rp200.000.000) dan jumlah 1–100.');
        }

        
        $stmt = $pdo->prepare('SELECT fn_total_harga(?, ?) AS total_awal, fn_harga_jual(?, ?) AS harga_jual');
        $stmt->execute([$harga, $jumlah, $harga, $marginAktif]);
        $hasilHitung = $stmt->fetch(PDO::FETCH_ASSOC);
    }
} catch (Throwable $e) {
    error_log('CompuStock Function: ' . $e->getMessage());
    $error = $e instanceof PDOException
        ? 'Function belum tersedia atau query gagal. Periksa Function dan koneksi MySQL.'
        : $e->getMessage();
}

require __DIR__ . '/includes/header.php';
?>
<div class="eyebrow">COMPUSTOCK / FUNCTION MYSQL</div>
<h1>Function</h1>
<p class="intro">Pengujian 2 Function MySQL untuk menghitung total harga dan harga jual setelah margin.</p>
<?php if ($error): ?><div class="alert"><?= h($error) ?></div><?php endif; ?>

<div class="table-card demo-card">
    <div class="table-header">
        <div><strong>Simulasi Perhitungan Harga</strong><small>Margin aktif dari VIEW v_margin_penjualan: <?= $marginAktif !== null ? h($marginAktif) . '%' : 'belum tersedia' ?></small></div>
    </div>
    <form class="demo-form" method="get" action="function.php">
        <input type="hidden" name="hitung" value="1">
        <label>Harga satuan (Rp) <input name="harga" type="number" min="1" max="200000000" value="<?= h($harga) ?>" required></label>
        <label>Jumlah <input name="jumlah" type="number" min="1" max="100" value="<?= h($jumlah) ?>" required></label>
        <button type="submit">Hitung lewat MySQL</button>
    </form>
</div>

<div class="table-card demo-card">
    <div class="table-header"><div><strong>Function 1 · fn_total_harga()</strong><small>fn_total_harga(harga, jumlah) — total sebelum margin</small></div></div>
    <div class="demo-result">
        <?php if ($hasilHitung): ?>
            Total harga: <strong>Rp <?= h(number_format((int) $hasilHitung['total_awal'], 0, ',', '.')) ?></strong>
        <?php else: ?>
            Masukkan harga dan jumlah, lalu klik <strong>Hitung lewat MySQL</strong>.
        <?php endif; ?>
    </div>
</div>

<div class="table-card demo-card">
    <div class="table-header"><div><strong>Function 2 · fn_harga_jual()</strong><small>fn_harga_jual(harga, margin) — harga jual per unit setelah margin</small></div></div>
    <div class="demo-result">
        <?php if ($hasilHitung): ?>
            Harga jual per unit (margin <?= h($marginAktif) ?>%): <strong>Rp <?= h(number_format((int) $hasilHitung['harga_jual'], 0, ',', '.')) ?></strong>
        <?php else: ?>
            Hasil harga jual akan ditampilkan setelah perhitungan dilakukan.
        <?php endif; ?>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
