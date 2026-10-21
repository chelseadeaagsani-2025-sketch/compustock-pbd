CREATE DATABASE pbd_uts;
USE pbd_uts;

CREATE TABLE `role` (
    idrole INT,
    nama_role VARCHAR(100),
    PRIMARY KEY (idrole)
);
DESCRIBE `role`;

CREATE TABLE satuan (
    idsatuan INT,
    nama_satuan VARCHAR(45),
    status TINYINT,
    PRIMARY KEY (idsatuan)
);
DESCRIBE satuan;

CREATE TABLE vendor (
    idvendor INT,
    nama_vendor VARCHAR(100),
    badan_hukum CHAR(1),
    status CHAR(1),
    PRIMARY KEY (idvendor)
);
DESCRIBE vendor;

CREATE TABLE `user` (
    iduser INT,
    username VARCHAR(45),
    password VARCHAR(100),
    idrole INT,
    PRIMARY KEY (iduser),
    FOREIGN KEY (idrole) REFERENCES `role`(idrole)
);
DESCRIBE `user`;

CREATE TABLE margin_penjualan (
    idmargin_penjualan INT,
    created_at TIMESTAMP,
    persen DOUBLE,
    status TINYINT,
    iduser INT,
    updated_at TIMESTAMP,
    PRIMARY KEY (idmargin_penjualan),
    FOREIGN KEY (iduser) REFERENCES `user`(iduser)
);
DESCRIBE margin_penjualan;

CREATE TABLE barang (
    idbarang INT,
    jenis CHAR(1),
    nama VARCHAR(45),
    idsatuan INT,
    status TINYINT,
    harga INT,
    PRIMARY KEY (idbarang),
    FOREIGN KEY (idsatuan) REFERENCES satuan(idsatuan)
);
DESCRIBE barang;

CREATE TABLE pengadaan (
    idpengadaan BIGINT,
    `timestamp` TIMESTAMP,
    user_iduser INT,
    status CHAR(1),
    vendor_idvendor INT,
    subtotal_nilai INT,
    ppn INT,
    total_nilai INT,
    PRIMARY KEY (idpengadaan),
    FOREIGN KEY (user_iduser) REFERENCES `user`(iduser),
    FOREIGN KEY (vendor_idvendor) REFERENCES vendor(idvendor)
);
DESCRIBE pengadaan;


CREATE TABLE detail_pengadaan (
    iddetail_pengadaan BIGINT,
    harga_satuan INT,
    jumlah INT,
    sub_total INT,
    idbarang INT,
    idpengadaan BIGINT,
    PRIMARY KEY (iddetail_pengadaan),
    FOREIGN KEY (idbarang) REFERENCES barang(idbarang),
    FOREIGN KEY (idpengadaan) REFERENCES pengadaan(idpengadaan)
);
DESCRIBE detail_pengadaan;


CREATE TABLE penerimaan (
    idpenerimaan BIGINT,
    created_at TIMESTAMP,
    status CHAR(1),
    idpengadaan BIGINT,
    iduser INT,
    PRIMARY KEY (idpenerimaan),
    FOREIGN KEY (idpengadaan) REFERENCES pengadaan(idpengadaan),
    FOREIGN KEY (iduser) REFERENCES `user`(iduser)
);
DESCRIBE penerimaan;


CREATE TABLE detail_penerimaan (
    iddetail_penerimaan BIGINT,
    idpenerimaan BIGINT,
    barang_idbarang INT,
    jumlah_terima INT,
    harga_satuan_terima INT,
    sub_total_terima INT,
    PRIMARY KEY (iddetail_penerimaan),
    FOREIGN KEY (idpenerimaan) REFERENCES penerimaan(idpenerimaan),
    FOREIGN KEY (barang_idbarang) REFERENCES barang(idbarang)
);
DESCRIBE detail_penerimaan;


CREATE TABLE retur (
    idretur BIGINT,
    created_at TIMESTAMP,
    idpenerimaan BIGINT,
    iduser INT,
    PRIMARY KEY (idretur),
    FOREIGN KEY (idpenerimaan) REFERENCES penerimaan(idpenerimaan),
    FOREIGN KEY (iduser) REFERENCES `user`(iduser)
);
DESCRIBE retur;


CREATE TABLE detail_retur (
    iddetail_retur INT,
    jumlah INT,
    alasan VARCHAR(200),
    idretur BIGINT,
    iddetail_penerimaan BIGINT,
    PRIMARY KEY (iddetail_retur),
    FOREIGN KEY (idretur) REFERENCES retur(idretur),
    FOREIGN KEY (iddetail_penerimaan) REFERENCES detail_penerimaan(iddetail_penerimaan)
);	
DESCRIBE detail_retur;


