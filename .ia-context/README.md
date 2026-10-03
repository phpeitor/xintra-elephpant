# Contexto del proyecto

Este directorio contiene las reglas y referencias vigentes para trabajar en Xintra Elephpant. La aplicación administra usuarios y datos operativos segmentados por sucursal.

## Stack y estructura

- PHP con PDO y sesiones nativas; MySQL/MariaDB.
- Controladores en `controller/`, modelos en `model/`, vistas en `views/` y parciales en `layout/`.
- JavaScript en `assets/js/` y CSS en `assets/css/`.
- Configuración y sesión desde `config/bootstrap.php`; variables de entorno en `.env`.
- Migraciones SQL en `database/migration/`.

## Documentos

- `AGENTS_ROLES.md`: áreas de responsabilidad para implementación, datos, QA y seguridad.
- `BACKEND.md`: patrones de endpoints, sesión, validación y acceso a datos.
- `DATABASE_STANDARDS.md`: esquema, migraciones, índices e aislamiento por sucursal.
- `FRONTEND.md`: estructura de vistas y reglas de CSS/JavaScript.
- `REGLAS_DESARROLLO.md`: reglas generales y checklist de verificación.

## Prioridades de dominio

- La sucursal activa se determina por el usuario autenticado y debe imponerse desde el backend.
- Los módulos de negocio incluyen personal, clientes, categorías, productos/servicios, stock, tickets, horarios, asistencia y reportes.
- Consultar `README.md` para instalación, estructura y operación del proyecto.
