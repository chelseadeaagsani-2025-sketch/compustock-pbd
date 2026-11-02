USE `pbd_uts`;

DROP FUNCTION IF EXISTS `fn_total_harga`;
DELIMITER $$
CREATE FUNCTION `fn_total_harga`(
    p_harga INT,
    p_jumlah INT
)
RETURNS BIGINT
DETERMINISTIC
BEGIN
    RETURN p_harga * p_jumlah;
END$$
DELIMITER ;

DROP FUNCTION IF EXISTS `fn_harga_jual`;
DELIMITER $$
CREATE FUNCTION `fn_harga_jual`(
    p_harga INT,
    p_persen DOUBLE
)
RETURNS BIGINT
DETERMINISTIC
NO SQL
BEGIN
    RETURN ROUND(p_harga * (1 + p_persen / 100));
END$$
DELIMITER ;