-- Registro de comprobantes de compra y sus líneas, aislado por sucursal.
CREATE TABLE IF NOT EXISTS compra (
    id INT NOT NULL AUTO_INCREMENT,
    id_sucursal INT NOT NULL,
    id_proveedor INT NOT NULL,
    id_usuario INT NOT NULL,
    tipo_comprobante ENUM('FACTURA', 'BOLETA', 'TICKET', 'OTRO') NOT NULL,
    serie VARCHAR(20) NOT NULL,
    numero VARCHAR(20) NOT NULL,
    fecha DATE NOT NULL,
    moneda CHAR(3) NOT NULL DEFAULT 'PEN',
    subtotal DECIMAL(14,2) NOT NULL DEFAULT 0,
    igv DECIMAL(14,2) NOT NULL DEFAULT 0,
    total DECIMAL(14,2) NOT NULL DEFAULT 0,
    estado ENUM('PENDIENTE', 'ANULADA') NOT NULL DEFAULT 'PENDIENTE',
    observaciones VARCHAR(500) NULL,
    fecha_registro DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_compra_comprobante (id_sucursal, tipo_comprobante, serie, numero),
    KEY idx_compra_sucursal_fecha (id_sucursal, fecha),
    KEY idx_compra_sucursal_proveedor_fecha (id_sucursal, id_proveedor, fecha),
    CONSTRAINT fk_compra_sucursal FOREIGN KEY (id_sucursal)
        REFERENCES sucursal (IDSUCURSAL)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_compra_proveedor FOREIGN KEY (id_proveedor)
        REFERENCES proveedor (id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_compra_usuario FOREIGN KEY (id_usuario)
        REFERENCES personal (IDPERSONAL)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS compra_detalle (
    id INT NOT NULL AUTO_INCREMENT,
    id_compra INT NOT NULL,
    id_sucursal INT NOT NULL,
    id_productservice INT NOT NULL,
    descripcion VARCHAR(250) NOT NULL,
    cantidad DECIMAL(12,3) NOT NULL,
    costo_unitario DECIMAL(12,4) NOT NULL,
    porcentaje_igv DECIMAL(5,2) NOT NULL DEFAULT 18.00,
    subtotal DECIMAL(14,2) NOT NULL,
    igv DECIMAL(14,2) NOT NULL,
    total DECIMAL(14,2) NOT NULL,
    PRIMARY KEY (id),
    KEY idx_compra_detalle_compra (id_compra, id_sucursal),
    CONSTRAINT fk_compra_detalle_compra FOREIGN KEY (id_compra)
        REFERENCES compra (id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_compra_detalle_sucursal FOREIGN KEY (id_sucursal)
        REFERENCES sucursal (IDSUCURSAL)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_compra_detalle_producto FOREIGN KEY (id_productservice)
        REFERENCES product_service (id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
