# Reglas de desarrollo - Xintra Elephpant

## Arquitectura

- Vistas en `views/` y entrada pública en `index.php`.
- Controladores y endpoints en `controller/`.
- Acceso a datos y lógica de dominio en `model/`.
- Inicialización, sesión y configuración en `config/`.
- Parciales reutilizables de interfaz en `layout/`.
- JavaScript en `assets/js/` y CSS en `assets/css/`.
- Mantener la lógica SQL fuera de las vistas.

## Reglas principales

1. No mezclar código JavaScript ni CSS en vistas PHP/HTML: crear o reutilizar archivos en `assets/js/` y `assets/css/`.
2. Evitar manejadores de eventos inline como `onclick`; enlazar eventos desde JavaScript.
3. Mantener vistas enfocadas en estructura y referencias a assets.
4. Validar método HTTP, formato y límites de los datos en el controlador/modelo.
5. Usar consultas preparadas para datos externos.
6. Los endpoints consumidos por JavaScript devuelven JSON consistente y códigos HTTP apropiados.
7. Cargar `config/bootstrap.php` para configuración global; usar `ROOT` para includes internos.
8. Las vistas protegidas y endpoints protegidos deben verificar sesión y autorización.
9. La sucursal se obtiene de la sesión autenticada; no aceptar suplantaciones desde parámetros del cliente.
10. Validar en backend permisos, alcance de sucursal y referencias entre entidades.
11. Usar transacciones para operaciones compuestas que escriban en varias tablas.
12. No mostrar mensajes de excepción, rutas ni detalles internos en producción.

## Frontend

- Seguir `.ia-context/FRONTEND.md` y el sistema visual del template Xintra.
- Reutilizar componentes y lógica común antes de duplicar comportamientos.
- En flujos dinámicos, cubrir carga, éxito, error, estado vacío y edición cuando corresponda.
- Mantener formularios accesibles con labels, estados deshabilitados y feedback entendible.
- Evitar cambios de estilo globales para resolver un caso puntual; usar estilos específicos de pantalla cuando corresponda.

## Base de datos

- Seguir `.ia-context/DATABASE_STANDARDS.md`.
- Crear migraciones SQL en `database/migration/` cuando cambie el esquema.
- No codificar IDs de sucursal en filtros ni inserts.
- Revisar impacto en datos, relaciones, índices y despliegue antes de cambiar tablas.

## Verificación

- Ejecutar `php -l` en los PHP modificados.
- Ejecutar `node --check` en los JavaScript modificados.
- Revisar `git diff --check`.
- Comprobar el flujo funcional y los límites de sucursal cuando el cambio afecte datos.
- No declarar una prueba de integración si no se ejecutó contra el servicio/base de datos correspondiente.

## Cambios y documentación

- Mantener cambios focalizados y consistentes con los patrones cercanos.
- No borrar ni sobrescribir trabajo preexistente sin revisarlo.
- Documentar cambios en endpoints, scripts de migración o procedimientos operativos cuando afecten su uso.
- No incluir datos productivos o personales reales en documentación o fixtures versionados.
