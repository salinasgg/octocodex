-- INSERT para agregar nuevo usuario administrador
-- Usuario: salinasgg
-- Contraseña: caca2025 (hasheada con bcrypt)
-- Rol: administrador

INSERT INTO `usuarios` (
    `us_nombre`, 
    `us_apellido`, 
    `us_username`, 
    `us_password`, 
    `us_email`, 
    `us_rol`, 
    `us_activo`
) VALUES (
    'Gabriel', 
    'Salinas', 
    'salinasgg', 
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
    'salinasgg@condor.com', 
    'administrador', 
    1
);


