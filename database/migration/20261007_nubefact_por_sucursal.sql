-- Guarda rutas/secretos NubeFact por sucursal y separa los correlativos.
-- Las claves API se capturan desde Sucursales; no agregar tokens a este SQL.
CREATE TABLE IF NOT EXISTS sucursal_nubefact (
    id_sucursal INT NOT NULL,
    entorno_activo ENUM('demo', 'production') NOT NULL DEFAULT 'demo',
    demo_url VARCHAR(500) NULL,
    demo_token VARCHAR(512) NULL,
    demo_serie_boleta CHAR(4) NULL,
    demo_serie_factura CHAR(4) NULL,
    demo_numero_inicial_boleta INT UNSIGNED NOT NULL DEFAULT 1,
    demo_numero_inicial_factura INT UNSIGNED NOT NULL DEFAULT 1,
    production_url VARCHAR(500) NULL,
    production_token VARCHAR(512) NULL,
    production_serie_boleta CHAR(4) NULL,
    production_serie_factura CHAR(4) NULL,
    production_numero_inicial_boleta INT UNSIGNED NOT NULL DEFAULT 1,
    production_numero_inicial_factura INT UNSIGNED NOT NULL DEFAULT 1,
    actualizado_por INT NULL,
    actualizado_en DATETIME NULL,
    PRIMARY KEY (id_sucursal)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DELIMITER //
DROP PROCEDURE IF EXISTS migrar_nubefact_por_sucursal//
CREATE PROCEDURE migrar_nubefact_por_sucursal()
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema = DATABASE()
          AND table_name = 'nubefact_comprobante'
          AND column_name = 'id_sucursal'
    ) THEN
        ALTER TABLE nubefact_comprobante ADD COLUMN id_sucursal INT NULL AFTER id_pedido;
    END IF;

    UPDATE nubefact_comprobante c
    INNER JOIN pedido p ON p.id = c.id_pedido
    INNER JOIN personal u ON u.IDPERSONAL = p.usuario
    SET c.id_sucursal = u.IDSUCURSAL
    WHERE c.id_sucursal IS NULL;

    IF EXISTS (SELECT 1 FROM nubefact_comprobante WHERE id_sucursal IS NULL) THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Hay comprobantes sin sucursal; relaciona sus tickets antes de continuar.';
    END IF;

    ALTER TABLE nubefact_comprobante MODIFY COLUMN id_sucursal INT NOT NULL;

    IF EXISTS (
        SELECT 1 FROM information_schema.statistics
        WHERE table_schema = DATABASE()
          AND table_name = 'nubefact_comprobante'
          AND index_name = 'uq_nubefact_serie_numero'
    ) THEN
        ALTER TABLE nubefact_comprobante DROP INDEX uq_nubefact_serie_numero;
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM information_schema.statistics
        WHERE table_schema = DATABASE()
          AND table_name = 'nubefact_comprobante'
          AND index_name = 'uq_nubefact_sucursal_serie_numero'
    ) THEN
        ALTER TABLE nubefact_comprobante
            ADD UNIQUE KEY uq_nubefact_sucursal_serie_numero (id_sucursal, tipo_de_comprobante, serie, numero);
    END IF;
END//

CALL migrar_nubefact_por_sucursal()//
DROP PROCEDURE migrar_nubefact_por_sucursal//
DELIMITER ;

CREATE TABLE IF NOT EXISTS nubefact_secuencia_sucursal (
    id_sucursal INT NOT NULL,
    tipo_de_comprobante TINYINT UNSIGNED NOT NULL,
    serie CHAR(4) NOT NULL,
    ultimo_numero INT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (id_sucursal, tipo_de_comprobante, serie)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO nubefact_secuencia_sucursal (id_sucursal, tipo_de_comprobante, serie, ultimo_numero)
SELECT id_sucursal, tipo_de_comprobante, serie, MAX(numero)
FROM nubefact_comprobante
GROUP BY id_sucursal, tipo_de_comprobante, serie
ON DUPLICATE KEY UPDATE ultimo_numero = GREATEST(ultimo_numero, VALUES(ultimo_numero));
