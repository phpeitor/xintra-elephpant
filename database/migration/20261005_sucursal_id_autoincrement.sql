-- Garantiza IDs únicos y autoincrementales para el CRUD de sucursales.
-- Falla sin alterar la tabla si detecta IDs duplicados.
DELIMITER //
DROP PROCEDURE IF EXISTS migrar_sucursal_id_autoincrement//
CREATE PROCEDURE migrar_sucursal_id_autoincrement()
BEGIN
    IF NOT EXISTS (
        SELECT 1
        FROM information_schema.statistics
        WHERE table_schema = DATABASE()
          AND table_name = 'sucursal'
          AND index_name = 'PRIMARY'
    ) THEN
        IF EXISTS (
            SELECT IDSUCURSAL
            FROM sucursal
            GROUP BY IDSUCURSAL
            HAVING COUNT(*) > 1
        ) THEN
            SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'Hay IDSUCURSAL duplicados; corrígelos antes de continuar.';
        END IF;
        ALTER TABLE sucursal ADD PRIMARY KEY (IDSUCURSAL);
    END IF;

    IF NOT EXISTS (
        SELECT 1
        FROM information_schema.columns
        WHERE table_schema = DATABASE()
          AND table_name = 'sucursal'
          AND column_name = 'IDSUCURSAL'
          AND EXTRA LIKE '%auto_increment%'
    ) THEN
        ALTER TABLE sucursal MODIFY COLUMN IDSUCURSAL INT NOT NULL AUTO_INCREMENT;
    END IF;
END//

CALL migrar_sucursal_id_autoincrement()//
DROP PROCEDURE migrar_sucursal_id_autoincrement//
DELIMITER ;
