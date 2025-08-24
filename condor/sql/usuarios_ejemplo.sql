-- ===== USUARIOS DE EJEMPLO ADICIONALES =====
-- Este archivo contiene usuarios de ejemplo con datos más completos
-- para demostrar todas las funcionalidades de la nueva estructura

USE `u802689289_octocodex_db`;

-- ===== USUARIO DESARROLLADOR =====
INSERT INTO `usuarios` (
    `us_nombre`,
    `us_apellido`,
    `us_username`, 
    `us_password`, 
    `us_email`, 
    `us_rol`, 
    `us_activo`,
    `us_bio`,
    `us_foto_perfil`,
    `us_fecha_nacimiento`,
    `us_url_perfil`,
    `us_visibilidad_perfil`,
    `us_seguidores_count`,
    `us_publicaciones_count`
) VALUES (
    'María',
    'González',
    'maria_dev',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password123
    'maria.gonzalez@condor.com',
    'usuario',
    1,
    'Desarrolladora Full Stack apasionada por crear experiencias digitales increíbles. Especializada en PHP, JavaScript y React.',
    'https://via.placeholder.com/150/8b5cf6/ffffff?text=MG',
    '1992-05-15',
    'maria-gonzalez',
    'publico',
    45,
    12
) ON DUPLICATE KEY UPDATE 
    `us_bio` = VALUES(`us_bio`),
    `us_foto_perfil` = VALUES(`us_foto_perfil`);

-- ===== USUARIO DISEÑADOR =====
INSERT INTO `usuarios` (
    `us_nombre`,
    `us_apellido`,
    `us_username`, 
    `us_password`, 
    `us_email`, 
    `us_rol`, 
    `us_activo`,
    `us_bio`,
    `us_foto_perfil`,
    `us_fecha_nacimiento`,
    `us_url_perfil`,
    `us_visibilidad_perfil`,
    `us_seguidores_count`,
    `us_publicaciones_count`
) VALUES (
    'Carlos',
    'Rodríguez',
    'carlos_design',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password123
    'carlos.rodriguez@condor.com',
    'usuario',
    1,
    'Diseñador UX/UI creativo. Me encanta crear interfaces intuitivas y experiencias de usuario memorables.',
    'https://via.placeholder.com/150/7c3aed/ffffff?text=CR',
    '1988-12-03',
    'carlos-rodriguez',
    'publico',
    78,
    23
) ON DUPLICATE KEY UPDATE 
    `us_bio` = VALUES(`us_bio`),
    `us_foto_perfil` = VALUES(`us_foto_perfil`);

-- ===== USUARIO MARKETING =====
INSERT INTO `usuarios` (
    `us_nombre`,
    `us_apellido`,
    `us_username`, 
    `us_password`, 
    `us_email`, 
    `us_rol`, 
    `us_activo`,
    `us_bio`,
    `us_foto_perfil`,
    `us_fecha_nacimiento`,
    `us_url_perfil`,
    `us_visibilidad_perfil`,
    `us_seguidores_count`,
    `us_publicaciones_count`
) VALUES (
    'Ana',
    'Martínez',
    'ana_marketing',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password123
    'ana.martinez@condor.com',
    'usuario',
    1,
    'Especialista en Marketing Digital. Estrategias de crecimiento y análisis de datos para maximizar el ROI.',
    'https://via.placeholder.com/150/6d28d9/ffffff?text=AM',
    '1990-08-22',
    'ana-martinez',
    'solo_seguidores',
    156,
    67
) ON DUPLICATE KEY UPDATE 
    `us_bio` = VALUES(`us_bio`),
    `us_foto_perfil` = VALUES(`us_foto_perfil`);

-- ===== USUARIO PRIVADO =====
INSERT INTO `usuarios` (
    `us_nombre`,
    `us_apellido`,
    `us_username`, 
    `us_password`, 
    `us_email`, 
    `us_rol`, 
    `us_activo`,
    `us_bio`,
    `us_foto_perfil`,
    `us_fecha_nacimiento`,
    `us_url_perfil`,
    `us_visibilidad_perfil`,
    `us_seguidores_count`,
    `us_publicaciones_count`
) VALUES (
    'Luis',
    'Fernández',
    'luis_private',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password123
    'luis.fernandez@condor.com',
    'usuario',
    1,
    'Perfil privado - Solo contenido para amigos cercanos.',
    'https://via.placeholder.com/150/5b21b6/ffffff?text=LF',
    '1995-03-10',
    'luis-fernandez',
    'privado',
    12,
    5
) ON DUPLICATE KEY UPDATE 
    `us_bio` = VALUES(`us_bio`),
    `us_foto_perfil` = VALUES(`us_foto_perfil`);

-- ===== USUARIO SUSPENDIDO =====
INSERT INTO `usuarios` (
    `us_nombre`,
    `us_apellido`,
    `us_username`, 
    `us_password`, 
    `us_email`, 
    `us_rol`, 
    `us_activo`,
    `us_bio`,
    `us_foto_perfil`,
    `us_fecha_nacimiento`,
    `us_url_perfil`,
    `us_visibilidad_perfil`,
    `us_seguidores_count`,
    `us_publicaciones_count`,
    `us_fecha_suspension`
) VALUES (
    'Roberto',
    'Sánchez',
    'roberto_suspended',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password123
    'roberto.sanchez@condor.com',
    'usuario',
    0,
    'Usuario suspendido temporalmente por violación de términos de servicio.',
    'https://via.placeholder.com/150/4c1d95/ffffff?text=RS',
    '1987-11-18',
    'roberto-sanchez',
    'privado',
    0,
    0,
    NOW()
) ON DUPLICATE KEY UPDATE 
    `us_activo` = VALUES(`us_activo`),
    `us_fecha_suspension` = VALUES(`us_fecha_suspension`);

-- ===== CONSULTAS DE VERIFICACIÓN =====

-- Mostrar todos los usuarios con información completa
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
    `us_url_perfil`,
    `us_visibilidad_perfil`,
    `us_seguidores_count`,
    `us_publicaciones_count`,
    `us_fecha_suspension`
FROM `usuarios` 
ORDER BY `id`;

-- Estadísticas de usuarios por visibilidad
SELECT 
    `us_visibilidad_perfil`,
    COUNT(*) as total_usuarios,
    AVG(`us_seguidores_count`) as promedio_seguidores,
    AVG(`us_publicaciones_count`) as promedio_publicaciones
FROM `usuarios` 
WHERE `us_activo` = 1
GROUP BY `us_visibilidad_perfil`;

-- Usuarios más populares (por seguidores)
SELECT 
    `us_username`,
    CONCAT(`us_nombre`, ' ', `us_apellido`) as nombre_completo,
    `us_seguidores_count`,
    `us_publicaciones_count`,
    `us_bio`
FROM `usuarios` 
WHERE `us_activo` = 1
ORDER BY `us_seguidores_count` DESC
LIMIT 5;

-- Usuarios suspendidos
SELECT 
    `us_username`,
    CONCAT(`us_nombre`, ' ', `us_apellido`) as nombre_completo,
    `us_fecha_suspension`,
    `us_email`
FROM `usuarios` 
WHERE `us_activo` = 0 AND `us_fecha_suspension` IS NOT NULL;
