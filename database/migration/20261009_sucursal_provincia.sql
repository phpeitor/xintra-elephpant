-- Agrega provincia para conservar la selección completa del ubigeo de cada sucursal.
SET @provincia_column_exists := (
    SELECT COUNT(1)
    FROM information_schema.columns
    WHERE table_schema = DATABASE()
      AND table_name = 'sucursal'
      AND column_name = 'PROVINCIA'
);
SET @provincia_column_sql := IF(
    @provincia_column_exists = 0,
    'ALTER TABLE sucursal ADD COLUMN PROVINCIA VARCHAR(200) NULL AFTER DISTRITO',
    'SELECT 1'
);
PREPARE provincia_column_stmt FROM @provincia_column_sql;
EXECUTE provincia_column_stmt;
DEALLOCATE PREPARE provincia_column_stmt;
