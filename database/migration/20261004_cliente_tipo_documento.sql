-- Permite registrar DNI y RUC; guarda la razón social completa de personas jurídicas.
ALTER TABLE cliente
    ADD COLUMN tipo_documento VARCHAR(3) NOT NULL DEFAULT 'DNI' AFTER documento;

UPDATE cliente
SET tipo_documento = CASE
    WHEN CHAR_LENGTH(TRIM(documento)) = 11 THEN 'RUC'
    ELSE 'DNI'
END;

ALTER TABLE cliente
    MODIFY COLUMN nombres VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;

ALTER TABLE cliente
    ADD INDEX idx_cliente_sucursal_tipo_documento (id_sucursal, tipo_documento, documento);
