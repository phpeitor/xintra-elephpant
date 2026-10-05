CREATE TABLE IF NOT EXISTS usuario_permisos_configurados (
    id_usuario INT NOT NULL PRIMARY KEY,
    actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_usuario_permisos_configurados_personal
        FOREIGN KEY (id_usuario) REFERENCES personal (IDPERSONAL) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS usuario_permisos (
    id_usuario INT NOT NULL,
    permiso VARCHAR(40) NOT NULL,
    PRIMARY KEY (id_usuario, permiso),
    CONSTRAINT fk_usuario_permisos_configuracion
        FOREIGN KEY (id_usuario) REFERENCES usuario_permisos_configurados (id_usuario) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
