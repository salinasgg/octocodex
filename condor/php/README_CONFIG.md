# Configuración de Base de Datos

## 📋 Instrucciones de Configuración

### 1. Configuración Inicial

Para configurar la base de datos en tu entorno local:

1. **Copia el archivo de ejemplo:**
   ```bash
   cp config_bd.example.php config_bd.php
   ```

2. **Modifica las credenciales en `config_bd.php`:**
   ```php
   define('DB_HOST', 'localhost');        // Tu servidor de base de datos
   define('DB_NAME', 'octocodex_db');     // Nombre de tu base de datos
   define('DB_USER', 'root');             // Tu usuario de MySQL
   define('DB_PASS', '');                 // Tu contraseña de MySQL
   ```

### 2. Configuraciones Comunes

#### Para XAMPP (Windows):
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'octocodex_db');
define('DB_USER', 'root');
define('DB_PASS', '');  // Sin contraseña por defecto
```

#### Para MAMP (Mac):
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'octocodex_db');
define('DB_USER', 'root');
define('DB_PASS', 'root');  // Contraseña por defecto de MAMP
```

#### Para servidor de producción:
```php
define('DB_HOST', 'tu-servidor.com');
define('DB_NAME', 'u802689289_octocodex_db');
define('DB_USER', 'u802689289_octocodex');
define('DB_PASS', 'tu-contraseña-segura');
```

### 3. Importar Base de Datos

1. Crea una nueva base de datos en phpMyAdmin
2. Importa el archivo `u802689289_octocodex_db.sql`
3. Ejecuta el INSERT para crear usuarios de prueba

### 4. Usuarios de Prueba

#### Usuario Administrador:
- **Usuario:** `salinasgg`
- **Contraseña:** `caca2025`
- **Rol:** Administrador

#### Usuario Regular:
- **Usuario:** `admin`
- **Contraseña:** `password`
- **Rol:** Administrador

### 5. Seguridad

⚠️ **IMPORTANTE:** 
- El archivo `config_bd.php` está en `.gitignore` por seguridad
- Nunca subas credenciales reales al repositorio
- Usa variables de entorno en producción

### 6. Solución de Problemas

#### Error de conexión:
- Verifica que MySQL esté ejecutándose
- Confirma las credenciales
- Asegúrate de que la base de datos existe

#### Error de permisos:
- Verifica que el usuario tenga permisos en la base de datos
- En XAMPP, el usuario `root` tiene todos los permisos por defecto
