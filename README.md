# Xintra Elephpant

Aplicación web para la gestión operativa de sucursales: usuarios, clientes, categorías, productos y servicios, inventario, tickets/ventas, horarios, asistencia, dashboards y reportes.

## Tecnologías

- PHP 8.0 o superior, PDO y sesiones nativas.
- MySQL 8.0 recomendado (o una versión compatible con CTE y funciones de ventana).
- Composer, `vlucas/phpdotenv` y Dompdf.
- JavaScript vanilla, `fetch`, ApexCharts y el template Xintra.
- Cloudflare Turnstile para protección del inicio de sesión.

## Requisitos

- PHP con las extensiones `curl`, `pdo_mysql`, `openssl`, `mbstring` y `json`.
- Composer y un servidor web compatible (Apache, Nginx o hosting cPanel).
- Una base de datos con el esquema de Xintra Elephpant.

> Los SQL de `database/migration/` son migraciones o tareas puntuales; no constituyen por sí solos un esquema completo para crear una base vacía. Prepara/restaura el esquema de la aplicación antes de aplicar las migraciones requeridas.

## Instalación local

```bash
git clone <url-del-repositorio> xintra-elephpant
cd xintra-elephpant
composer install
```

Configura el servidor web para servir el proyecto y crea `.env` en la raíz. Ejemplo con valores ficticios:

```dotenv
DB_HOST=127.0.0.1
DB_NAME=bd_black
DB_USER=usuario_local
DB_PASS=contraseña_local

IP_API_URL=https://api.ipify.org

TURNSTILE_SITE_KEY=clave-publica
TURNSTILE_SECRET_KEY=clave-privada
TURNSTILE_HOSTNAME=localhost

UBUNTUX_API_URL=https://api.example.test/consulta?dni=
PROMOCODE=
```

`TURNSTILE_SECRET_KEY` puede dejarse vacío en un entorno local sin validación Turnstile. En producción, configura las claves y el hostname autorizados en Cloudflare. Define `UBUNTUX_API_URL` si se usará la consulta externa del documento; `PROMOCODE` contiene los códigos promocionales habilitados según el formato que espera el backend.

Abre la aplicación desde el host configurado, por ejemplo:

```text
http://127.0.0.1/xintra-elephpant/index.php
```

La sesión y configuración común se inicializan en `config/bootstrap.php`. Las vistas protegidas requieren `controller/check_session.php`.

## Estructura

```text
.
├── assets/
│   ├── css/             # Estilos del proyecto
│   ├── js/              # Interacción y consumo de endpoints
│   ├── images/          # Imágenes
│   └── libs/            # Librerías frontend
├── config/              # Bootstrap y servicios externos
├── controller/          # Endpoints y controladores HTTP
├── database/
│   ├── conexion.php     # Conexión PDO
│   └── migration/       # Migraciones y tareas SQL revisables
├── layout/              # Parciales compartidos
├── model/               # Dominio y acceso a datos
├── views/               # Vistas PHP
├── .ia-context/         # Contexto y estándares para el desarrollo
├── index.php            # Entrada e inicio de sesión
└── README.md
```

## Arquitectura y convenciones

1. Las vistas presentan la interfaz; la lógica HTTP vive en `controller/` y la lógica de dominio/acceso a datos en `model/`.
2. Los endpoints AJAX responden JSON con una estructura consistente y códigos HTTP apropiados.
3. Las consultas con datos externos usan PDO y parámetros preparados.
4. La sucursal se obtiene de la sesión autenticada. El backend debe aplicar ese alcance en lecturas, escrituras y relaciones; no se acepta el ID de sucursal enviado por el navegador como autorización.
5. El CSS y JavaScript propios viven en `assets/css/` y `assets/js/`; no se agregan bloques de código ni manejadores inline a las vistas.
6. Los cambios de esquema se documentan como SQL en `database/migration/` y se ejecutan antes del código que dependa de ellos.
7. Usa `ROOT` para includes internos y conserva los parciales comunes en `layout/`.

Consulta `.ia-context/README.md` para el índice de estándares y `.ia-context/` antes de implementar cambios.

## Base de datos y migraciones

- Configura la base seleccionada por `DB_NAME` antes de ejecutar SQL.
- Revisa cada migración y su orden antes de aplicarla; algunos archivos son tareas puntuales o cargas de datos y no deben ejecutarse indiscriminadamente.
- Realiza respaldo antes de cambios de esquema o importaciones de datos.
- Mantén índices en función de las consultas reales y revisa planes con `EXPLAIN` cuando las tablas crezcan.
- No versionar volcados con datos personales, hashes de contraseñas, IPs o credenciales.

## Validación local

Validar PHP:

```bash
php -l index.php
php -l controller/acceso.php
php -l model/ticket.php
php -l views/home.php
```

Validar JavaScript:

```bash
node --check assets/js/loginscript.js
node --check assets/js/sales-dashboard.js
node --check assets/js/analytics-reporte.js
```

Si se edita otra área, valida también los archivos modificados. Antes de cerrar el cambio, revisa `git diff --check` y prueba el flujo afectado en el navegador/servidor disponible.

## Despliegue en hosting/cPanel

1. Despliega el código y las dependencias de Composer (`composer install --no-dev --optimize-autoloader`, si Composer está disponible en el hosting).
2. Configura `.env` con credenciales de producción y protege el archivo para que no sea accesible por HTTP.
3. Verifica que la base de datos y el usuario del hosting tengan los permisos necesarios.
4. Revisa y ejecuta las migraciones requeridas antes de publicar el código dependiente.
5. Configura `TURNSTILE_HOSTNAME` para el dominio público y comprueba `curl`, OpenSSL y acceso HTTPS desde PHP.
6. Verifica inicio de sesión, filtros por sucursal, operaciones de inventario/ventas y carga de assets.

## Diagnóstico

- **Falla el acceso a la base:** revisa `DB_HOST`, `DB_NAME`, usuario, contraseña y permisos del hosting.
- **Turnstile rechaza el login:** comprueba claves, hostname registrado y conectividad HTTPS desde PHP.
- **Un endpoint `fetch` devuelve HTML:** revisa expiración de sesión, redirecciones y la pestaña Network del navegador.
- **No carga un gráfico:** inspecciona primero la respuesta JSON del endpoint correspondiente y luego la consola del navegador.
- **Datos de otra sucursal o vacíos:** comprueba `session_idsucursal`, las columnas de sucursal y el filtro aplicado en el modelo.

## Atribución

Consulta `.licence` para las condiciones de uso y atribución del proyecto.
