<?php

// Establecer el tipo de contenido de la respuesta como JSON
header('Content-Type: application/json');
// Permitir acceso desde cualquier origen (CORS)
header('Access-Control-Allow-Origin: *');
// Permitir solo el método GET para las peticiones
header('Access-Control-Allow-Methods: GET');
// Permitir el encabezado Content-Type en las peticiones
header('Access-Control-Allow-Headers: Content-Type');

// Incluir archivo de configuración de base de datos
require_once 'config_bd.php';

try {
    // Conexión a la base de datos usando la clase Database
    $database = Database::getInstance();
    $pdo = $database->getConnection();
    
    // Verificar que la petición sea de tipo GET
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Consulta SQL para obtener todos los usuarios
        $sql = "SELECT id, us_username, us_email, us_rol, us_nombre, us_apellido, 
                       us_bio, us_foto_perfil, us_url_perfil, us_fecha_ultimo_acceso,
                       us_ultimo_ip, us_activo, us_fecha_registro, fecha_actualizacion
                FROM usuarios 
                ORDER BY us_fecha_registro DESC";
        
        // Preparar la consulta SQL para evitar inyección de código
        $stmt = $pdo->prepare($sql);
        // Ejecutar la consulta preparada
        $stmt->execute();
        
        // Obtener TODOS los resultados como array asociativo
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if ($usuarios) {
            // Convertir el array de usuarios a formato JSON y enviarlo como respuesta
            echo json_encode($usuarios);
        } else {
            // Si no hay usuarios, devolver array vacío
            echo json_encode([]);
        }
        
    } else {
        // Si el método no es GET, devolver error
        echo json_encode(['error' => 'Método no permitido']);
    }
    
} catch (PDOException $e) {
    // Capturar errores específicos de la base de datos
    echo json_encode(['error' => 'Error de base de datos: ' . $e->getMessage()]);
} catch (Exception $e) {
    // Capturar cualquier otro error del servidor
    echo json_encode(['error' => 'Error del servidor: ' . $e->getMessage()]);
}

?>