CREATE TABLE penjualan (
    idpenjualan INT,
    created_at TIMESTAMP,
    subtotal_nilai INT,
    ppn INT,
    total_nilai INT,
    iduser INT,
    idmargin_penjualan INT,
    PRIMARY KEY (idpenjualan),
    FOREIGN KEY (iduser) REFERENCES `user`(iduser),
    FOREIGN KEY (idmargin_penjualan) REFERENCES margin_penjualan(idmargin_penjualan)
);
DESCRIBE penjualan;


CREATE TABLE detail_penjualan (
    iddetail_penjualan BIGINT,
    harga_satuan INT,
    jumlah INT,
    subtotal INT,
    idpenjualan INT,
    idbarang INT,
    PRIMARY KEY (iddetail_penjualan),
    FOREIGN KEY (idpenjualan) REFERENCES penjualan(idpenjualan),
    FOREIGN KEY (idbarang) REFERENCES barang(idbarang)
);
DESCRIBE detail_penjualan;


CREATE TABLE kartu_stok (
    idkartu_stok BIGINT,
    jenis_transaksi CHAR(1),
    masuk INT,
    keluar INT,
    stock INT,
    created_at TIMESTAMP,
    idtransaksi INT,
    idbarang INT,
    PRIMARY KEY (idkartu_stok),
    FOREIGN KEY (idbarang) REFERENCES barang(idbarang)
);
DESCRIBE kartu_stok;

SHOW TABLES;


INSERT INTO `role` (idrole, nama_role) VALUES
(1, 'Admin'),
(2, 'Gudang'),
(3, 'Kasir');
SELECT * FROM `role`;

INSERT INTO satuan (idsatuan, nama_satuan, status) VALUES
(1, 'PCS', 1),
(2, 'BOX', 1),
(3, 'UNIT', 1);
SELECT * FROM satuan;

INSERT INTO vendor (idvendor, nama_vendor, badan_hukum, status) VALUES
(1, 'PT Sumber Makmur', 'P', 'A'),
(2, 'CV Maju Jaya', 'C', 'A'),
(3, 'PT Teknologi Nusantara', 'P', 'A');
SELECT * FROM vendor;

INSERT INTO `user` (iduser, username, password, idrole) VALUES
(1, 'admin', 'admin123', 1),
(2, 'gudang', 'gudang123', 2),
(3, 'kasir', 'kasir123', 3);
SELECT * FROM `user`;


INSERT INTO barang (idbarang, jenis, nama, idsatuan, status, harga) VALUES
(1, 'B', 'Keyboard Mekanik', 1, 1, 750000),
(2, 'B', 'Mouse Nirkabel', 1, 1, 220000),
(3, 'B', 'Monitor 24 Inch', 1, 1, 1850000),
(4, 'B', 'SSD NVMe 1 TB', 1, 1, 1150000),
(5, 'B', 'Headset Gaming', 1, 1, 450000);
SELECT * FROM barang;

INSERT INTO margin_penjualan
(idmargin_penjualan, created_at, persen, status, iduser, updated_at)
VALUES
(1, CURRENT_TIMESTAMP, 10, 1, 1, CURRENT_TIMESTAMP),
(2, CURRENT_TIMESTAMP, 20, 0, 1, CURRENT_TIMESTAMP),
(3, CURRENT_TIMESTAMP, 30, 0, 1, CURRENT_TIMESTAMP),
(4, CURRENT_TIMESTAMP, 40, 0, 1, CURRENT_TIMESTAMP),
(5, CURRENT_TIMESTAMP, 50, 0, 1, CURRENT_TIMESTAMP);
SELECT * FROM margin_penjualan;


-- VIEW 1: ROLE
CREATE VIEW v_role AS
SELECT idrole, nama_role
FROM `role`;
SELECT * FROM v_role;

-- VIEW 2: SATUAN
CREATE VIEW v_satuan AS
SELECT idsatuan, nama_satuan, status
FROM satuan;
SELECT * FROM v_satuan;

-- VIEW 3: VENDOR
CREATE VIEW v_vendor AS
SELECT idvendor, nama_vendor, badan_hukum, status
FROM vendor;
SELECT * FROM v_vendor;

-- VIEW 4: USER
CREATE VIEW v_user AS
SELECT
    u.iduser,
    u.username,
    u.password,
    r.nama_role
FROM `user` u
JOIN `role` r ON u.idrole = r.idrole;
SELECT * FROM v_user;

-- VIEW 5: BARANG
CREATE VIEW v_barang AS
SELECT
    b.idbarang,
    b.jenis,
    b.nama,
    s.nama_satuan,
    b.status,
    b.harga
FROM barang b
JOIN satuan s ON b.idsatuan = s.idsatuan;
SELECT * FROM v_barang;

-- VIEW 6: MARGIN PENJUALAN
CREATE VIEW v_margin_penjualan AS
SELECT
    idmargin_penjualan,
    created_at,
    persen,
    status,
    iduser,
    updated_at
FROM margin_penjualan;
SELECT * FROM v_margin_penjualan;
