-- La aplicación omite `id` al insertar categorías; la columna debe generarlo.
ALTER TABLE categoria
    MODIFY COLUMN id INT NOT NULL AUTO_INCREMENT;
