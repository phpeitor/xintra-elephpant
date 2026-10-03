-- Persistencia de emisión de boletas/facturas NubeFact y correlativos locales.
CREATE TABLE IF NOT EXISTS nubefact_comprobante (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_pedido INT NOT NULL,
    tipo_de_comprobante TINYINT UNSIGNED NOT NULL,
    serie CHAR(4) NOT NULL,
    numero INT UNSIGNED NOT NULL,
    codigo_unico VARCHAR(20) NOT NULL,
    estado ENUM('PENDIENTE', 'EMITIDO', 'ERROR') NOT NULL DEFAULT 'PENDIENTE',
    request_json MEDIUMTEXT NOT NULL,
    response_json MEDIUMTEXT NULL,
    enlace VARCHAR(2048) NULL,
    enlace_del_pdf VARCHAR(2048) NULL,
    enlace_del_xml VARCHAR(2048) NULL,
    enlace_del_cdr VARCHAR(2048) NULL,
    aceptada_por_sunat TINYINT(1) NULL,
    mensaje TEXT NULL,
    id_usuario_emision INT NOT NULL,
    fecha_emision DATETIME NOT NULL,
    fecha_respuesta DATETIME NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_nubefact_pedido (id_pedido),
    UNIQUE KEY uq_nubefact_serie_numero (tipo_de_comprobante, serie, numero),
    UNIQUE KEY uq_nubefact_codigo_unico (codigo_unico),
    KEY idx_nubefact_estado_fecha (estado, fecha_emision)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS nubefact_secuencia (
    tipo_de_comprobante TINYINT UNSIGNED NOT NULL,
    serie CHAR(4) NOT NULL,
    ultimo_numero INT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (tipo_de_comprobante, serie)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
