-- INSERT para agregar nuevo usuario regular
-- Usuario: salinasuser
-- Contraseña: caca2025 (hasheada con bcrypt)
-- Rol: usuario

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
    'salinasuser', 
    '$2y$10$ba3y.M0oXddq0fVpDODGBum82JBzjCEQzu.8jWHoLN11T/rDz2R8a', 
    'salinasuser@condor.com', 
    'usuario', 
    1
);
