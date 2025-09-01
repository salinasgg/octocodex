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
-- Estructura de tabla para la tabla `clientes`
-- 30/08/2025

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL COMMENT 'ID único del cliente',
  `cl_nombre` varchar(100) NOT NULL COMMENT 'Nombre del cliente',
  `cl_apellido` varchar(100) NOT NULL COMMENT 'Apellido del cliente',
  `cl_empresa` varchar(100) DEFAULT NULL COMMENT 'Nombre de la empresa',
  `cl_email` varchar(100) NOT NULL COMMENT 'Email del cliente',
  `cl_telefono` varchar(20) DEFAULT NULL COMMENT 'Teléfono del cliente',
  `cl_direccion` text DEFAULT NULL COMMENT 'Dirección del cliente',
  `cl_ciudad` varchar(100) DEFAULT NULL COMMENT 'Ciudad del cliente',
  `cl_pais` varchar(100) DEFAULT NULL COMMENT 'País del cliente',
  `cl_tipo` enum('potencial','actual') NOT NULL DEFAULT 'potencial' COMMENT 'Tipo de cliente',
  `cl_fecha_registro` timestamp NULL DEFAULT current_timestamp() COMMENT 'Fecha de registro del cliente',
  `cl_notas` text DEFAULT NULL COMMENT 'Notas adicionales sobre el cliente',
  `usuario_id` int(11) DEFAULT NULL COMMENT 'ID del usuario que gestiona al cliente',
  `cl_activo` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Estado activo/inactivo del cliente',
  `fecha_actualizacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Fecha de última actualización'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de clientes del sistema';

--
-- Estructura de tabla para la tabla `contactos_cliente`
--

CREATE TABLE `contactos_cliente` (
  `id` int(11) NOT NULL COMMENT 'ID único del contacto',
  `cliente_id` int(11) NOT NULL COMMENT 'ID del cliente asociado',
  `co_nombre` varchar(100) NOT NULL COMMENT 'Nombre del contacto',
  `co_cargo` varchar(100) DEFAULT NULL COMMENT 'Cargo del contacto',
  `co_email` varchar(100) DEFAULT NULL COMMENT 'Email del contacto',
  `co_telefono` varchar(20) DEFAULT NULL COMMENT 'Teléfono del contacto',
  `co_principal` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Indica si es el contacto principal',
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp() COMMENT 'Fecha de creación del contacto',
  `fecha_actualizacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Fecha de última actualización'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de contactos de clientes';


