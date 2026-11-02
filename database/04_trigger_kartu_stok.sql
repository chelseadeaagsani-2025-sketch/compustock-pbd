USE `pbd_uts`;

DELIMITER $$

-- Trigger 1: otomatis mencatat stok masuk setelah detail penerimaan disimpan.
CREATE TRIGGER `trg_stok_masuk` AFTER INSERT ON `detail_penerimaan` FOR EACH ROW BEGIN
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
END$$

-- Trigger 2: otomatis mencatat stok keluar; menolak jika stok kurang.
CREATE TRIGGER `trg_stok_keluar` AFTER INSERT ON `detail_penjualan` FOR EACH ROW BEGIN
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
END$$

DELIMITER ;
