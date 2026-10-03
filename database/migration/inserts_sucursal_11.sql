-- Export de los registros asociados a IDSUCURSAL = 11 en la base local.
-- Importar en cPanel sobre la base destino vacía para estos IDs.
-- Incluye datos personales y hashes de contraseña de los usuarios de la sucursal.
SET NAMES utf8mb4;
START TRANSACTION;

INSERT INTO sucursal (IDSUCURSAL, SUCURSAL, DISTRITO, DPTO, DIRECCION, TLF, IDESTADO)
VALUES (11, 'AMV SOLUCIONES', 'SAN MIGUEL', 'LIMA', 'ANGELICA PALMA 128', '942890820', 1);

INSERT INTO sucursal_cuota (id, id_sucursal, cuota, estado, fecha, tipo)
VALUES (4, 11, 1000, 1, '2026-10-03 01:24:03', 'Ventas');

INSERT INTO cargo (id, nombre, estado) VALUES (1, 'ADMIN', 1);
INSERT INTO cargo (id, nombre, estado) VALUES (5, 'GESTOR', 1);

INSERT INTO personal (IDPERSONAL, APELLIDOS, NOMBRES, FECHANAC, SEXO, DOC, ESTCIV, CARFAM, NUMHIJ, DIRECCION, DISTRITO, DPTO, REFDIR, TLF, CEL, EMAIL, GRADOINS, CARGO, IDSUCURSAL, USUARIO, PASSWORD, IDESTADO, fecha_registro, fecha_baja, id_cartera)
VALUES (1, 'MONTALVAN', 'ALEJANDRO', '1991-08-04', 1, '46798772', 1, 1, 1, 'ANGELICA PALMA 12', 'SAN MIGUEL', 'LIMA ', 'TOTTUS MARINA', '942890820', '942890820', 'alejandro.montalvan@fortelcorp.com', 5, 1, 11, 'PHP.IO', '8f569466d9a3e274d9d63ab6ff52b46d', 1, '2020-08-19 00:00:00', NULL, 0);
INSERT INTO personal (IDPERSONAL, APELLIDOS, NOMBRES, FECHANAC, SEXO, DOC, ESTCIV, CARFAM, NUMHIJ, DIRECCION, DISTRITO, DPTO, REFDIR, TLF, CEL, EMAIL, GRADOINS, CARGO, IDSUCURSAL, USUARIO, PASSWORD, IDESTADO, fecha_registro, fecha_baja, id_cartera)
VALUES (226, 'PERCA VILCAHUAMAN', 'WILLY JOEL', NULL, 1, '48118043', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '987654321', NULL, 'wperca@gmail.com', NULL, 5, 11, 'willy.perca', '735caf4871d5f8a9fa6879c8fd8b797d', 1, '2026-10-03 00:38:55', '1900-01-01 00:00:00', 11);

INSERT INTO categoria (id, tpo, nombre, estado, fecha_creacion, id_sucursal) VALUES (69, 'PRODUCTO', 'Accesorios', 1, '2026-10-03 01:18:19', 11);
INSERT INTO categoria (id, tpo, nombre, estado, fecha_creacion, id_sucursal) VALUES (68, 'PRODUCTO', 'Merchandising', 1, '2026-10-03 01:15:42', 11);
INSERT INTO categoria (id, tpo, nombre, estado, fecha_creacion, id_sucursal) VALUES (67, 'SERVICIO', 'DOCUMENTO', 1, '2026-10-03 00:54:23', 11);
INSERT INTO categoria (id, tpo, nombre, estado, fecha_creacion, id_sucursal) VALUES (65, 'SERVICIO', 'ENCOMIENDA', 1, '2026-10-03 00:53:17', 11);
INSERT INTO categoria (id, tpo, nombre, estado, fecha_creacion, id_sucursal) VALUES (66, 'SERVICIO', 'EQUIPAJE', 1, '2026-10-03 00:54:06', 11);
INSERT INTO categoria (id, tpo, nombre, estado, fecha_creacion, id_sucursal) VALUES (64, 'SERVICIO', 'TRANSPORTE', 1, '2026-10-03 00:52:58', 11);

INSERT INTO cliente (id, nombres, apellidos, documento, email, sexo, fecha_creacion, id_sucursal, telefono)
VALUES (1838, 'ALEJANDRO MANUEL', 'MONTALVAN BRAVO', '46798772', 'alex@gmail.com', 1, '2026-10-03 01:05:07', 11, '942890820');

