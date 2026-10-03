## Xintra Elephpant 🐘

[![forthebadge](http://forthebadge.com/badges/uses-css.svg)](https://www.linkedin.com/in/drphp/)
[![forthebadge](http://forthebadge.com/badges/built-with-love.svg)](https://www.linkedin.com/in/drphp/)

[![Video](https://img.youtube.com/vi/G7heyYn1CBk/0.jpg)](https://www.youtube.com/watch?v=G7heyYn1CBk)

[![Video Demo](https://img.shields.io/badge/YouTube-FF0000?style=for-the-badge&logo=youtube)](https://www.youtube.com/watch?v=G7heyYn1CBk)

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

NUBEFACT_ENV=demo
NUBEFACT_URL=https://api.nubefact.com/api/v1/REEMPLAZAR_RUTA_DEMO
NUBEFACT_TOKEN=REEMPLAZAR_TOKEN_DEMO
NUBEFACT_SERIE_BOLETA=BBB1
NUBEFACT_SERIE_FACTURA=FFF1
NUBEFACT_NUMERO_INICIAL_BOLETA=1
NUBEFACT_NUMERO_INICIAL_FACTURA=1
```

`TURNSTILE_SECRET_KEY` puede dejarse vacío en un entorno local sin validación Turnstile. En producción, configura las claves y el hostname autorizados en Cloudflare. Define `UBUNTUX_API_URL` si se usará la consulta externa del documento; `PROMOCODE` contiene los códigos promocionales habilitados según el formato que espera el backend.

Para emitir comprobantes, configura la ruta y el token de tu cuenta DEMO NubeFact en `.env`. Las series del ejemplo (`BBB1` y `FFF1`) deben coincidir con las habilitadas para tu cuenta. Ajusta los números iniciales si esas series ya tienen documentos emitidos. En modo demo se acepta la ruta de `demo.nubefact.com` o una ruta asignada en `api.nubefact.com` a una cuenta DEMO. Producción requiere cambiar explícitamente `NUBEFACT_ENV`, confirmar la ruta y configurar los próximos correlativos. Nunca publiques el token ni lo guardes en JavaScript, SQL o el repositorio. Revisa `database/migration/20261003_nubefact_comprobantes.sql` y aplícala antes de habilitar la emisión.

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

Consulta los estándares disponibles en `.ia-context/` antes de implementar cambios.

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
