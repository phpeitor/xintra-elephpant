CREATE TABLE IF NOT EXISTS asistencia_personal (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_personal INT NOT NULL,
    tipo ENUM('ENTRADA', 'SALIDA') NOT NULL,
    fecha DATETIME NOT NULL,
    PRIMARY KEY (id),
    KEY idx_asistencia_personal_fecha (id_personal, fecha),
    KEY idx_asistencia_fecha (fecha),
    CONSTRAINT fk_asistencia_personal
        FOREIGN KEY (id_personal) REFERENCES personal (IDPERSONAL)
        ON DELETE CASCADE ON UPDATE CASCADE
);
