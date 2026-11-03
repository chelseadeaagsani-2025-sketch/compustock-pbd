<?php

require __DIR__ . '/koneksi.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'VIEW Data Transaksi';
$activePage = 'data_transaksi';

$daftarView = [
    'pengadaan'         => ['Pengadaan', 'v_pengadaan', 'idpengadaan'],
    'detail_pengadaan'  => ['Detail Pengadaan', 'v_detail_pengadaan', 'iddetail_pengadaan'],
    'penerimaan'        => ['Penerimaan', 'v_penerimaan', 'idpenerimaan'],
    'detail_penerimaan' => ['Detail Penerimaan', 'v_detail_penerimaan', 'iddetail_penerimaan'],
    'retur'             => ['Retur', 'v_retur', 'idretur'],
    'detail_retur'      => ['Detail Retur', 'v_detail_retur', 'iddetail_retur'],
    'penjualan'         => ['Penjualan', 'v_penjualan', 'idpenjualan'],
    'detail_penjualan'  => ['Detail Penjualan', 'v_detail_penjualan', 'iddetail_penjualan'],
    'kartu_stok'        => ['Kartu Stok', 'v_kartu_stok', 'idkartu_stok'],
];

$pilihan = (string) ($_GET['jenis'] ?? 'pengadaan');
if (!array_key_exists($pilihan, $daftarView)) {
    $pilihan = 'pengadaan';
}
[$labelAktif, $viewAktif, $idAktif] = $daftarView[$pilihan];
$baris = [];
$kolom = [];
$error = '';

try {
    
    $sql = "SELECT * FROM `{$viewAktif}` ORDER BY `{$idAktif}` DESC LIMIT 100";
    $stmt = $pdo->query($sql);
    $baris = $stmt->fetchAll(PDO::FETCH_ASSOC);

   
    for ($i = 0; $i < $stmt->columnCount(); $i++) {
        $meta = $stmt->getColumnMeta($i);
        if (isset($meta['name'])) {
            $kolom[] = (string) $meta['name'];
        }
    }
    if (!$kolom && $baris) {
        $kolom = array_keys($baris[0]);
    }
} catch (Throwable $e) {
    error_log('CompuStock VIEW transaksi: ' . $e->getMessage());
    $error = 'VIEW belum dapat ditampilkan. Jalankan 06_view_transaksi_lainnya.sql dan periksa koneksi database.';
}

require __DIR__ . '/includes/header.php';
?>
<div class="eyebrow">COMPUSTOCK / VIEW TRANSAKSI</div>
<h1>VIEW Data Transaksi</h1>
<p class="intro">Menampilkan data dari 9 VIEW non-master sesuai tabel transaksi pada PDM. Halaman ini hanya untuk membaca data.</p>
<?php if ($error): ?><div class="alert"><?= h($error) ?></div><?php endif; ?>
<div class="table-card demo-card">
    <div class="table-header"><strong>Pilih tabel transaksi</strong></div>
    <form method="get" class="demo-form">
        <label>Nama data
            <select name="jenis" required>
                <?php foreach ($daftarView as $key => $v): ?>
                    <option value="<?= h($key) ?>" <?= $pilihan === $key ? 'selected' : '' ?>><?= h($v[0]) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <button type="submit">Tampilkan data</button>
    </form>
</div>
<div class="table-card demo-card">
    <div class="table-header">
        <div><strong><?= h($labelAktif) ?></strong><small>Sumber: SELECT * FROM <?= h($viewAktif) ?></small></div>
        <span class="count"><?= count($baris) ?> baris ditampilkan</span>
    </div>
    <div class="table-wrap" style="overflow-x:auto;">
        <table>
            <thead><tr>
                <?php foreach ($kolom as $namaKolom): ?>
                    <th><?= h(str_replace('_', ' ', $namaKolom)) ?></th>
                <?php endforeach; ?>
            </tr></thead>
            <tbody>
                <?php foreach ($baris as $data): ?>
                    <tr>
                        <?php foreach ($kolom as $namaKolom): ?>
                            <td><?= $data[$namaKolom] === null ? '—' : h($data[$namaKolom]) ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$baris): ?>
                    <tr><td colspan="<?= max(1, count($kolom)) ?>"><?= $error ? 'Tidak dapat mengambil data.' : 'Belum ada data pada VIEW ini.' ?></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
