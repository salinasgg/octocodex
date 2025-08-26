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
require_once 'php/config_bd.php';

try {
    // Obtener el user_id de la petición GET
    $user_id = $_GET['user_id'] ?? null;
    
    if (!$user_id) {
        echo json_encode(['error' => 'No se proporcionó user_id']);
        exit;
    }
    
    // Conexión a la base de datos usando la clase Database
    $database = Database::getInstance();
    $pdo = $database->getConnection();  
    
    // Verificar que la petición sea de tipo GET
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Consulta SQL para obtener el usuario específico
        $sql = "SELECT id, us_username, us_email, us_rol, us_nombre, us_apellido, 
               us_bio, us_foto_perfil, us_url_perfil, us_activo
               FROM usuarios WHERE id = :id";
        
        // Preparar la consulta SQL para evitar inyección de código
        $stmt = $pdo->prepare($sql);
        // Ejecutar la consulta preparada con el user_id
        $stmt->execute(['id' => $user_id]);
        
        // Obtener el resultado como array asociativo
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usuario) {
            // Convertir el array del usuario a formato JSON y enviarlo como respuesta
            echo json_encode($usuario);
        } else {
            echo json_encode(['error' => 'Usuario no encontrado']);
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
