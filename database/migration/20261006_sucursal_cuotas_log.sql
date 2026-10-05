-- IDs estables, una cuota vigente por sucursal e historial de incrementos.
-- La migración aborta si existen IDs duplicados o más de una cuota vigente por sucursal.
DELIMITER //
DROP PROCEDURE IF EXISTS migrar_sucursal_cuotas_log//
CREATE PROCEDURE migrar_sucursal_cuotas_log()
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.statistics
        WHERE table_schema = DATABASE() AND table_name = 'sucursal_cuota' AND index_name = 'PRIMARY'
    ) THEN
        IF EXISTS (SELECT id FROM sucursal_cuota GROUP BY id HAVING COUNT(*) > 1) THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Hay IDs de cuota duplicados; corrígelos antes de continuar.';
        END IF;
        ALTER TABLE sucursal_cuota ADD PRIMARY KEY (id);
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema = DATABASE() AND table_name = 'sucursal_cuota'
          AND column_name = 'id' AND EXTRA LIKE '%auto_increment%'
    ) THEN
        ALTER TABLE sucursal_cuota MODIFY COLUMN id INT NOT NULL AUTO_INCREMENT;
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM information_schema.statistics
        WHERE table_schema = DATABASE() AND table_name = 'sucursal_cuota'
          AND index_name = 'uq_sucursal_cuota_sucursal'
    ) THEN
        IF EXISTS (SELECT id_sucursal FROM sucursal_cuota GROUP BY id_sucursal HAVING COUNT(*) > 1) THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Hay varias cuotas vigentes para una sucursal; consolídalas antes de continuar.';
        END IF;
        ALTER TABLE sucursal_cuota ADD UNIQUE KEY uq_sucursal_cuota_sucursal (id_sucursal);
    END IF;
END//

CALL migrar_sucursal_cuotas_log()//
DROP PROCEDURE migrar_sucursal_cuotas_log//

CREATE TABLE IF NOT EXISTS sucursal_cuota_log (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_sucursal INT NOT NULL,
    id_sucursal_cuota INT NOT NULL,
    cuota_anterior INT NOT NULL,
    incremento INT NOT NULL,
    cuota_nueva INT NOT NULL,
    id_usuario INT NOT NULL,
    motivo VARCHAR(250) NOT NULL DEFAULT '',
    fecha DATETIME NOT NULL,
    PRIMARY KEY (id),
    KEY idx_sucursal_cuota_log_fecha (id_sucursal, fecha),
    KEY idx_sucursal_cuota_log_cuota (id_sucursal_cuota, fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci//
DELIMITER ;
