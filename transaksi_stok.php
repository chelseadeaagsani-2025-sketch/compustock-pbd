<?php

if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
require __DIR__ . '/koneksi.php';
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Uji Trigger Kartu Stok';
$activePage = 'stok';
$error = '';
$notice = isset($_GET['sukses']) ? 'Transaksi tersimpan. Trigger MySQL otomatis memperbarui kartu stok.' : '';
if (empty($_SESSION['csrf_compustock'])) { $_SESSION['csrf_compustock'] = bin2hex(random_bytes(32)); }


function nextDemoId(PDO $db, string $table, string $column): int {
    $allowed = [
        'pengadaan'=>'idpengadaan', 'detail_pengadaan'=>'iddetail_pengadaan',
        'penerimaan'=>'idpenerimaan', 'detail_penerimaan'=>'iddetail_penerimaan',
        'penjualan'=>'idpenjualan', 'detail_penjualan'=>'iddetail_penjualan'
    ];
    if (($allowed[$table] ?? null) !== $column) { throw new RuntimeException('Nama tabel tidak dikenali.'); }
    return (int)$db->query("SELECT COALESCE(MAX(`$column`),0)+1 FROM `$table`")->fetchColumn();
}

try {
    $barang = $pdo->query('SELECT idbarang, nama, harga FROM v_barang WHERE status = 1 ORDER BY idbarang')->fetchAll();
    $vendor = $pdo->query("SELECT idvendor, nama_vendor FROM v_vendor WHERE status = 'A' ORDER BY idvendor")->fetchAll();
    $users = $pdo->query('SELECT iduser, username FROM v_user ORDER BY iduser')->fetchAll();
    $marginAktif = $pdo->query('SELECT idmargin_penjualan, persen FROM v_margin_penjualan WHERE status = 1')->fetchAll();
    $stockHistory = $pdo->query('SELECT idkartu_stok, created_at, nama_barang, jenis_transaksi, masuk, keluar, stock FROM v_kartu_stok ORDER BY idkartu_stok DESC LIMIT 30')->fetchAll();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!hash_equals($_SESSION['csrf_compustock'], (string)($_POST['csrf'] ?? ''))) {
            throw new RuntimeException('Form sudah tidak berlaku. Muat ulang halaman.');
        }
        $aksi = (string)($_POST['aksi'] ?? '');
        if (!in_array($aksi, ['penerimaan', 'penjualan'], true)) { throw new RuntimeException('Jenis transaksi tidak dikenal.'); }
        $barangId = filter_var($_POST['barang_id'] ?? null, FILTER_VALIDATE_INT);
        $userId = filter_var($_POST['user_id'] ?? null, FILTER_VALIDATE_INT);
        $qty = filter_var($_POST['qty'] ?? null, FILTER_VALIDATE_INT);
        if ($qty === false || $qty < 1 || $qty > 100) { throw new RuntimeException('Jumlah harus antara 1 dan 100.'); }
        $barangPilihan = null;
        foreach ($barang as $b) { if ((int)$b['idbarang'] === $barangId) { $barangPilihan = $b; break; } }
        if (!$barangPilihan || !in_array($userId, array_map('intval', array_column($users,'iduser')), true)) {
            throw new RuntimeException('Barang atau user tidak valid.');
        }
        if (count($marginAktif) !== 1) { throw new RuntimeException('Periksa margin aktif: harus tepat satu margin berstatus 1.'); }

        $pdo->beginTransaction();
       
        $lock = $pdo->prepare('SELECT idbarang FROM barang WHERE idbarang = ? FOR UPDATE');
        $lock->execute([$barangId]);

        if ($aksi === 'penerimaan') {
            $vendorId = filter_var($_POST['vendor_id'] ?? null, FILTER_VALIDATE_INT);
            $biaya = filter_var($_POST['harga_beli'] ?? null, FILTER_VALIDATE_INT);
            if ($biaya === false || $biaya < 1 || $biaya > 200000000 || !in_array($vendorId, array_map('intval', array_column($vendor,'idvendor')), true)) {
                throw new RuntimeException('Vendor atau harga beli tidak valid.');
            }
            $total = $biaya * $qty;
            $idPengadaan = nextDemoId($pdo, 'pengadaan','idpengadaan');
            $idDetailPengadaan = nextDemoId($pdo, 'detail_pengadaan','iddetail_pengadaan');
            $idPenerimaan = nextDemoId($pdo, 'penerimaan','idpenerimaan');
            $idDetailPenerimaan = nextDemoId($pdo, 'detail_penerimaan','iddetail_penerimaan');
            $pdo->prepare("INSERT INTO pengadaan(idpengadaan,`timestamp`,user_iduser,status,vendor_idvendor,subtotal_nilai,ppn,total_nilai) VALUES (?,NOW(),?,'A',?,?,0,?)")
                ->execute([$idPengadaan,$userId,$vendorId,$total,$total]);
            $pdo->prepare('INSERT INTO detail_pengadaan(iddetail_pengadaan,harga_satuan,jumlah,sub_total,idbarang,idpengadaan) VALUES (?,?,?,?,?,?)')
                ->execute([$idDetailPengadaan,$biaya,$qty,$total,$barangId,$idPengadaan]);
            $pdo->prepare("INSERT INTO penerimaan(idpenerimaan,created_at,status,idpengadaan,iduser) VALUES (?,NOW(),'A',?,?)")
                ->execute([$idPenerimaan,$idPengadaan,$userId]);
            // INSERT di bawah memicu trg_stok_masuk secara otomatis.
            $pdo->prepare('INSERT INTO detail_penerimaan(iddetail_penerimaan,idpenerimaan,barang_idbarang,jumlah_terima,harga_satuan_terima,sub_total_terima) VALUES (?,?,?,?,?,?)')
                ->execute([$idDetailPenerimaan,$idPenerimaan,$barangId,$qty,$biaya,$total]);
        } else {
            $margin = $marginAktif[0];
            $hargaJualStmt = $pdo->prepare('SELECT fn_harga_jual(?,?)');
            $hargaJualStmt->execute([(int)$barangPilihan['harga'],(float)$margin['persen']]);
            $hargaJual = (int)$hargaJualStmt->fetchColumn();
            $totalStmt = $pdo->prepare('SELECT fn_total_harga(?,?)');
            $totalStmt->execute([$hargaJual,$qty]);
            $total = (int)$totalStmt->fetchColumn();
            $idPenjualan = nextDemoId($pdo, 'penjualan','idpenjualan');
            $idDetailPenjualan = nextDemoId($pdo, 'detail_penjualan','iddetail_penjualan');
            $pdo->prepare('INSERT INTO penjualan(idpenjualan,created_at,subtotal_nilai,ppn,total_nilai,iduser,idmargin_penjualan) VALUES (?,NOW(),?,0,?,?,?)')
                ->execute([$idPenjualan,$total,$total,$userId,(int)$margin['idmargin_penjualan']]);
            // INSERT berikut memicu trg_stok_keluar dan menolak bila stok tidak mencukupi.
            $pdo->prepare('INSERT INTO detail_penjualan(iddetail_penjualan,harga_satuan,jumlah,subtotal,idpenjualan,idbarang) VALUES (?,?,?,?,?,?)')
                ->execute([$idDetailPenjualan,$hargaJual,$qty,$total,$idPenjualan,$barangId]);
        }
        $pdo->commit();
        header('Location: transaksi_stok.php?sukses=1');
        exit;
    }
} catch (Throwable $e) {
    if ($pdo->inTransaction()) { $pdo->rollBack(); }
    error_log('CompuStock trigger demo: ' . $e->getMessage());
    $error = $e instanceof PDOException
        ? (($e->errorInfo[0] ?? '') === '45000' ? 'Stok barang tidak mencukupi, transaksi penjualan dibatalkan.' : 'Gagal memproses transaksi. Periksa tabel, trigger, dan data di MySQL Workbench.')
        : $e->getMessage();
}

