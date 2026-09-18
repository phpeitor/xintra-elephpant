CREATE TABLE IF NOT EXISTS horario_personal (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_personal INT NOT NULL,
    dia_semana TINYINT UNSIGNED NOT NULL,
    hora_inicio TIME NULL,
    hora_fin TIME NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (id),
    UNIQUE KEY uq_horario_personal_dia (id_personal, dia_semana),
    KEY idx_horario_personal (id_personal),
    CONSTRAINT fk_horario_personal
        FOREIGN KEY (id_personal) REFERENCES personal (IDPERSONAL)
        ON DELETE CASCADE ON UPDATE CASCADE
);
