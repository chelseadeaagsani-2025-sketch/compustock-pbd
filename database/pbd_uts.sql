CREATE DATABASE pbd_uts;
USE pbd_uts;


-- BAGIAN 2: MEMBUAT TABEL
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

-- BAGIAN 3: MEMERIKSA TABEL
SHOW TABLES;

-- BAGIAN 4: MENGISI DATA MASTER
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



-- BAGIAN 5: MEMBUAT DAN MENGUJI VIEW
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


-- BAGIAN 6: PEMERIKSAAN STRUKTUR TABEL
DESCRIBE kartu_stok;
DESCRIBE detail_penerimaan;
DESCRIBE detail_penjualan;

-- BAGIAN 7: PEMERIKSAAN DATA TRANSAKSI
SELECT * FROM pengadaan;
SELECT * FROM penerimaan;
SELECT * FROM detail_penerimaan;
SELECT * FROM penjualan;
SELECT * FROM detail_penjualan;
SELECT * FROM kartu_stok;

SHOW TRIGGERS FROM pbd_uts;


-- 7. TRIGGER KARTU STOK (STOK MASUK)
DELIMITER //

CREATE TRIGGER trg_stok_masuk
AFTER INSERT ON detail_penerimaan
FOR EACH ROW
BEGIN
    DECLARE stok_sebelumnya INT DEFAULT 0;
    DECLARE id_baru BIGINT;

    -- Mengambil stok terakhir barang
    SELECT COALESCE(
        (
            SELECT stock
            FROM kartu_stok
            WHERE idbarang = NEW.barang_idbarang
            ORDER BY idkartu_stok DESC
            LIMIT 1
        ), 0
    )
    INTO stok_sebelumnya;

    -- Membuat ID kartu stok berikutnya
    SELECT COALESCE(MAX(idkartu_stok), 0) + 1
    INTO id_baru
    FROM kartu_stok;

    -- Mencatat barang masuk ke kartu stok
    INSERT INTO kartu_stok (
        idkartu_stok,
        jenis_transaksi,
        masuk,
        keluar,
        stock,
        created_at,
        idtransaksi,
        idbarang
    )
    VALUES (
        id_baru,
        'P',
        NEW.jumlah_terima,
        0,
        stok_sebelumnya + NEW.jumlah_terima,
        CURRENT_TIMESTAMP,
        NEW.idpenerimaan,
        NEW.barang_idbarang
    );
END//

DELIMITER ;

-- 8. CEK TRIGGER YANG SUDAH DIBUAT
SHOW TRIGGERS FROM pbd_uts;

-- 9. CEK DATA UNTUK PENGUJIAN TRIGGER
SELECT * FROM penerimaan;
SELECT * FROM detail_penerimaan;
SELECT * FROM kartu_stok;

-- 10. CEK DATA MASTER UNTUK PENGUJIAN
SELECT * FROM `user`;
SELECT * FROM barang;
SELECT * FROM pengadaan;


-- 10A. DATA UJI PENGADAAN
INSERT INTO pengadaan
(idpengadaan, `timestamp`, user_iduser, status,
 vendor_idvendor, subtotal_nilai, ppn, total_nilai)
VALUES
(1, CURRENT_TIMESTAMP, 1, 'A', 1, 1000000, 0, 1000000);

-- 10B. DATA UJI PENERIMAAN BARANG
INSERT INTO penerimaan
(idpenerimaan, created_at, status, idpengadaan, iduser)
VALUES
(1, CURRENT_TIMESTAMP, 'A', 1, 1);


-- 11. DATA UJI DETAIL PENERIMAAN
INSERT INTO detail_penerimaan
(iddetail_penerimaan, idpenerimaan, barang_idbarang,
 jumlah_terima, harga_satuan_terima, sub_total_terima)
VALUES
(1, 1, 1, 5, 100000, 500000);

-- 12. CEK HASIL TRIGGER KARTU STOK
SELECT * FROM kartu_stok;


-- 13. TRIGGER KARTU STOK (STOK KELUAR)
DELIMITER //

CREATE TRIGGER trg_stok_keluar
AFTER INSERT ON detail_penjualan
FOR EACH ROW
BEGIN
    DECLARE stok_sebelumnya INT DEFAULT 0;
    DECLARE id_baru BIGINT;

    -- Mengambil stok terakhir barang
    SELECT COALESCE(
        (
            SELECT stock
            FROM kartu_stok
            WHERE idbarang = NEW.idbarang
            ORDER BY idkartu_stok DESC
            LIMIT 1
        ), 0
    )
    INTO stok_sebelumnya;

    -- Memastikan stok mencukupi
    IF stok_sebelumnya < NEW.jumlah THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Stok barang tidak mencukupi';
    END IF;

    -- Membuat ID kartu stok berikutnya
    SELECT COALESCE(MAX(idkartu_stok), 0) + 1
    INTO id_baru
    FROM kartu_stok;

    -- Mencatat barang keluar
    INSERT INTO kartu_stok (
        idkartu_stok,
        jenis_transaksi,
        masuk,
        keluar,
        stock,
        created_at,
        idtransaksi,
        idbarang
    )
    VALUES (
        id_baru,
        'J',
        0,
        NEW.jumlah,
        stok_sebelumnya - NEW.jumlah,
        CURRENT_TIMESTAMP,
        NEW.idpenjualan,
        NEW.idbarang
    );