require __DIR__ . '/includes/header.php';
?>
<div class="eyebrow">COMPUSTOCK / TRIGGER MYSQL</div>
<h1>Uji Trigger Kartu Stok</h1>
<p class="intro">Form ini menyimpan transaksi ke tabel dasar. Trigger MySQL otomatis mencatat stok. Riwayat di bawah diambil dari VIEW <code>v_kartu_stok</code>.</p>
<?php if ($error): ?><div class="alert"><?= h($error) ?></div><?php endif; ?>
<?php if ($notice): ?><div class="demo-success"><?= h($notice) ?></div><?php endif; ?>
<div class="table-card demo-card"><div class="table-header"><strong>Penerimaan barang · Trigger stok masuk</strong></div>
<form method="post" class="demo-form">
<input type="hidden" name="csrf" value="<?= h($_SESSION['csrf_compustock']) ?>"><input type="hidden" name="aksi" value="penerimaan">
<label>Barang <select name="barang_id" required><?php foreach ($barang as $b): ?><option value="<?= h($b['idbarang']) ?>"><?= h($b['nama']) ?></option><?php endforeach; ?></select></label>
<label>Vendor <select name="vendor_id" required><?php foreach ($vendor as $v): ?><option value="<?= h($v['idvendor']) ?>"><?= h($v['nama_vendor']) ?></option><?php endforeach; ?></select></label>
<label>User <select name="user_id" required><?php foreach ($users as $u): ?><option value="<?= h($u['iduser']) ?>"><?= h($u['username']) ?></option><?php endforeach; ?></select></label>
<label>Harga beli (Rp) <input type="number" name="harga_beli" value="100000" min="1" max="200000000" required></label>
<label>Jumlah <input type="number" name="qty" min="1" max="100" value="1" required></label>
<button type="submit">Simpan penerimaan</button></form>
<p class="demo-note">Membuat pengadaan, detail pengadaan, penerimaan, dan detail penerimaan. Trigger stok masuk berjalan ketika detail penerimaan tersimpan.</p></div>
<div class="table-card demo-card"><div class="table-header"><strong>Penjualan barang · Trigger stok keluar</strong></div>
<form method="post" class="demo-form">
<input type="hidden" name="csrf" value="<?= h($_SESSION['csrf_compustock']) ?>"><input type="hidden" name="aksi" value="penjualan">
<label>Barang <select name="barang_id" required><?php foreach ($barang as $b): ?><option value="<?= h($b['idbarang']) ?>"><?= h($b['nama']) ?></option><?php endforeach; ?></select></label>
<label>User <select name="user_id" required><?php foreach ($users as $u): ?><option value="<?= h($u['iduser']) ?>"><?= h($u['username']) ?></option><?php endforeach; ?></select></label>
<label>Jumlah <input type="number" name="qty" min="1" max="100" value="1" required></label>
<button type="submit">Simpan penjualan</button></form>
<p class="demo-note">Harga jual dihitung lewat Function berdasarkan margin aktif. Trigger akan membatalkan detail penjualan jika stok tidak mencukupi.</p></div>
<div class="table-card demo-card"><div class="table-header"><div><strong>Riwayat kartu stok</strong><small>Sumber: SELECT dari v_kartu_stok</small></div><span class="count"><?= count($stockHistory ?? []) ?> catatan</span></div>
<div class="table-wrap"><table><thead><tr><th>ID</th><th>Waktu</th><th>Barang</th><th>Transaksi</th><th>Masuk</th><th>Keluar</th><th>Stok akhir</th></tr></thead><tbody>
<?php foreach (($stockHistory ?? []) as $s): ?><tr><td><?= h($s['idkartu_stok']) ?></td><td><?= h($s['created_at']) ?></td><td><?= h($s['nama_barang']) ?></td><td><?= h($s['jenis_transaksi']==='P'?'Penerimaan':'Penjualan') ?></td><td><?= h($s['masuk']) ?></td><td><?= h($s['keluar']) ?></td><td><?= h($s['stock']) ?></td></tr><?php endforeach; ?>
<?php if (empty($stockHistory)): ?><tr><td colspan="7">Belum ada catatan kartu stok.</td></tr><?php endif; ?>
</tbody></table></div></div>
<div class="hint demo-danger">Halaman ini untuk praktik lokal. Belum dilengkapi sistem login/otorisasi dan belum dirancang untuk transaksi serentak banyak pengguna.</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
