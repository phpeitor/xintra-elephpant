-- Ejecutar una vez en la base de datos bd_black.
-- La rutina evita volver a crear índices con estos mismos nombres.
DELIMITER //
DROP PROCEDURE IF EXISTS agregar_indice_sucursal_si_falta//
CREATE PROCEDURE agregar_indice_sucursal_si_falta(
    IN p_tabla VARCHAR(64),
    IN p_indice VARCHAR(64),
    IN p_columnas VARCHAR(255)
)
BEGIN
    IF NOT EXISTS (
        SELECT 1
        FROM information_schema.statistics
        WHERE table_schema = DATABASE()
          AND table_name = p_tabla
          AND index_name = p_indice
    ) THEN
        SET @sql_indice = CONCAT(
            'ALTER TABLE `', REPLACE(p_tabla, '`', '``'),
            '` ADD INDEX `', REPLACE(p_indice, '`', '``'),
            '` (', p_columnas, ')'
        );
        PREPARE stmt_indice FROM @sql_indice;
        EXECUTE stmt_indice;
        DEALLOCATE PREPARE stmt_indice;
    END IF;
END//

CALL agregar_indice_sucursal_si_falta('personal', 'idx_personal_sucursal_estado_id', '`IDSUCURSAL`, `IDESTADO`, `IDPERSONAL`')//
CALL agregar_indice_sucursal_si_falta('personal', 'idx_personal_sucursal_doc', '`IDSUCURSAL`, `DOC`')//
CALL agregar_indice_sucursal_si_falta('cliente', 'idx_cliente_sucursal_id', '`id_sucursal`, `id`')//
CALL agregar_indice_sucursal_si_falta('categoria', 'idx_categoria_sucursal_estado_tipo', '`id_sucursal`, `estado`, `tpo`, `nombre`')//
CALL agregar_indice_sucursal_si_falta('product_service', 'idx_producto_sucursal_estado_categoria', '`id_sucursal`, `estado`, `categoria`, `id`')//
CALL agregar_indice_sucursal_si_falta('pedido', 'idx_pedido_fecha_id', '`fecha`, `id`')//
CALL agregar_indice_sucursal_si_falta('pedido', 'idx_pedido_usuario_fecha_id', '`usuario`, `fecha`, `id`')//
CALL agregar_indice_sucursal_si_falta('detalle_pedido', 'idx_detalle_pedido_producto_pedido', '`id_productservice`, `id_pedido`')//
CALL agregar_indice_sucursal_si_falta('stock_black', 'idx_stock_pedido_tipo', '`id_pedido`, `tipo`')//
CALL agregar_indice_sucursal_si_falta('stock_black', 'idx_stock_producto_tipo_fecha', '`id_product`, `tipo`, `fecha`')//
CALL agregar_indice_sucursal_si_falta('login', 'idx_login_tipo_usuario_fecha', '`tipo`, `id_user`, `fecha`')//
CALL agregar_indice_sucursal_si_falta('asistencia_personal', 'idx_asistencia_fecha_personal', '`fecha`, `id_personal`')//

DROP PROCEDURE agregar_indice_sucursal_si_falta//
DELIMITER ;