-- Insertar datos de ejemplo en la tabla clientes
INSERT INTO `clientes` (`cl_nombre`, `cl_apellido`, `cl_empresa`, `cl_email`, `cl_telefono`, `cl_direccion`, `cl_ciudad`, `cl_pais`, `cl_tipo`, `cl_notas`, `usuario_id`, `cl_activo`) VALUES
('Juan', 'Pérez', 'TechSolutions SA', 'juan.perez@techsolutions.com', '+54911234567', 'Av. Corrientes 1234', 'Buenos Aires', 'Argentina', 'actual', 'Cliente importante del sector tecnológico', 4, 1),
('María', 'González', 'Marketing Pro', 'maria.gonzalez@mkpro.com', '+54911445566', 'Florida 500', 'Buenos Aires', 'Argentina', 'actual', 'Especialista en marketing digital', 4, 1),
('Carlos', 'Rodríguez', 'Industrias RG', 'carlos.rodriguez@rg.com', '+54911556677', 'Callao 123', 'Buenos Aires', 'Argentina', 'potencial', 'Interesado en servicios de consultoría', 4, 1),
('Ana', 'Martínez', 'Diseño Creativo', 'ana.martinez@dc.com', '+54911667788', 'Santa Fe 789', 'Buenos Aires', 'Argentina', 'actual', 'Cliente del área de diseño', 4, 1),
('Luis', 'García', 'Constructora García', 'luis.garcia@cg.com', '+54911778899', 'Libertador 1000', 'Buenos Aires', 'Argentina', 'actual', 'Proyecto en curso', 4, 1),
('Patricia', 'López', 'Educación Plus', 'patricia.lopez@edplus.com', '+54911889900', 'Belgrano 567', 'Buenos Aires', 'Argentina', 'potencial', 'Sector educativo', 4, 1),
('Roberto', 'Fernández', 'Logística RF', 'roberto.fernandez@rf.com', '+54911990011', 'San Martín 432', 'Córdoba', 'Argentina', 'actual', 'Servicios logísticos', 4, 1),
('Laura', 'Sánchez', 'Consultora LS', 'laura.sanchez@ls.com', '+54911001122', 'Independencia 789', 'Rosario', 'Argentina', 'actual', 'Consultora financiera', 4, 1),
('Diego', 'Torres', 'Alimentos DT', 'diego.torres@dt.com', '+54911112233', 'Rivadavia 345', 'Mendoza', 'Argentina', 'potencial', 'Sector alimenticio', 4, 1),
('Sofía', 'Ramírez', 'Farmacia SR', 'sofia.ramirez@sr.com', '+54911223344', 'Sarmiento 678', 'La Plata', 'Argentina', 'actual', 'Sector farmacéutico', 4, 1),
('Miguel', 'Díaz', 'Tecnología MD', 'miguel.diaz@md.com', '+54911334455', 'Maipú 890', 'Buenos Aires', 'Argentina', 'actual', 'Desarrollo de software', 4, 1),
('Carolina', 'Ruiz', 'Textiles CR', 'carolina.ruiz@cr.com', '+54911445566', 'Lavalle 123', 'Buenos Aires', 'Argentina', 'potencial', 'Industria textil', 4, 1),
('Fernando', 'Morales', 'Seguros FM', 'fernando.morales@fm.com', '+54911556677', 'Tucumán 456', 'Buenos Aires', 'Argentina', 'actual', 'Sector seguros', 4, 1),
('Valeria', 'Castro', 'Inmobiliaria VC', 'valeria.castro@vc.com', '+54911667788', 'Córdoba 789', 'Buenos Aires', 'Argentina', 'actual', 'Sector inmobiliario', 4, 1),
('Gustavo', 'Silva', 'Transporte GS', 'gustavo.silva@gs.com', '+54911778899', 'Entre Ríos 012', 'Buenos Aires', 'Argentina', 'potencial', 'Transporte de carga', 4, 1),
('Marcela', 'Ortiz', 'Eventos MO', 'marcela.ortiz@mo.com', '+54911889900', 'Paraná 345', 'Buenos Aires', 'Argentina', 'actual', 'Organización de eventos', 4, 1),
('Ricardo', 'Núñez', 'Metalúrgica RN', 'ricardo.nunez@rn.com', '+54911990011', 'Jujuy 678', 'Buenos Aires', 'Argentina', 'actual', 'Sector metalúrgico', 4, 1),
('Andrea', 'Peralta', 'Turismo AP', 'andrea.peralta@ap.com', '+54911001122', 'Salta 901', 'Buenos Aires', 'Argentina', 'potencial', 'Agencia de viajes', 4, 1),
('Pablo', 'Medina', 'Servicios PM', 'pablo.medina@pm.com', '+54911112233', 'Mendoza 234', 'Buenos Aires', 'Argentina', 'actual', 'Servicios generales', 4, 1),
('Lucía', 'Flores', 'Consultoría LF', 'lucia.flores@lf.com', '+54911223344', 'Neuquén 567', 'Buenos Aires', 'Argentina', 'actual', 'Consultoría empresarial', 4, 1);

