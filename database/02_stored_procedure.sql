USE `pbd_uts`;

DROP PROCEDURE IF EXISTS `sp_tampil_barang`;
DELIMITER $$
CREATE PROCEDURE `sp_tampil_barang`()
BEGIN
    SELECT idbarang, nama, nama_satuan, harga, status
    FROM v_barang
    ORDER BY idbarang;
END$$
DELIMITER ;

DROP PROCEDURE IF EXISTS `sp_barang_per_satuan`;
DELIMITER $$
CREATE PROCEDURE `sp_barang_per_satuan`(IN p_nama_satuan VARCHAR(45))
BEGIN
    SELECT idbarang, nama, nama_satuan, harga, status
    FROM v_barang
    WHERE nama_satuan = p_nama_satuan
    ORDER BY idbarang;
END$$
DELIMITER ;
