## Xintra Elephpant 🐘

[![forthebadge](http://forthebadge.com/badges/uses-css.svg)](https://www.linkedin.com/in/drphp/)
[![forthebadge](http://forthebadge.com/badges/built-with-love.svg)](https://www.linkedin.com/in/drphp/)

[![Video](https://img.youtube.com/vi/G7heyYn1CBk/0.jpg)](https://www.youtube.com/watch?v=G7heyYn1CBk)

[![Video Demo](https://img.shields.io/badge/YouTube-FF0000?style=for-the-badge&logo=youtube)](https://www.youtube.com/watch?v=G7heyYn1CBk)

## Stack

- PHP con PDO y sesiones nativas.
- MySQL/MariaDB como base de datos.
- Composer para autoload y dependencias.
- Servidor web compatible con PHP: Apache, Nginx, Caddy, cPanel, XAMPP, Laragon o similar.
- JavaScript vanilla con `fetch` para endpoints internos.
- ApexCharts para gráficos y dashboards.
- Tailwind/template Xintra para UI.
- Cloudflare Turnstile para protección del login.

## Requisitos

- PHP 8.0+ recomendado.
- PHP runtime accesible desde CLI y servidor web compatible.
- MySQL/MariaDB 5.7+.
- Composer.
- Extensiones PHP: `curl`, `pdo_mysql`, `openssl`, `mbstring`, `json`.
- Navegador moderno.

## Instalación

```bash
git clone <repo>
cd xintra-elephpant
composer install
```

Crea tu archivo `.env` en la raíz del proyecto usando las variables necesarias para tu entorno.

```env
DB_HOST=127.0.0.1
DB_NAME=bd_black
DB_USER=root
DB_PASS=

IP_API_URL=https://api.ipify.org

TURNSTILE_SITE_KEY=your-site-key
TURNSTILE_SECRET_KEY=your-secret-key
TURNSTILE_HOSTNAME=127.0.0.1
```

En producción, `TURNSTILE_HOSTNAME` debe coincidir con el dominio configurado en Cloudflare Turnstile, por ejemplo `tudominio.com`.

## Ejecución Local

Sirve el proyecto desde cualquier entorno compatible con PHP. Puede ser un virtual host, un document root local, un contenedor, cPanel, XAMPP, Laragon u otra configuración equivalente.

Ejemplo de acceso local:

```text
http://127.0.0.1/xintra-elephpant/index.php
```

Si trabajas en Windows y PHP/cURL no valida certificados HTTPS, el proyecto incluye soporte para `config/cacert.pem`. Este archivo es público, no contiene secretos y puede versionarse en Git. Permite validar servicios externos como Cloudflare Turnstile sin desactivar la verificación SSL.

## Estructura

```text
xintra-elephpant/
├── assets/              # CSS, JS, imágenes, librerías frontend
├── config/              # bootstrap, API config, certificados auxiliares
├── controller/          # endpoints PHP consumidos por fetch/ajax
├── database/            # conexión y recursos de base de datos
├── layout/              # parciales reutilizables: header, sidebar, footer
├── model/               # acceso a datos y lógica de dominio
├── views/               # vistas protegidas de la aplicación
├── vendor/              # dependencias instaladas por Composer
├── index.php            # login y entrada pública principal
└── README.md
```

## Convenciones del Proyecto

- `config/bootstrap.php` debe cargarse al inicio de cada vista/controlador que requiera configuración global.
- `ROOT` debe usarse para includes internos: `include ROOT . '/layout/header.php';`.
- Las vistas protegidas deben validar sesión con `controller/check_session.php`.
- Los controladores deben responder JSON cuando sean consumidos por JavaScript.
- No mezclar consultas SQL complejas dentro de vistas; usar modelos en `model/`.
- Los parciales visuales compartidos pertenecen a `layout/`.

Ejemplo base para una vista protegida:

```php
<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_once ROOT . '/controller/check_session.php';
?>
```

Ejemplo base para un controlador JSON:

```php
<?php
require_once __DIR__ . '/../config/bootstrap.php';
header('Content-Type: application/json');

echo json_encode(['ok' => true]);
```

## Seguridad

- El login está protegido con Cloudflare Turnstile.
- La validación de Turnstile ocurre en backend mediante `TURNSTILE_SECRET_KEY`.
- `TURNSTILE_HOSTNAME` permite limitar tokens al hostname esperado.
- Las sesiones se inicializan centralmente desde `config/bootstrap.php`.
- No subir `.env` con credenciales reales a repositorios públicos.
- Mantener `vendor/` actualizado con `composer install` o `composer update` según el flujo del equipo.

## Dashboards & Reportes

- `assets/js/sales-dashboard.js` alimenta el dashboard principal.
- `assets/js/analytics-reporte.js` alimenta reportes por usuario, tickets e items.
- Los endpoints de datos viven principalmente en `controller/dashboard/` y `controller/venta/`.
- Los gráficos usan ApexCharts y los tooltips auxiliares se centralizan en `assets/js/xintra-tooltip.js`.

## Verificación

Validar sintaxis PHP:

```bash
php -l index.php
php -l controller/acceso.php
php -l views/home.php
```

Validar sintaxis JavaScript:

```bash
node --check assets/js/loginscript.js
node --check assets/js/sales-dashboard.js
node --check assets/js/analytics-reporte.js
```

Revisar extensiones PHP disponibles:

```bash
php -m
```

## Despliegue

- Configura el `.env` del hosting con credenciales reales.
- Registra el dominio de producción en Cloudflare Turnstile.
- Ajusta `TURNSTILE_HOSTNAME` al hostname real.
- Verifica que PHP tenga habilitado `curl` y `openssl`.
- Confirma que el servidor web sirva correctamente assets estáticos desde `assets/`.
- Revisa permisos de lectura para `config/`, `controller/`, `model/`, `layout/` y `views/`.

## Troubleshooting

- Si el login muestra error de verificación de seguridad, revisa `TURNSTILE_SECRET_KEY`, `TURNSTILE_HOSTNAME` y conectividad HTTPS desde PHP.
- Si Turnstile carga pero backend falla en local, valida el certificado CA usado por cURL.
- Si un `fetch` devuelve HTML en lugar de JSON, probablemente la sesión expiró o hubo una redirección al login.
- Si un gráfico no carga, revisar la respuesta JSON del endpoint en DevTools antes de tocar ApexCharts.

## Licencia & Auditoría

Este proyecto es open source. Puedes estudiar, modificar y adaptar el código respetando los créditos originales del autor y la marca del proyecto.

Créditos principales:

- PHPEITOR como autor/marca técnica del proyecto.
- Xintra Elephpant como implementación web para gestión operativa, dashboards y reportes.

Antes de usarlo en producción, se recomienda auditar:

- Variables sensibles del `.env`.
- Permisos de base de datos.
- Reglas del servidor web.
- Flujo de autenticación y sesiones.
- Endpoints JSON expuestos bajo `controller/`.
- Configuración de Cloudflare Turnstile para el dominio final.

Consulta `.licence` para la nota de atribución del proyecto.