INSERT INTO product_service (id, nombre, categoria, precio, estado, stock, fecha_creacion, id_sucursal, medida)
VALUES (917, 'Chiclayo - Piura', 64, 35.00, 1, 0, '2026-10-03 00:56:17', 11, '');
INSERT INTO product_service (id, nombre, categoria, precio, estado, stock, fecha_creacion, id_sucursal, medida)
VALUES (918, 'Chiclayo - Trujillo', 64, 30.00, 1, 0, '2026-10-03 00:56:54', 11, '');
INSERT INTO product_service (id, nombre, categoria, precio, estado, stock, fecha_creacion, id_sucursal, medida)
VALUES (920, 'Polo', 68, 20.00, 1, 15, '2026-10-03 01:19:06', 11, '');
INSERT INTO product_service (id, nombre, categoria, precio, estado, stock, fecha_creacion, id_sucursal, medida)
VALUES (921, 'Gorra', 68, 10.00, 1, 6, '2026-10-03 01:20:48', 11, '');
INSERT INTO product_service (id, nombre, categoria, precio, estado, stock, fecha_creacion, id_sucursal, medida)
VALUES (919, 'Audifonos', 69, 10.00, 1, 50, '2026-10-03 01:18:37', 11, '');

INSERT INTO pedido (id, cliente, usuario, user_registro, fecha, fecha_registro, dscto, tipo_dscto, pago)
VALUES (14279, 1838, 226, 1, '2026-10-03', '2026-10-03 01:21:16', 0.00, 'NO APLICA', 'EFECTIVO');
INSERT INTO detalle_pedido (id_pedido, id_productservice, precio, cantidad, subtotal) VALUES (14279, 917, 35.00, 1, 35.00);
INSERT INTO detalle_pedido (id_pedido, id_productservice, precio, cantidad, subtotal) VALUES (14279, 919, 10.00, 1, 10.00);

INSERT INTO stock_black (id, id_product, id_pedido, tipo, stock, fecha, user) VALUES (36877, 919, 0, 'E', 50, '2026-10-03 01:18:37', 'PHP.IO');
INSERT INTO stock_black (id, id_product, id_pedido, tipo, stock, fecha, user) VALUES (36878, 920, 0, 'E', 15, '2026-10-03 01:19:06', 'PHP.IO');
INSERT INTO stock_black (id, id_product, id_pedido, tipo, stock, fecha, user) VALUES (36879, 921, 0, 'E', 6, '2026-10-03 01:20:48', 'PHP.IO');
INSERT INTO stock_black (id, id_product, id_pedido, tipo, stock, fecha, user) VALUES (36880, 919, 14279, 'S', 1, '2026-10-03 01:21:16', 'PHP.IO');

INSERT INTO login (id, tipo, fecha, id_user, ip) VALUES (1, 'OUT', '2026-09-17 02:46:44', 1, '127.0.0.1');
INSERT INTO login (id, tipo, fecha, id_user, ip) VALUES (2, 'IN', '2026-09-17 02:46:55', 1, '127.0.0.1');
INSERT INTO login (id, tipo, fecha, id_user, ip) VALUES (3, 'OUT', '2026-09-17 02:57:47', 1, '127.0.0.1');
INSERT INTO login (id, tipo, fecha, id_user, ip) VALUES (4, 'IN', '2026-09-17 16:12:13', 1, '127.0.0.1');
INSERT INTO login (id, tipo, fecha, id_user, ip) VALUES (5, 'IN', '2026-09-17 20:57:56', 1, '127.0.0.1');
INSERT INTO login (id, tipo, fecha, id_user, ip) VALUES (6, 'IN', '2026-09-18 08:06:16', 1, '127.0.0.1');
INSERT INTO login (id, tipo, fecha, id_user, ip) VALUES (7, 'IN', '2026-10-03 00:16:16', 1, '127.0.0.1');
INSERT INTO login (id, tipo, fecha, id_user, ip) VALUES (8, 'OUT', '2026-10-03 00:18:23', 1, '127.0.0.1');
INSERT INTO login (id, tipo, fecha, id_user, ip) VALUES (9, 'IN', '2026-10-03 00:37:17', 1, '127.0.0.1');

COMMIT;
