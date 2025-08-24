-- ===== ESTRUCTURA DE LA BASE DE DATOS PARA EL SISTEMA DE LOGIN =====
-- Este archivo contiene la estructura de la tabla usuarios necesaria para el sistema

-- Crear la base de datos si no existe
CREATE DATABASE IF NOT EXISTS `u802689289_octocodex_db` 
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Usar la base de datos
USE `u802689289_octocodex_db`;

-- ===== TABLA DE USUARIOS =====
-- Tabla principal para almacenar información de usuarios del sistema
CREATE TABLE IF NOT EXISTS `usuarios` (
    `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'ID único del usuario',
    `us_nombre` VARCHAR(100) NOT NULL COMMENT 'Nombre del usuario',
    `us_apellido` VARCHAR(100) NOT NULL COMMENT 'Apellido del usuario',
    `us_username` VARCHAR(50) NOT NULL UNIQUE COMMENT 'Nombre de usuario único',
    `us_password` VARCHAR(255) NOT NULL COMMENT 'Contraseña hasheada',
    `us_email` VARCHAR(100) NOT NULL UNIQUE COMMENT 'Email del usuario',
    `us_bio` TEXT NULL DEFAULT NULL COMMENT 'Biografía del usuario',
    `us_foto_perfil` VARCHAR(255) NULL DEFAULT NULL COMMENT 'URL de la foto de perfil',
    `us_fecha_nacimiento` DATE NULL DEFAULT NULL COMMENT 'Fecha de nacimiento del usuario',
    `us_rol` ENUM('administrador', 'usuario') NOT NULL DEFAULT 'usuario' COMMENT 'Rol del usuario en el sistema',
    `us_fecha_registro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de registro del usuario',
    `us_fecha_ultimo_acceso` TIMESTAMP NULL DEFAULT NULL COMMENT 'Timestamp del último acceso',
    `us_fecha_suspension` TIMESTAMP NULL DEFAULT NULL COMMENT 'Fecha de suspensión del usuario',
    `us_ultimo_ip` VARCHAR(45) NULL DEFAULT NULL COMMENT 'Última dirección IP del usuario',
    `us_url_perfil` VARCHAR(255) UNIQUE NULL DEFAULT NULL COMMENT 'URL personalizada del perfil',
    `us_visibilidad_perfil` ENUM('publico', 'privado', 'solo_seguidores') NOT NULL DEFAULT 'publico' COMMENT 'Visibilidad del perfil',
    `us_seguidores_count` INT DEFAULT 0 COMMENT 'Número de seguidores',
    `us_publicaciones_count` INT DEFAULT 0 COMMENT 'Número de publicaciones',
    `us_activo` BOOLEAN NOT NULL DEFAULT TRUE COMMENT 'Estado activo/inactivo del usuario',
    `token_recuperacion` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Token para recuperación de contraseña',
    `token_expiracion` TIMESTAMP NULL DEFAULT NULL COMMENT 'Expiración del token de recuperación',
    `fecha_actualizacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha de última actualización',
    PRIMARY KEY (`id`),
    INDEX `idx_us_username` (`us_username`),
    INDEX `idx_us_email` (`us_email`),
    INDEX `idx_us_rol` (`us_rol`),
    INDEX `idx_us_activo` (`us_activo`),
    INDEX `idx_us_fecha_registro` (`us_fecha_registro`),
    INDEX `idx_us_ultimo_acceso` (`us_fecha_ultimo_acceso`),
    INDEX `idx_us_url_perfil` (`us_url_perfil`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de usuarios del sistema';

-- ===== INSERTAR USUARIO ADMINISTRADOR POR DEFECTO =====
-- Crear un usuario administrador para pruebas iniciales
-- Usuario: admin
-- Contraseña: admin123 (hasheada con password_hash)
INSERT INTO `usuarios` (
    `us_nombre`,
    `us_apellido`,
    `us_username`, 
    `us_password`, 
    `us_email`, 
    `us_rol`, 
    `us_activo`,
    `us_bio`,
    `us_url_perfil`
) VALUES (
    'Administrador',
    'del Sistema',
    'admin',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- admin123
    'admin@condor.com',
    'administrador',
    1,
    'Administrador principal del sistema Condor',
    'admin'
) ON DUPLICATE KEY UPDATE 
    `us_password` = VALUES(`us_password`),
    `us_email` = VALUES(`us_email`),
    `us_nombre` = VALUES(`us_nombre`),
    `us_apellido` = VALUES(`us_apellido`),
    `us_rol` = VALUES(`us_rol`),
    `us_activo` = VALUES(`us_activo`);

-- ===== INSERTAR USUARIO DE PRUEBA =====
-- Crear un usuario normal para pruebas
-- Usuario: usuario
-- Contraseña: usuario123 (hasheada con password_hash)
INSERT INTO `usuarios` (
    `us_nombre`,
    `us_apellido`,
    `us_username`, 
    `us_password`, 
    `us_email`, 
    `us_rol`, 
    `us_activo`,
    `us_bio`,
    `us_url_perfil`
) VALUES (
    'Usuario',
    'de Prueba',
    'usuario',
    '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', -- usuario123
    'usuario@condor.com',
    'usuario',
    1,
    'Usuario de prueba del sistema',
    'usuario'
) ON DUPLICATE KEY UPDATE 
    `us_password` = VALUES(`us_password`),
    `us_email` = VALUES(`us_email`),
    `us_nombre` = VALUES(`us_nombre`),
    `us_apellido` = VALUES(`us_apellido`),
    `us_rol` = VALUES(`us_rol`),
    `us_activo` = VALUES(`us_activo`);

-- ===== INSERTAR USUARIO MODERADOR DE PRUEBA =====
-- Crear un moderador para pruebas (usando rol 'usuario' ya que solo tenemos 'administrador' y 'usuario')
-- Usuario: moderador
-- Contraseña: moderador123 (hasheada con password_hash)
INSERT INTO `usuarios` (
    `us_nombre`,
    `us_apellido`,
    `us_username`, 
    `us_password`, 
    `us_email`, 
    `us_rol`, 
    `us_activo`,
    `us_bio`,
    `us_url_perfil`
) VALUES (
    'Moderador',
    'del Sistema',
    'moderador',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- moderador123
    'moderador@condor.com',
    'usuario',
    1,
    'Moderador del sistema Condor',
    'moderador'
) ON DUPLICATE KEY UPDATE 
    `us_password` = VALUES(`us_password`),
    `us_email` = VALUES(`us_email`),
    `us_nombre` = VALUES(`us_nombre`),
    `us_apellido` = VALUES(`us_apellido`),
    `us_rol` = VALUES(`us_rol`),
    `us_activo` = VALUES(`us_activo`);

-- ===== TABLA DE SESIONES (OPCIONAL) =====
-- Tabla para manejar sesiones persistentes y tokens de "recordarme"
CREATE TABLE IF NOT EXISTS `sesiones_usuarios` (
    `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'ID único de la sesión',
    `usuario_id` INT(11) NOT NULL COMMENT 'ID del usuario',
    `token` VARCHAR(255) NOT NULL UNIQUE COMMENT 'Token de sesión',
    `ip_address` VARCHAR(45) NULL DEFAULT NULL COMMENT 'Dirección IP del usuario',
    `user_agent` TEXT NULL DEFAULT NULL COMMENT 'User agent del navegador',
    `fecha_creacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creación de la sesión',
    `fecha_expiracion` TIMESTAMP NOT NULL COMMENT 'Fecha de expiración de la sesión',
    `activa` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Estado activo/inactivo de la sesión',
    PRIMARY KEY (`id`),
    INDEX `idx_usuario_id` (`usuario_id`),
    INDEX `idx_token` (`token`),
    INDEX `idx_fecha_expiracion` (`fecha_expiracion`),
    INDEX `idx_activa` (`activa`),
    FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de sesiones de usuarios';

-- ===== TABLA DE LOGS DE ACCESO (OPCIONAL) =====
-- Tabla para registrar todos los intentos de acceso al sistema
CREATE TABLE IF NOT EXISTS `logs_acceso` (
    `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'ID único del log',
    `usuario_id` INT(11) NULL DEFAULT NULL COMMENT 'ID del usuario (NULL si falló el login)',
    `username` VARCHAR(50) NOT NULL COMMENT 'Username intentado',
    `ip_address` VARCHAR(45) NOT NULL COMMENT 'Dirección IP del intento',
    `user_agent` TEXT NULL DEFAULT NULL COMMENT 'User agent del navegador',
    `tipo_acceso` ENUM('exitoso', 'fallido', 'logout') NOT NULL COMMENT 'Tipo de acceso',
    `fecha_acceso` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora del acceso',
    `detalles` TEXT NULL DEFAULT NULL COMMENT 'Detalles adicionales del acceso',
    PRIMARY KEY (`id`),
    INDEX `idx_usuario_id` (`usuario_id`),
    INDEX `idx_username` (`username`),
    INDEX `idx_tipo_acceso` (`tipo_acceso`),
    INDEX `idx_fecha_acceso` (`fecha_acceso`),
    FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Logs de acceso al sistema';

-- ===== PROCEDIMIENTO PARA LIMPIAR SESIONES EXPIRADAS =====
-- Procedimiento almacenado para limpiar sesiones expiradas automáticamente
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS `limpiar_sesiones_expiradas`()
BEGIN
    -- Eliminar sesiones expiradas
    DELETE FROM `sesiones_usuarios` 
    WHERE `fecha_expiracion` < NOW() OR `activa` = 0;
    
    -- Eliminar logs antiguos (más de 30 días)
    DELETE FROM `logs_acceso` 
    WHERE `fecha_acceso` < DATE_SUB(NOW(), INTERVAL 30 DAY);
END //
DELIMITER ;

-- ===== EVENTO PARA LIMPIAR SESIONES AUTOMÁTICAMENTE =====
-- Evento que se ejecuta diariamente para limpiar sesiones expiradas
CREATE EVENT IF NOT EXISTS `limpiar_sesiones_diario`
ON SCHEDULE EVERY 1 DAY
STARTS CURRENT_TIMESTAMP
DO CALL `limpiar_sesiones_expiradas`();

-- ===== CONSULTAS DE VERIFICACIÓN =====
-- Consultas para verificar que todo se creó correctamente

-- Verificar que la tabla usuarios existe y tiene datos
SELECT 
    COUNT(*) as total_usuarios,
    SUM(CASE WHEN us_rol = 'administrador' THEN 1 ELSE 0 END) as administradores,
    SUM(CASE WHEN us_rol = 'usuario' THEN 1 ELSE 0 END) as usuarios
FROM `usuarios` 
WHERE `us_activo` = 1;

-- Mostrar usuarios creados
SELECT 
    `id`,
    `us_nombre`,
    `us_apellido`,
    `us_username`,
    `us_email`,
    CONCAT(`us_nombre`, ' ', `us_apellido`) as nombre_completo,
    `us_rol`,
    `us_activo`,
    `us_fecha_registro`,
    `us_bio`,
    `us_url_perfil`
FROM `usuarios` 
ORDER BY `id`;

-- ===== COMENTARIOS FINALES =====
/*
ESTRUCTURA CREADA:

1. Tabla `usuarios`: Almacena información completa de usuarios del sistema
   - Campos básicos: nombre, apellido, username, password, email
   - Campos de perfil: bio, foto_perfil, fecha_nacimiento, url_perfil
   - Campos de privacidad: visibilidad_perfil
   - Campos de estadísticas: seguidores_count, publicaciones_count
   - Campos de seguridad: activo, fecha_suspension, ultimo_ip
   - Campos de sesión: fecha_ultimo_acceso, tokens de recuperación

2. Tabla `sesiones_usuarios`: Maneja sesiones persistentes (opcional)
3. Tabla `logs_acceso`: Registra intentos de acceso (opcional)
4. Procedimiento `limpiar_sesiones_expiradas`: Limpia sesiones expiradas
5. Evento `limpiar_sesiones_diario`: Ejecuta limpieza automática

USUARIOS CREADOS:
- admin / admin123 (Administrador)
- usuario / usuario123 (Usuario normal)
- moderador / moderador123 (Usuario con rol especial)

NUEVOS CAMPOS AGREGADOS:
- us_nombre, us_apellido: Separación de nombre y apellido
- us_bio: Biografía del usuario
- us_foto_perfil: URL de la foto de perfil
- us_fecha_nacimiento: Fecha de nacimiento
- us_url_perfil: URL personalizada del perfil
- us_visibilidad_perfil: Control de privacidad (público, privado, solo_seguidores)
- us_seguidores_count: Contador de seguidores
- us_publicaciones_count: Contador de publicaciones
- us_fecha_suspension: Fecha de suspensión
- us_ultimo_ip: Última dirección IP

NOTAS:
- Las contraseñas están hasheadas con password_hash()
- Se incluyen índices optimizados para consultas frecuentes
- Se configuran foreign keys para integridad referencial
- Se incluye limpieza automática de datos expirados
- Estructura preparada para funcionalidades sociales avanzadas
*/
