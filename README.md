# XINTRA Elephpant 🐘🎟️

Sistema de gestión de tickets, clientes, usuarios e inventario con interfaz interactiva y animada.

[![forthebadge](http://forthebadge.com/badges/uses-css.svg)](https://www.linkedin.com/in/drphp/)
[![forthebadge](http://forthebadge.com/badges/built-with-love.svg)](https://www.linkedin.com/in/drphp/)

## 📋 Descripción

**XINTRA Elephpant** es una aplicación web completa para la gestión integral de:
- 🎫 **Tickets/Venta**: Control de ventas con detalles de cliente, items y fechas
- 👥 **Usuarios**: Gestión de usuarios del sistema con roles y permisos
- 🛒 **Clientes**: Base de datos de clientes con información de contacto
- 📦 **Inventario**: Control de items y stock disponible
- 📊 **Reportes**: Dashboards y análisis de datos con gráficos (ApexCharts)

## ✨ Características

- ✅ **Autenticación segura** con validación de intentos fallidos
- ✅ **Sistema de bloqueo**: 3 intentos fallidos = bloqueo de 5 minutos
- ✅ **Interfaz interactiva**: Avatar animado que reacciona a las interacciones
- ✅ **Validación en tiempo real**: Bordes rojos en campos vacíos
- ✅ **Timer de sesión**: Redirección automática después de login exitoso
- ✅ **Gestión CRUD completa**: Crear, leer, actualizar y eliminar registros
- ✅ **Tablas dinámicas**: DataTables con búsqueda y paginación
- ✅ **Exportación de datos**: Excel y PDF
- ✅ **Dashboards visuales**: Gráficos interactivos con ApexCharts
- ✅ **Diseño responsivo**: Compatible con dispositivos móviles

## 🛠️ Requisitos Previos

- **PHP 7.4+**
- **Apache 2.4+** (con mod_rewrite habilitado)
- **MySQL/MariaDB 5.7+**
- **Composer** (para gestión de dependencias)
- **Navegador moderno** (Chrome, Firefox, Safari, Edge)

## 📦 Instalación

### 1. Clonar repositorio
```bash
git clone https://github.com/phpeitor/xintra-elephpant.git
cd xintra-elephpant
```

### 2. Instalar dependencias
```bash
composer install
```

### 3. Configurar conexión a base de datos
Edita `config/api.php` con tus credenciales:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'tu_usuario');
define('DB_PASS', 'tu_contraseña');
define('DB_NAME', 'xintra_db');
```

### 4. Crear base de datos
Importa el archivo de migración o ejecuta los scripts SQL en `database/`

### 5. Verificar permisos
Asegúrate que Apache tenga permisos de lectura/escritura en carpetas necesarias

### 6. Iniciar sesión
- Accede a `http://localhost/xintra-elephpant/`
- Usuario predefinido: `admin` / `password`

## 📂 Estructura del Proyecto

```
xintra-elephpant/
├── assets/
│   ├── css/          # Estilos (loginstyle.css, styles.css)
│   ├── js/           # Scripts (loginscript.js, datatables, gráficos)
│   ├── icon-fonts/   # Fuentes de iconos (Bootstrap, Tabler, etc)
│   ├── images/       # Imágenes y recursos
│   └── libs/         # Librerías externas (ApexCharts, DataTables, etc)
├── config/
│   ├── api.php       # Configuración de base de datos
│   └── bootstrap.php # Inicialización
├── controller/       # Controladores de lógica de negocio
│   ├── acceso.php    # Autenticación
│   ├── add_*.php     # Crear registros
│   ├── upd_*.php     # Actualizar registros
│   ├── delete_*.php  # Eliminar registros
│   ├── get_*.php     # Obtener datos
│   ├── table_*.php   # Tablas dinámicas
│   └── venta/        # Módulo de ventas/tickets
├── database/
│   └── conexion.php  # Conexión a BD
├── model/            # Modelos y clases
│   ├── usuario.php
│   ├── cliente.php
│   ├── item.php
│   ├── ticket.php
│   ├── dashboard.php
│   └── notify.php
├── vendor/           # Dependencias Composer (DOMPDF, etc)
├── index.php         # Página de login
├── home.php          # Panel principal
├── *.php             # Páginas principales (usuarios, clientes, items, tickets)
└── README.md         # Este archivo
```

## 🔐 Seguridad Implementada

### Sistema de Autenticación
- ✅ Validación de sesiones
- ✅ Control de intentos fallidos (máximo 3 intentos)
- ✅ Bloqueo temporal de 5 minutos tras fallos
- ✅ Timer de redirección automática tras login exitoso

### Validaciones Frontend
- ✅ Verificación de campos vacíos con retroalimentación visual (borde rojo)
- ✅ Máxima longitud de caracteres en inputs
- ✅ Validación de datos antes de envíos

### Validaciones Backend
- ✅ Check de sesión activa
- ✅ Validación de entrada de datos
- ✅ Prepared statements para prevenir SQL injection

## 💾 Base de Datos

### Tablas principales
- `usuarios` - Gestión de usuarios del sistema
- `clientes` - Información de clientes
- `items` - Catálogo de productos
- `tickets` - Registro de ventas/transacciones
- `stock_items` - Control de inventario

## 🎯 Uso del Sistema

### Panel de Control
- Acceso al dashboard con resumen de datos
- Gráficos de ventas diarias, mensuales
- Contador de clientes, items, usuarios

### Gestión de Usuarios
```
Usuarios → [Ver tabla] → [Agregar] [Editar] [Eliminar]
```

### Gestión de Clientes
```
Clientes → [Ver tabla] → [Agregar] [Editar] [Eliminar]
```

### Gestión de Inventario
```
Items → [Ver tabla] → [Agregar] [Editar] [Eliminar] [Ver Stock]
```

### Registro de Ventas
```
Tickets → [Ver tabla] → [Nueva venta] [Editar] [Ver PDF] [Exportar Excel]
```

## 📊 Reportes y Exportación

- **Excel**: Exportar tablas en formato `.xlsx`
- **PDF**: Generar recibos y reportes en PDF
- **Dashboards**: Análisis visuales con ApexCharts

## 🔧 Configuración Adicional

### Variables de entorno (opcional)
Crea un archivo `.env` para valores sensibles:
```
DB_HOST=localhost
DB_USER=root
DB_PASS=password
DB_NAME=xintra_db
```

### Tabla de colores principales
- **Primario**: `#217093` (Azul oscuro)
- **Secundario**: `#4eb8dd` (Azul claro)
- **Error**: `#d32f2f` (Rojo)
- **Éxito**: `#4caf50` (Verde)

## 📱 Compatibilidad

| Navegador | Versión Mínima |
|-----------|----------------|
| Chrome    | 90+            |
| Firefox   | 88+            |
| Safari    | 14+            |
| Edge      | 90+            |

## 🚀 Mejoras Futuras

- [ ] Sistema de roles y permisos granular
- [ ] Autenticación con 2FA
- [ ] API REST completa
- [ ] Aplicación móvil nativa
- [ ] Sistema de notificaciones en tiempo real
- [ ] Backup automático de base de datos

## 📝 Licencia

Este proyecto está bajo licencia MIT. Ver archivo LICENSE para más detalles.

## 👨‍💻 Desarrollador

Creado con ❤️ por **PHPeitor**

[![LinkedIn](https://img.shields.io/badge/LinkedIn-0A66C2?style=for-the-badge&logo=linkedin)](https://www.linkedin.com/in/drphp/)
[![Video Demo](https://img.shields.io/badge/YouTube-FF0000?style=for-the-badge&logo=youtube)](https://www.youtube.com/watch?v=G7heyYn1CBk)

## 📞 Soporte

Para problemas o sugerencias, abre un **Issue** en el repositorio.
