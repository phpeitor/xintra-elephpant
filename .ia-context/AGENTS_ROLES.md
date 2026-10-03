# Roles de trabajo - Xintra Elephpant

## Contexto del sistema

- Aplicación web PHP para gestión operativa de sucursales.
- Funcionalidades principales: usuarios, clientes, categorías, productos/servicios, inventario, tickets/ventas, horarios, asistencia, dashboard y reportes.
- Backend organizado en `controller/`, `model/`, `config/` y `database/`.
- Frontend basado en las vistas PHP, el template Xintra, JavaScript en `assets/js/` y CSS en `assets/css/`.
- Persistencia MySQL/MariaDB mediante PDO.
- Consultar `README.md`, `BACKEND.md`, `FRONTEND.md`, `DATABASE_STANDARDS.md` y `REGLAS_DESARROLLO.md` antes de cambios que afecten esas áreas.

## Rol Full Stack

**Responsabilidad:** implementar el flujo solicitado de punta a punta.

- Mantener la separación entre vistas, controladores, modelos, JavaScript y CSS.
- Implementar validaciones tanto en frontend como en backend.
- Mantener respuestas JSON estables para llamadas `fetch`.
- Aplicar permisos y filtros de sucursal en el servidor; nunca depender solo de ocultar controles en la UI.
- Considerar los flujos relacionados al modificar usuarios, clientes, items, inventario, tickets o asistencia.

## Rol Base de datos

**Responsabilidad:** preservar integridad y rendimiento de los datos.

- Revisar esquema, consultas, índices, relaciones y volumen esperado.
- Crear migraciones en `database/migration/` para cambios de esquema.
- Considerar datos preexistentes, relaciones y secuencia de despliegue.
- Filtrar y asignar la sucursal autenticada de forma dinámica.
- No incluir credenciales ni datos personales reales en scripts versionados.

## Rol QA y requisitos

**Responsabilidad:** comprobar el comportamiento solicitado y prevenir regresiones.

- Verificar casos normales, validaciones, errores, estados vacíos y permisos.
- Probar que usuarios de sucursales distintas no puedan consultar ni modificar datos ajenos.
- Verificar que una escritura se vea reflejada en listados, reportes e indicadores relacionados.
- Ejecutar comprobaciones de sintaxis apropiadas a los archivos modificados.

## Rol Seguridad y operaciones

**Responsabilidad:** revisar autenticación, permisos, secretos y despliegues.

- Validar sesión y autorización en cada endpoint protegido.
- Usar consultas preparadas y validar entradas en el backend.
- No registrar ni exponer contraseñas, tokens, datos privados ni errores internos.
- Confirmar que las migraciones se ejecuten antes del código que dependa de ellas.
- No ejecutar cambios destructivos o de producción sin autorización explícita.

## Flujo sugerido

1. Precisar el comportamiento solicitado y los módulos afectados.
2. Revisar patrones y datos existentes antes de cambiar esquema o lógica.
3. Implementar el cambio más acotado que cubra el flujo completo.
4. Añadir una migración si hay cambios de esquema.
5. Verificar sintaxis, seguridad y regresiones relevantes.
6. Informar los archivos cambiados, validaciones y cualquier paso manual de despliegue.
