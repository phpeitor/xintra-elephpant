-- Catálogo de proveedores aislado por sucursal.
CREATE TABLE IF NOT EXISTS proveedor (
    id INT NOT NULL AUTO_INCREMENT,
    id_sucursal INT NOT NULL,
    tipo_documento ENUM('RUC', 'DNI') NOT NULL DEFAULT 'RUC',
    documento VARCHAR(11) NOT NULL,
    razon_social VARCHAR(150) NOT NULL,
    email VARCHAR(150) NULL,
    telefono VARCHAR(20) NULL,
    direccion VARCHAR(200) NULL,
    estado TINYINT(1) NOT NULL DEFAULT 1,
    fecha_creacion DATETIME NOT NULL,
    fecha_actualizacion DATETIME NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_proveedor_sucursal_documento (id_sucursal, tipo_documento, documento),
    KEY idx_proveedor_sucursal_estado (id_sucursal, estado),
    CONSTRAINT fk_proveedor_sucursal FOREIGN KEY (id_sucursal)
        REFERENCES sucursal (IDSUCURSAL)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