-- Insertar contactos asociados a los clientes
INSERT INTO `contactos_cliente` (`cliente_id`, `co_nombre`, `co_cargo`, `co_email`, `co_telefono`, `co_principal`) VALUES
(1, 'Juan Pérez', 'CEO', 'juan.perez@techsolutions.com', '+54911234567', 1),
(1, 'María López', 'Gerente IT', 'maria.lopez@techsolutions.com', '+54911234568', 0),
(2, 'María González', 'Directora', 'maria.gonzalez@mkpro.com', '+54911445566', 1),
(3, 'Carlos Rodríguez', 'Presidente', 'carlos.rodriguez@rg.com', '+54911556677', 1),
(4, 'Ana Martínez', 'Directora Creativa', 'ana.martinez@dc.com', '+54911667788', 1),
(5, 'Luis García', 'Gerente General', 'luis.garcia@cg.com', '+54911778899', 1),
(6, 'Patricia López', 'Directora Académica', 'patricia.lopez@edplus.com', '+54911889900', 1),
(7, 'Roberto Fernández', 'Gerente Operaciones', 'roberto.fernandez@rf.com', '+54911990011', 1),
(8, 'Laura Sánchez', 'Consultora Senior', 'laura.sanchez@ls.com', '+54911001122', 1),
(9, 'Diego Torres', 'Director Comercial', 'diego.torres@dt.com', '+54911112233', 1),
(10, 'Sofía Ramírez', 'Gerente', 'sofia.ramirez@sr.com', '+54911223344', 1),
(11, 'Miguel Díaz', 'CTO', 'miguel.diaz@md.com', '+54911334455', 1),
(12, 'Carolina Ruiz', 'Directora', 'carolina.ruiz@cr.com', '+54911445566', 1),
(13, 'Fernando Morales', 'Gerente Comercial', 'fernando.morales@fm.com', '+54911556677', 1),
(14, 'Valeria Castro', 'Broker Principal', 'valeria.castro@vc.com', '+54911667788', 1),
(15, 'Gustavo Silva', 'Director Logística', 'gustavo.silva@gs.com', '+54911778899', 1),
(16, 'Marcela Ortiz', 'Coordinadora', 'marcela.ortiz@mo.com', '+54911889900', 1),
(17, 'Ricardo Núñez', 'Gerente Planta', 'ricardo.nunez@rn.com', '+54911990011', 1),
(18, 'Andrea Peralta', 'Directora', 'andrea.peralta@ap.com', '+54911001122', 1),
(19, 'Pablo Medina', 'Gerente General', 'pablo.medina@pm.com', '+54911112233', 1),
(20, 'Lucía Flores', 'Consultora Senior', 'lucia.flores@lf.com', '+54911223344', 1);

-- Primero, asegurarnos que la tabla clientes tenga AUTO_INCREMENT antes de insertar
ALTER TABLE `clientes` 
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID único del cliente',
  AUTO_INCREMENT = 1;

-- Deshabilitar temporalmente las restricciones de clave foránea
SET FOREIGN_KEY_CHECKS=0;

-- Insertar datos en la tabla clientes
INSERT INTO `clientes` (`id`, `cl_nombre`, `cl_apellido`, `cl_empresa`, `cl_email`, `cl_telefono`, `cl_direccion`, `cl_ciudad`, `cl_pais`, `cl_tipo`, `cl_notas`, `usuario_id`, `cl_activo`) VALUES
(1, 'Juan', 'Pérez', 'TechSolutions SA', 'juan.perez@techsolutions.com', '+54911234567', 'Av. Corrientes 1234', 'Buenos Aires', 'Argentina', 'actual', 'Cliente importante del sector tecnológico', 4, 1),
(2, 'María', 'González', 'Marketing Pro', 'maria.gonzalez@mkpro.com', '+54911445566', 'Florida 500', 'Buenos Aires', 'Argentina', 'actual', 'Especialista en marketing digital', 4, 1),
(3, 'Carlos', 'Rodríguez', 'Industrias RG', 'carlos.rodriguez@rg.com', '+54911556677', 'Callao 123', 'Buenos Aires', 'Argentina', 'potencial', 'Interesado en servicios de consultoría', 4, 1),
(4, 'Ana', 'Martínez', 'Diseño Creativo', 'ana.martinez@dc.com', '+54911667788', 'Santa Fe 789', 'Buenos Aires', 'Argentina', 'actual', 'Cliente del área de diseño', 4, 1),
(5, 'Luis', 'García', 'Constructora García', 'luis.garcia@cg.com', '+54911778899', 'Libertador 1000', 'Buenos Aires', 'Argentina', 'actual', 'Proyecto en curso', 4, 1);

-- Insertar datos en la tabla contactos_cliente con IDs específicos
INSERT INTO `contactos_cliente` (`cliente_id`, `co_nombre`, `co_cargo`, `co_email`, `co_telefono`, `co_principal`) VALUES
(1, 'Juan Pérez', 'CEO', 'juan.perez@techsolutions.com', '+54911234567', 1),
(1, 'María López', 'Gerente IT', 'maria.lopez@techsolutions.com', '+54911234568', 0),
(2, 'María González', 'Directora', 'maria.gonzalez@mkpro.com', '+54911445566', 1),
(3, 'Carlos Rodríguez', 'Presidente', 'carlos.rodriguez@rg.com', '+54911556677', 1),
(4, 'Ana Martínez', 'Directora Creativa', 'ana.martinez@dc.com', '+54911667788', 1),
(5, 'Luis García', 'Gerente General', 'luis.garcia@cg.com', '+54911778899', 1);

-- Volver a habilitar las restricciones de clave foránea
SET FOREIGN_KEY_CHECKS=1;


