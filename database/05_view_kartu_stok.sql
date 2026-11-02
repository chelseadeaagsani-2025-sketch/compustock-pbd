USE `pbd_uts`;

CREATE OR REPLACE VIEW `v_kartu_stok` AS
SELECT k.idkartu_stok, k.created_at, k.idtransaksi,
       k.jenis_transaksi, k.idbarang, b.nama AS nama_barang,
       k.masuk, k.keluar, k.stock
FROM kartu_stok k
JOIN v_barang b ON k.idbarang = b.idbarang;