END//

DELIMITER ;


-- 14. CEK TRIGGER
SHOW TRIGGERS FROM pbd_uts;


-- 15. DATA UJI PENJUALAN
INSERT INTO penjualan
(idpenjualan, created_at, subtotal_nilai, ppn,
 total_nilai, iduser, idmargin_penjualan)
VALUES
(1, CURRENT_TIMESTAMP, 200000, 0, 200000, 1, 1);


-- 16. DATA UJI DETAIL PENJUALAN
INSERT INTO detail_penjualan
(iddetail_penjualan, harga_satuan, jumlah, subtotal,
 idpenjualan, idbarang)
VALUES
(1, 100000, 2, 200000, 1, 1);


-- 17. CEK HASIL TRIGGER KARTU STOK
SELECT * FROM kartu_stok
ORDER BY idkartu_stok;


-- 18. STORED PROCEDURE UNTUK MENAMPILKAN DATA BARANG
DELIMITER //

CREATE PROCEDURE sp_tampil_barang()
BEGIN
    SELECT
        b.idbarang,
        b.nama,
        s.nama_satuan,
        b.harga,
        b.status
    FROM barang b
    JOIN satuan s ON b.idsatuan = s.idsatuan;
END//

DELIMITER ;


-- 19. PENGUJIAN STORED PROCEDURE
CALL sp_tampil_barang();


-- 20. FUNCTION MENGHITUNG TOTAL HARGA
DELIMITER //

CREATE FUNCTION fn_total_harga(
    p_harga INT,
    p_jumlah INT
)
RETURNS BIGINT
DETERMINISTIC
BEGIN
    RETURN p_harga * p_jumlah;
END//

DELIMITER ;


-- 21. PENGUJIAN FUNCTION
SELECT fn_total_harga(100000, 2) AS total_harga;


-- 22. PEMERIKSAAN AKHIR DATABASE
-- A. Cek seluruh tabel
SHOW TABLES;

-- B. Cek seluruh VIEW
SHOW FULL TABLES
WHERE Table_type = 'VIEW';

-- C. Cek seluruh TRIGGER
SHOW TRIGGERS FROM pbd_uts;

-- D. Cek STORED PROCEDURE
SHOW PROCEDURE STATUS
WHERE Db = 'pbd_uts';

-- E. Cek FUNCTION
SHOW FUNCTION STATUS
WHERE Db = 'pbd_uts';

-- F. Cek margin penjualan
SELECT * FROM margin_penjualan;

-- G. Cek kartu stok
SELECT * FROM kartu_stok
ORDER BY idkartu_stok;


-- BAGIAN 23. CEK MARGIN PENJUALAN
SELECT idmargin_penjualan, persen, status
FROM margin_penjualan
ORDER BY idmargin_penjualan;


-- BAGIAN 24. PENGUJIAN VIEW MASTER DATA
SELECT * FROM v_satuan;
SELECT * FROM v_barang;
SELECT * FROM v_vendor;
SELECT * FROM v_user;
SELECT * FROM v_role;
SELECT * FROM v_margin_penjualan;


-- BAGIAN 25. CEK DATA TRANSAKSI RETUR
SELECT * FROM retur;
SELECT * FROM detail_retur;

-- BAGIAN 26. CEK DATA PENGADAAN
SELECT * FROM pengadaan;
SELECT * FROM detail_pengadaan;


-- BAGIAN 27. CEK DATA PENJUALAN
SELECT * FROM penjualan;
SELECT * FROM detail_penjualan;


-- BAGIAN 28. CEK DATA PENERIMAAN
SELECT * FROM penerimaan;
SELECT * FROM detail_penerimaan;


-- BAGIAN 29. CEK KARTU STOK
SELECT * FROM kartu_stok;


-- BAGIAN 30. CEK MARGIN PENJUALAN
SELECT * FROM margin_penjualan;

-- 1. Cek VIEW
SHOW FULL TABLES
WHERE Table_type = 'VIEW';

-- 2. Cek TRIGGER
SHOW TRIGGERS FROM pbd_uts;

-- 3. Cek STORED PROCEDURE
SHOW PROCEDURE STATUS
WHERE Db = 'pbd_uts';

-- 4. Cek FUNCTION
SHOW FUNCTION STATUS
WHERE Db = 'pbd_uts';