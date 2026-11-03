USE `pbd_uts`;

-- 1. VIEW tabel pengadaan
CREATE OR REPLACE VIEW `v_pengadaan` AS
SELECT
    `idpengadaan`,
    `timestamp`,
    `user_iduser`,
    `status`,
    `vendor_idvendor`,
    `subtotal_nilai`,
    `ppn`,
    `total_nilai`
FROM `pengadaan`;

-- 2. VIEW tabel detail_pengadaan
CREATE OR REPLACE VIEW `v_detail_pengadaan` AS
SELECT
    `iddetail_pengadaan`,
    `harga_satuan`,
    `jumlah`,
    `sub_total`,
    `idbarang`,
    `idpengadaan`
FROM `detail_pengadaan`;

-- 3. VIEW tabel penerimaan
CREATE OR REPLACE VIEW `v_penerimaan` AS
SELECT
    `idpenerimaan`,
    `created_at`,
    `status`,
    `idpengadaan`,
    `iduser`
FROM `penerimaan`;

-- 4. VIEW tabel detail_penerimaan
CREATE OR REPLACE VIEW `v_detail_penerimaan` AS
SELECT
    `iddetail_penerimaan`,
    `idpenerimaan`,
    `barang_idbarang`,
    `jumlah_terima`,
    `harga_satuan_terima`,
    `sub_total_terima`
FROM `detail_penerimaan`;

-- 5. VIEW tabel retur
CREATE OR REPLACE VIEW `v_retur` AS
SELECT
    `idretur`,
    `created_at`,
    `idpenerimaan`,
    `iduser`
FROM `retur`;

-- 6. VIEW tabel detail_retur
CREATE OR REPLACE VIEW `v_detail_retur` AS
SELECT
    `iddetail_retur`,
    `jumlah`,
    `alasan`,
    `idretur`,
    `iddetail_penerimaan`
FROM `detail_retur`;

-- 7. VIEW tabel penjualan
CREATE OR REPLACE VIEW `v_penjualan` AS
SELECT
    `idpenjualan`,
    `created_at`,
    `subtotal_nilai`,
    `ppn`,
    `total_nilai`,
    `iduser`,
    `idmargin_penjualan`
FROM `penjualan`;

-- 8. VIEW tabel detail_penjualan
CREATE OR REPLACE VIEW `v_detail_penjualan` AS
SELECT
    `iddetail_penjualan`,
    `harga_satuan`,
    `jumlah`,
    `subtotal`,
    `idpenjualan`,
    `idbarang`
FROM `detail_penjualan`;
