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
    // Verificar que la petición sea de tipo GET
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        echo json_encode(['error' => 'Método no permitido. Solo se permite GET.']);
        exit;
    }
    
    // Obtener y validar el ID del usuario
    $id = $_GET['id'] ?? null;
    
    if (!$id || !is_numeric($id)) {
        echo json_encode(['error' => 'ID de usuario inválido o no proporcionado']);
        exit;
    }
    
    // Conexión a la base de datos usando la clase Database
    $database = Database::getInstance();
    $pdo = $database->getConnection();
    
    // Consulta SQL para obtener la información del usuario
    $sql = "SELECT id, us_username, us_email, us_nombre, us_apellido, us_fecha_nacimiento, 
                   us_rol, us_activo, us_bio, us_foto_perfil, us_url_perfil, 
                   us_fecha_registro, fecha_actualizacion
            FROM usuarios 
            WHERE id = :id";
    
    // Preparar la consulta SQL para evitar inyección de código
    $stmt = $pdo->prepare($sql);
    
    // Ejecutar la consulta preparada
    $stmt->execute(['id' => $id]);
    
    // Obtener el resultado como array asociativo
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($usuario) {
        // Convertir el array de usuario a formato JSON y enviarlo como respuesta
        echo json_encode(['success' => true, 'data' => $usuario]);
    } else {
        // Si no se encuentra el usuario, devolver error
        echo json_encode(['error' => 'Usuario no encontrado']);
    }
    
} catch (PDOException $e) {
    // Capturar errores específicos de la base de datos
    echo json_encode(['error' => 'Error de base de datos: ' . $e->getMessage()]);
} catch (Exception $e) {
    // Capturar cualquier otro error del servidor
    echo json_encode(['error' => 'Error del servidor: ' . $e->getMessage()]);
}
?>