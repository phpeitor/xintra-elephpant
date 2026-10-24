-- Guarda el RUC emisor asociado a la configuración de facturación por sucursal.
SET @ruc_emisor_column_exists := (
    SELECT COUNT(1)
    FROM information_schema.columns
    WHERE table_schema = DATABASE()
      AND table_name = 'sucursal_nubefact'
      AND column_name = 'ruc_emisor'
);
SET @ruc_emisor_column_sql := IF(
    @ruc_emisor_column_exists = 0,
    'ALTER TABLE sucursal_nubefact ADD COLUMN ruc_emisor CHAR(11) NULL AFTER entorno_activo',
    'SELECT 1'
);
PREPARE ruc_emisor_column_stmt FROM @ruc_emisor_column_sql;
EXECUTE ruc_emisor_column_stmt;
DEALLOCATE PREPARE ruc_emisor_column_stmt;
