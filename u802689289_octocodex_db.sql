-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 24-08-2025 a las 15:39:21
-- Versión del servidor: 10.11.10-MariaDB-log
-- Versión de PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `u802689289_octocodex_db`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`u802689289_octocodex`@`127.0.0.1` PROCEDURE `limpiar_sesiones_expiradas` ()   BEGIN
    -- Eliminar sesiones expiradas
    DELETE FROM `sesiones_usuarios` 
    WHERE `fecha_expiracion` < NOW() OR `activa` = 0;
    
    -- Eliminar logs antiguos (más de 30 días)
    DELETE FROM `logs_acceso` 
    WHERE `fecha_acceso` < DATE_SUB(NOW(), INTERVAL 30 DAY);
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `logs_acceso`
--

CREATE TABLE `logs_acceso` (
  `id` int(11) NOT NULL COMMENT 'ID único del log',
  `usuario_id` int(11) DEFAULT NULL COMMENT 'ID del usuario (NULL si falló el login)',
  `username` varchar(50) NOT NULL COMMENT 'Username intentado',
  `ip_address` varchar(45) NOT NULL COMMENT 'Dirección IP del intento',
  `user_agent` text DEFAULT NULL COMMENT 'User agent del navegador',
  `tipo_acceso` enum('exitoso','fallido','logout') NOT NULL COMMENT 'Tipo de acceso',
  `fecha_acceso` timestamp NULL DEFAULT current_timestamp() COMMENT 'Fecha y hora del acceso',
  `detalles` text DEFAULT NULL COMMENT 'Detalles adicionales del acceso'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Logs de acceso al sistema';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sesiones_usuarios`
--

CREATE TABLE `sesiones_usuarios` (
  `id` int(11) NOT NULL COMMENT 'ID único de la sesión',
  `usuario_id` int(11) NOT NULL COMMENT 'ID del usuario',
  `token` varchar(255) NOT NULL COMMENT 'Token de sesión',
  `ip_address` varchar(45) DEFAULT NULL COMMENT 'Dirección IP del usuario',
  `user_agent` text DEFAULT NULL COMMENT 'User agent del navegador',
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp() COMMENT 'Fecha de creación de la sesión',
  `fecha_expiracion` timestamp NOT NULL COMMENT 'Fecha de expiración de la sesión',
  `activa` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Estado activo/inactivo de la sesión'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de sesiones de usuarios';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL COMMENT 'ID único del usuario',
  `us_nombre` varchar(100) NOT NULL COMMENT 'Nombre del usuario',
  `us_apellido` varchar(100) NOT NULL COMMENT 'Apellido del usuario',
  `us_username` varchar(50) NOT NULL COMMENT 'Nombre de usuario único',
  `us_password` varchar(255) NOT NULL COMMENT 'Contraseña hasheada',
  `us_email` varchar(100) NOT NULL COMMENT 'Email del usuario',
  `us_bio` text DEFAULT NULL COMMENT 'Biografía del usuario',
  `us_foto_perfil` varchar(255) DEFAULT NULL COMMENT 'URL de la foto de perfil',
  `us_fecha_nacimiento` date DEFAULT NULL COMMENT 'Fecha de nacimiento del usuario',
  `us_rol` enum('administrador','usuario') NOT NULL DEFAULT 'usuario' COMMENT 'Rol del usuario en el sistema',
  `us_fecha_registro` timestamp NULL DEFAULT current_timestamp() COMMENT 'Fecha de registro del usuario',
  `us_fecha_ultimo_acceso` timestamp NULL DEFAULT NULL COMMENT 'Timestamp del último acceso',
  `us_fecha_suspension` timestamp NULL DEFAULT NULL COMMENT 'Fecha de suspensión del usuario',
  `us_ultimo_ip` varchar(45) DEFAULT NULL COMMENT 'Última dirección IP del usuario',
  `us_url_perfil` varchar(255) DEFAULT NULL COMMENT 'URL personalizada del perfil',
  `us_visibilidad_perfil` enum('publico','privado','solo_seguidores') NOT NULL DEFAULT 'publico' COMMENT 'Visibilidad del perfil',
  `us_seguidores_count` int(11) DEFAULT 0 COMMENT 'Número de seguidores',
  `us_publicaciones_count` int(11) DEFAULT 0 COMMENT 'Número de publicaciones',
  `us_activo` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Estado activo/inactivo del usuario',
  `token_recuperacion` varchar(255) DEFAULT NULL COMMENT 'Token para recuperación de contraseña',
  `token_expiracion` timestamp NULL DEFAULT NULL COMMENT 'Expiración del token de recuperación',
  `fecha_actualizacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Fecha de última actualización'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de usuarios del sistema';


--
-- Agregar campo us_puesto a la tabla usuarios
--
ALTER TABLE `usuarios`
  ADD COLUMN `us_puesto` varchar(100) DEFAULT NULL COMMENT 'Puesto o cargo del usuario' AFTER `us_rol`;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `us_nombre`, `us_apellido`, `us_username`, `us_password`, `us_email`, `us_bio`, `us_foto_perfil`, `us_fecha_nacimiento`, `us_rol`, `us_fecha_registro`, `us_fecha_ultimo_acceso`, `us_fecha_suspension`, `us_ultimo_ip`, `us_url_perfil`, `us_visibilidad_perfil`, `us_seguidores_count`, `us_publicaciones_count`, `us_activo`, `token_recuperacion`, `token_expiracion`, `fecha_actualizacion`) VALUES
(1, 'Gabriel', 'Salinas', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@condor.com', 'Administrador principal del sistema Condor', NULL, NULL, 'administrador', '2025-08-20 18:58:55', NULL, NULL, NULL, 'admin', 'publico', 0, 0, 1, NULL, NULL, '2025-08-20 19:00:02'),
(2, 'Usuario', 'de Prueba', 'usuario', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'usuario@condor.com', 'Usuario de prueba del sistema', NULL, NULL, 'usuario', '2025-08-20 18:58:55', NULL, NULL, NULL, 'usuario', 'publico', 0, 0, 1, NULL, NULL, '2025-08-20 18:58:55'),
(3, 'Moderador', 'del Sistema', 'moderador', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'moderador@condor.com', 'Moderador del sistema Condor', NULL, NULL, 'usuario', '2025-08-20 18:58:55', NULL, NULL, NULL, 'moderador', 'publico', 0, 0, 1, NULL, NULL, '2025-08-20 18:58:55');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `logs_acceso`
--
ALTER TABLE `logs_acceso`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuario_id` (`usuario_id`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_tipo_acceso` (`tipo_acceso`),
  ADD KEY `idx_fecha_acceso` (`fecha_acceso`);

--
-- Indices de la tabla `sesiones_usuarios`
--
ALTER TABLE `sesiones_usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `idx_usuario_id` (`usuario_id`),
  ADD KEY `idx_token` (`token`),
  ADD KEY `idx_fecha_expiracion` (`fecha_expiracion`),
  ADD KEY `idx_activa` (`activa`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `us_username` (`us_username`),
  ADD UNIQUE KEY `us_email` (`us_email`),
  ADD UNIQUE KEY `us_url_perfil` (`us_url_perfil`),
  ADD KEY `idx_us_username` (`us_username`),
  ADD KEY `idx_us_email` (`us_email`),
  ADD KEY `idx_us_rol` (`us_rol`),
  ADD KEY `idx_us_activo` (`us_activo`),
  ADD KEY `idx_us_fecha_registro` (`us_fecha_registro`),
  ADD KEY `idx_us_ultimo_acceso` (`us_fecha_ultimo_acceso`),
  ADD KEY `idx_us_url_perfil` (`us_url_perfil`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `logs_acceso`
--
ALTER TABLE `logs_acceso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID único del log';

--
-- AUTO_INCREMENT de la tabla `sesiones_usuarios`
--
ALTER TABLE `sesiones_usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID único de la sesión';

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID único del usuario', AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `logs_acceso`
--
ALTER TABLE `logs_acceso`
  ADD CONSTRAINT `logs_acceso_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `sesiones_usuarios`
--
ALTER TABLE `sesiones_usuarios`
  ADD CONSTRAINT `sesiones_usuarios_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

DELIMITER $$
--
-- Eventos
--
CREATE DEFINER=`u802689289_octocodex`@`127.0.0.1` EVENT `limpiar_sesiones_diario` ON SCHEDULE EVERY 1 DAY STARTS '2025-08-20 18:58:55' ON COMPLETION NOT PRESERVE ENABLE DO CALL `limpiar_sesiones_expiradas`()$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
