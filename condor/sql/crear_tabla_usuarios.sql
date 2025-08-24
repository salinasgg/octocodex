/* Tabla para cualquier sistema, ideal para redes sociales, login y registro de usuarios */
-- ===== CREAR TABLA USUARIOS =====
-- Eliminar la tabla si existe
DROP TABLE IF EXISTS usuarios;

-- Crear la tabla
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    us_nombre VARCHAR(100) NOT NULL,
    us_apellido VARCHAR(100) NOT NULL,
    us_username VARCHAR(50) NOT NULL UNIQUE,
    us_password VARCHAR(255) NOT NULL,
    us_email VARCHAR(100) NOT NULL UNIQUE,
    us_bio TEXT,
    us_foto_perfil VARCHAR(255),
    us_fecha_nacimiento DATE,
    us_rol ENUM('administrador', 'usuario') NOT NULL,
    us_fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    us_fecha_ultimo_acceso TIMESTAMP,
    us_fecha_suspension TIMESTAMP,
    us_ultimo_ip VARCHAR(45),
    us_url_perfil VARCHAR(255) UNIQUE,
    us_visibilidad_perfil ENUM('publico', 'privado', 'solo_seguidores') NOT NULL DEFAULT 'publico',
    us_seguidores_count INT DEFAULT 0,
    us_publicaciones_count INT DEFAULT 0,
    us_activo BOOLEAN NOT NULL DEFAULT TRUE
);

-- ===== INSERTAR USUARIO ADMINISTRADOR DE PRUEBA =====
INSERT INTO usuarios (
    us_nombre,
    us_apellido, 
    us_username,
    us_password,
    us_email,
    us_rol,
    us_url_perfil,
    us_activo
) VALUES (
    'Gabriel',
    'Salinas',
    'admin',
    -- Nota: En producción, la contraseña debe estar hasheada (ej: usando PASSWORD() o bcrypt)
    'caca2025', -- El hash debe hacerse desde PHP usando password_hash('caca2025', PASSWORD_DEFAULT)
    'admin@sistema.com',
    'administrador',
    'admin', -- URL del perfil, generalmente basada en el username
    TRUE
);

-- ===== INFORMACIÓN SOBRE CAMPOS ESPECIALES =====
/*
CAMPO us_ultimo_ip:
- Se llena automáticamente cuando el usuario inicia sesión
- Se obtiene desde el servidor web usando $_SERVER['REMOTE_ADDR'] en PHP
- Puede ser IPv4 (ej: 192.168.1.100) o IPv6 (ej: 2001:db8::1)
- Se actualiza en cada login exitoso

CAMPO us_url_perfil:
- Se genera automáticamente basado en el username
- Formato típico: username en minúsculas y caracteres válidos para URL
- Debe ser único para evitar conflictos de rutas
- Se usa para crear enlaces como: /perfil/admin o /usuario/admin
- En el frontend se construye como: /perfil/{us_url_perfil}

EJEMPLO DE USO EN PHP:
$ultimo_ip = $_SERVER['REMOTE_ADDR'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? 'unknown';
$url_perfil = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $username));
*/