--
-- Estructura de tabla para la tabla `historial_cliente`
--

CREATE TABLE `historial_cliente` (
  `id` int(11) NOT NULL COMMENT 'ID único del registro',
  `cliente_id` int(11) NOT NULL COMMENT 'ID del cliente',
  `usuario_id` int(11) NOT NULL COMMENT 'ID del usuario que realizó la acción',
  `tipo_accion` enum('contacto','reunion','cotizacion','venta','otro') NOT NULL COMMENT 'Tipo de acción realizada',
  `descripcion` text NOT NULL COMMENT 'Descripción de la acción',
  `fecha_accion` timestamp NULL DEFAULT current_timestamp() COMMENT 'Fecha de la acción',
  `resultado` text DEFAULT NULL COMMENT 'Resultado de la acción',
  `seguimiento_requerido` tinyint(1) DEFAULT 0 COMMENT 'Indica si requiere seguimiento',
  `fecha_seguimiento` date DEFAULT NULL COMMENT 'Fecha programada para seguimiento'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Historial de interacciones con clientes';

--
-- Índices para las tablas
--

ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuario_id` (`usuario_id`),
  ADD KEY `idx_cl_email` (`cl_email`),
  ADD KEY `idx_cl_tipo` (`cl_tipo`),
  ADD KEY `idx_cl_activo` (`cl_activo`),
  ADD CONSTRAINT `fk_clientes_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

ALTER TABLE `contactos_cliente`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_cliente_id` (`cliente_id`),
  ADD KEY `idx_co_email` (`co_email`),
  ADD CONSTRAINT `fk_contactos_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE;

ALTER TABLE `historial_cliente`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_cliente_id` (`cliente_id`),
  ADD KEY `idx_usuario_id` (`usuario_id`),
  ADD KEY `idx_tipo_accion` (`tipo_accion`),
  ADD KEY `idx_fecha_accion` (`fecha_accion`),
  ADD CONSTRAINT `fk_historial_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_historial_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- AUTO_INCREMENT para las tablas
--

ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `contactos_cliente`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `historial_cliente`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- finalizado 30/08/2025

--
-- Estructura de tabla para la tabla `proyectos`
-- 31/08/2025
--

CREATE TABLE `proyectos` (
  `id` int(11) NOT NULL COMMENT 'ID único del proyecto',
  `pr_titulo` varchar(200) NOT NULL COMMENT 'Título del proyecto',
  `pr_descripcion` text DEFAULT NULL COMMENT 'Descripción detallada del proyecto',
  `pr_estado` enum('propuesta','en_desarrollo','en_revision','finalizado','cancelado','pausado') NOT NULL DEFAULT 'propuesta' COMMENT 'Estado actual del proyecto',
  `pr_fecha_inicio` date DEFAULT NULL COMMENT 'Fecha de inicio del proyecto',
  `pr_fecha_fin` date DEFAULT NULL COMMENT 'Fecha de finalización del proyecto',
  `pr_fecha_estimada` date DEFAULT NULL COMMENT 'Fecha estimada de finalización',
  `pr_presupuesto` decimal(10,2) DEFAULT NULL COMMENT 'Presupuesto asignado al proyecto',
  `pr_prioridad` enum('baja','media','alta','critica') NOT NULL DEFAULT 'media' COMMENT 'Prioridad del proyecto',
  `pr_progreso` int(3) DEFAULT 0 COMMENT 'Porcentaje de progreso del proyecto (0-100)',
  `cliente_id` int(11) DEFAULT NULL COMMENT 'ID del cliente asociado',
  `usuario_id` int(11) DEFAULT NULL COMMENT 'ID del usuario responsable del proyecto',
  `pr_activo` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Estado activo/inactivo del proyecto',
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp() COMMENT 'Fecha de creación del proyecto',
  `fecha_actualizacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Fecha de última actualización'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de proyectos del sistema';

--
-- Estructura de tabla para la tabla `tareas_proyecto`
--

CREATE TABLE `tareas_proyecto` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID único de la tarea',
  `proyecto_id` int(11) NOT NULL COMMENT 'ID del proyecto asociado',
  `ta_titulo` varchar(200) NOT NULL COMMENT 'Título de la tarea',
  `ta_descripcion` text DEFAULT NULL COMMENT 'Descripción de la tarea',
  `ta_estado` enum('pendiente','en_progreso','completada','cancelada') NOT NULL DEFAULT 'pendiente' COMMENT 'Estado de la tarea',
  `ta_fecha_vencimiento` date DEFAULT NULL COMMENT 'Fecha de vencimiento de la tarea',
  `ta_prioridad` enum('baja','media','alta') NOT NULL DEFAULT 'media' COMMENT 'Prioridad de la tarea',
  `usuario_asignado_id` int(11) DEFAULT NULL COMMENT 'ID del usuario asignado a la tarea',
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp() COMMENT 'Fecha de creación de la tarea',
  `fecha_actualizacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Fecha de última actualización',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de tareas de proyectos';

-- Insertar datos de ejemplo
INSERT INTO `proyectos` (`pr_titulo`, `pr_descripcion`, `pr_estado`, `pr_fecha_inicio`, `pr_fecha_fin`, `pr_fecha_estimada`, `pr_presupuesto`, `pr_prioridad`, `pr_progreso`, `cliente_id`, `usuario_id`, `pr_activo`) VALUES
('Desarrollo Web Corporativo', 'Diseño y desarrollo de sitio web corporativo con panel administrativo', 'en_desarrollo', '2025-08-01', NULL, '2025-10-15', 15000.00, 'alta', 65, 1, 4, 1),
('Sistema de Inventario', 'Desarrollo de sistema de gestión de inventario para empresa', 'propuesta', NULL, NULL, '2025-12-30', 25000.00, 'media', 0, 2, 4, 1),
('App Móvil E-commerce', 'Aplicación móvil para tienda en línea', 'en_revision', '2025-07-15', NULL, '2025-09-30', 30000.00, 'alta', 85, 4, 4, 1),
('Rediseño de Marca', 'Renovación completa de identidad corporativa', 'finalizado', '2025-06-01', '2025-08-20', '2025-08-15', 8000.00, 'media', 100, 5, 4, 1),
('Consultoría IT', 'Asesoramiento en tecnologías de la información', 'en_desarrollo', '2025-08-10', NULL, '2025-11-10', 12000.00, 'baja', 30, 8, 4, 1),
('Portal Educativo', 'Plataforma de cursos en línea', 'propuesta', NULL, NULL, '2026-02-28', 40000.00, 'alta', 0, 6, 4, 1);

-- Insertar tareas de ejemplo
INSERT INTO `tareas_proyecto` (`proyecto_id`, `ta_titulo`, `ta_descripcion`, `ta_estado`, `ta_fecha_vencimiento`, `ta_prioridad`, `usuario_asignado_id`) VALUES
(1, 'Diseño de wireframes', 'Crear wireframes para las páginas principales', 'completada', '2025-08-15', 'alta', 4),
(1, 'Desarrollo frontend', 'Implementar diseño responsive', 'en_progreso', '2025-09-15', 'alta', 4),
(1, 'Integración API', 'Conectar frontend con backend', 'pendiente', '2025-10-01', 'media', 4),
(3, 'Testing de la aplicación', 'Pruebas exhaustivas en diferentes dispositivos', 'en_progreso', '2025-09-20', 'alta', 4),
(3, 'Optimización de rendimiento', 'Mejorar velocidad de carga', 'pendiente', '2025-09-25', 'media', 4);

--
-- Índices para las tablas
--

ALTER TABLE `proyectos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_cliente_id` (`cliente_id`),
  ADD KEY `idx_usuario_id` (`usuario_id`),
  ADD KEY `idx_pr_estado` (`pr_estado`),
  ADD KEY `idx_pr_prioridad` (`pr_prioridad`),
  ADD KEY `idx_pr_activo` (`pr_activo`),
  ADD KEY `idx_fecha_creacion` (`fecha_creacion`),
  ADD CONSTRAINT `fk_proyectos_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_proyectos_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

ALTER TABLE `tareas_proyecto`
  ADD KEY `idx_proyecto_id` (`proyecto_id`),
  ADD KEY `idx_usuario_asignado_id` (`usuario_asignado_id`),
  ADD KEY `idx_ta_estado` (`ta_estado`),
  ADD KEY `idx_ta_prioridad` (`ta_prioridad`),
  ADD CONSTRAINT `fk_tareas_proyecto` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tareas_usuario` FOREIGN KEY (`usuario_asignado_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- AUTO_INCREMENT para las tablas
--

ALTER TABLE `proyectos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID único del proyecto';



-- finalizado proyectos 31/08/2025

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
