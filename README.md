## Xintra Elephpant 🐘

Sistema de gestión de tickets, clientes, usuarios e inventario.

[![forthebadge](http://forthebadge.com/badges/uses-css.svg)](https://www.linkedin.com/in/drphp/)
[![forthebadge](http://forthebadge.com/badges/built-with-love.svg)](https://www.linkedin.com/in/drphp/)

[![Video](https://img.youtube.com/vi/G7heyYn1CBk/0.jpg)](https://www.youtube.com/watch?v=G7heyYn1CBk)

[![Video Demo](https://img.shields.io/badge/YouTube-FF0000?style=for-the-badge&logo=youtube)](https://www.youtube.com/watch?v=G7heyYn1CBk)


## 📋 Resumen

Aplicación web para gestión de ventas, clientes, usuarios e inventario con dashboards y exportación. Esta rama del proyecto usa un patrón de entrada única: solo `index.php` queda en la raíz; todas las vistas están en `views/`.

## 🛠️ Requisitos mínimos

- PHP 7.4+
- Apache 2.4+ con `mod_rewrite` habilitado
- MySQL/MariaDB 5.7+
- Composer
- Navegador moderno (Chrome, Firefox, Edge)

## Instalación rápida

```bash
git clone <repo>
cd xintra-elephpant
composer install
```

Configura la conexión en `config/api.php` o usando `.env` si lo prefieres.

## Nota importante: nueva estructura y patrón de entrada

En esta versión hemos adoptado el patrón "single-entry" similar al proyecto Cotix:

- Solo `index.php` debe permanecer en la raíz del proyecto.
- Todas las páginas están en `views/` (por ejemplo `views/home.php`, `views/usuarios.php`).
- La inicialización central está en `config/bootstrap.php` que define la constante `ROOT` y se encarga de iniciar la sesión cuando hace falta.

Ejemplo de uso en una vista (arriba del archivo):

```php
require_once __DIR__ . '/../config/bootstrap.php';
require_once ROOT . '/controller/check_session.php';
```

No crear archivos `*.php` de páginas en la raíz (excepto `index.php`).

## Estructura del proyecto (actualizada)

```
xintra-elephpant/
├── assets/           # css, js, imágenes, libs
├── config/
│   ├── api.php       # configuración DB
│   └── bootstrap.php # define ROOT, autoload, inicia sesión
├── controller/       # endpoints (controller/*.php)
├── database/         # scripts y conexión
├── model/            # modelos y lógica de datos
├── views/            # todas las vistas (.php)
├── vendor/           # dependencias Composer
├── index.php         # único entrypoint público
└── README.md
```

## .htaccess y despliegue

- El proyecto usa reglas de `mod_rewrite` para enrutar correctamente. Si actualizas `.htaccess`, reinicia o recarga Apache.
- Asegúrate de que `AllowOverride` esté habilitado para el directorio del proyecto.

Ejemplo mínimo (ya incluido en el repo):

```
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [L,QSA]
```

## Bootstrap y manejo de sesiones

- `config/bootstrap.php` define la constante `ROOT` (ruta absoluta del proyecto) y llama a `session_start()` solo si no hay una sesión activa.
- Evita llamar a `session_start()` en otros archivos; usa `require_once ROOT . '/controller/check_session.php'` para validar sesiones en las vistas.

## Desarrollo: añadir vistas y controladores

- Para añadir una página:
	- Crea `views/nueva_vista.php` e incluye `config/bootstrap.php` al inicio.
	- Implementa la UI y los formularios que hagan `fetch()` a los controladores.

- Para añadir un endpoint (controlador):
	- Crea `controller/add_algo.php` o `controller/get_algo.php`.
	- Los controladores deben devolver JSON para las peticiones `fetch()` del frontend. Ejemplo:

```php
header('Content-Type: application/json');
echo json_encode(['ok' => true, 'msg' => 'Creado']);
```

## JavaScript y endpoints

- Los scripts en `assets/js/` usan `fetch` y esperan respuestas JSON de `controller/*.php`.
- Si un `fetch` recibe HTML (por ejemplo una página de login), suele ser porque la sesión expiró y `check_session` redirigió; valida sesión antes de las operaciones críticas.

## Pruebas y verificación rápida

- Sintaxis PHP:

```bash
php -l <archivo.php>
```

- Verifica JS (básico):

```bash
node --check assets/js/archivo.js
```

## Notas de despliegue

- Tras cambios en `.htaccess` o configuración de Apache, recarga el servicio:

```powershell
Restart-Service -Name 'apache2.4' # o el servicio que uses
```

- Asegura permisos adecuados en `assets/` y `database/` si el servidor escribe en disco.

## Contacto y soporte

Si encuentras problemas, crea un issue en el repositorio con pasos para reproducir y capturas de errores.