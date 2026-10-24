-- Permite marcar compras recibidas y enlazar entradas de stock con su compra.
ALTER TABLE compra
    MODIFY COLUMN estado ENUM('PENDIENTE', 'RECIBIDA', 'ANULADA') NOT NULL DEFAULT 'PENDIENTE';

SET @recibido_por_exists := (
    SELECT COUNT(1) FROM information_schema.columns
    WHERE table_schema = DATABASE() AND table_name = 'compra' AND column_name = 'recibido_por'
);
SET @recibido_por_sql := IF(
    @recibido_por_exists = 0,
    'ALTER TABLE compra ADD COLUMN recibido_por INT NULL AFTER estado',
    'SELECT 1'
);
PREPARE recibido_por_stmt FROM @recibido_por_sql;
EXECUTE recibido_por_stmt;
DEALLOCATE PREPARE recibido_por_stmt;

SET @fecha_recepcion_exists := (
    SELECT COUNT(1) FROM information_schema.columns
    WHERE table_schema = DATABASE() AND table_name = 'compra' AND column_name = 'fecha_recepcion'
);
SET @fecha_recepcion_sql := IF(
    @fecha_recepcion_exists = 0,
    'ALTER TABLE compra ADD COLUMN fecha_recepcion DATETIME NULL AFTER recibido_por',
    'SELECT 1'
);
PREPARE fecha_recepcion_stmt FROM @fecha_recepcion_sql;
EXECUTE fecha_recepcion_stmt;
DEALLOCATE PREPARE fecha_recepcion_stmt;

SET @id_compra_stock_exists := (
    SELECT COUNT(1) FROM information_schema.columns
    WHERE table_schema = DATABASE() AND table_name = 'stock_black' AND column_name = 'id_compra'
);
SET @id_compra_stock_sql := IF(
    @id_compra_stock_exists = 0,
    'ALTER TABLE stock_black ADD COLUMN id_compra INT NULL AFTER id_pedido',
    'SELECT 1'
);
PREPARE id_compra_stock_stmt FROM @id_compra_stock_sql;
EXECUTE id_compra_stock_stmt;
DEALLOCATE PREPARE id_compra_stock_stmt;

SET @idx_stock_compra_exists := (
    SELECT COUNT(1) FROM information_schema.statistics
    WHERE table_schema = DATABASE() AND table_name = 'stock_black' AND index_name = 'idx_stock_black_compra'
);
SET @idx_stock_compra_sql := IF(
    @idx_stock_compra_exists = 0,
    'ALTER TABLE stock_black ADD KEY idx_stock_black_compra (id_compra)',
    'SELECT 1'
);
PREPARE idx_stock_compra_stmt FROM @idx_stock_compra_sql;
EXECUTE idx_stock_compra_stmt;
DEALLOCATE PREPARE idx_stock_compra_stmt;
