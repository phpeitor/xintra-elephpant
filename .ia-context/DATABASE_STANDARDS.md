# Estándares de base de datos - Xintra Elephpant

## Plataforma y acceso

- Motor: MySQL/MariaDB 5.7 o superior, según las funciones usadas por cada migración.
- PHP accede a la base mediante PDO en `database/conexion.php`.
- Configuración de conexión mediante `.env` y `config/bootstrap.php`.
- No codificar credenciales, nombres de host productivos ni secretos en el código o SQL versionado.

## Esquema y migraciones

- Mantener los nombres de tablas y columnas existentes salvo que el cambio requiera una migración coordinada.
- Guardar migraciones nuevas en `database/migration/` y explicar su propósito y orden de ejecución.
- Preferir migraciones repetibles o con comprobación previa cuando sea viable y compatible con la versión del motor.
- Incluir defaults/backfill para columnas nuevas cuando haya datos existentes.
- Revisar tipos, claves, restricciones, valores actuales y relaciones antes de alterar tablas.
- Ejecutar la migración antes de desplegar código que dependa del nuevo esquema.
- No lanzar cambios destructivos o con pérdida de datos sin autorización explícita y respaldo.

## Sucursales y aislamiento

- `personal.IDSUCURSAL` determina la sucursal del usuario autenticado.
- Las tablas de negocio que tengan `id_sucursal` deben leer y escribir usando la sucursal de la sesión autenticada.
- En SQL usar el alcance dinámico establecido por la conexión (`@id_sucursal`); no fijar un ID de sucursal literal.
- Las operaciones sobre datos relacionados deben comprobar que todas las referencias pertenecen a la sucursal activa. Esto incluye personal, clientes, categorías, items, tickets y movimientos de stock.
- No confiar en IDs, hashes o sucursales enviados por el navegador para conceder acceso.

## Consultas e índices

- Indexar columnas de filtros, relaciones y ordenamiento según las consultas reales.
- Para tablas con sucursal, considerar índices compuestos que comiencen por `id_sucursal`/`IDSUCURSAL` y continúen con estado, tipo, fecha o clave de relación según el patrón de consulta.
- Antes de añadir índices, revisar `SHOW INDEX` para evitar duplicados o índices con prefijos redundantes.
- Mantener búsquedas por fecha sargables: usar rangos semiabiertos (`>= fecha_inicio` y `< fecha_fin_exclusiva`) en vez de envolver la columna con `DATE()`.
- Usar `EXPLAIN` en consultas de volumen significativo y revisar `GROUP BY`, agregaciones y joins.
- Ejecutar `ANALYZE TABLE` para actualizar estadísticas cuando sea necesario. Usar `OPTIMIZE TABLE` solo si se requiere recuperar espacio o hay fragmentación relevante; puede reconstruir tablas InnoDB.

## Integridad y datos sensibles

- Mantener relaciones lógicas entre cabeceras y detalles, tickets y productos, personal y horarios/asistencias.
- Usar transacciones para operaciones que escriban varias tablas.
- Preferir baja lógica donde el sistema ya conserva historial.
- Proteger datos personales, documentos e información de acceso.
- No versionar dumps con datos reales, hashes de contraseñas, IPs ni otros datos privados. Los datos de ejemplo deben ser ficticios.

## Lista de verificación

1. Las consultas aplican el alcance de sucursal y tienen parámetros preparados cuando corresponda.
2. Las referencias relacionadas se validan también en backend.
3. La migración contempla datos existentes y está en `database/migration/`.
4. Los índices propuestos corresponden a consultas observadas y no duplican índices actuales.
5. El despliegue documenta el orden de migración y código.
