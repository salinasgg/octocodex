<?php
// Establecer el tipo de contenido de la respuesta como JSON
header('Content-Type: application/json');
// Permitir acceso desde cualquier origen (CORS)
header('Access-Control-Allow-Origin: *');
// Permitir solo el método POST para las peticiones
header('Access-Control-Allow-Methods: POST');
// Permitir el encabezado Content-Type en las peticiones
header('Access-Control-Allow-Headers: Content-Type');

// Incluir archivo de configuración de base de datos
require_once 'config_bd.php';

try {
    // Verificar que la petición sea de tipo POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['error' => 'Método no permitido. Solo se permite POST.']);
        exit;
    }
    
    // Obtener y validar el ID del usuario
    $id = $_POST['id'] ?? null;
    
    if (!$id || !is_numeric($id)) {
        echo json_encode(['error' => 'ID de usuario inválido o no proporcionado']);
        exit;
    }
    
    // Conexión a la base de datos usando la clase Database
    $database = Database::getInstance();
    $pdo = $database->getConnection();
    
    // Verificar que el usuario existe antes de eliminarlo
    $checkSql = "SELECT id FROM usuarios WHERE id = :id";
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->execute(['id' => $id]);
    
    if ($checkStmt->rowCount() === 0) {
        echo json_encode(['error' => 'Usuario no encontrado']);
        exit;
    }
    
    // Consulta SQL para eliminar el usuario
    $sql = "DELETE FROM usuarios WHERE id = :id";
    
    // Preparar la consulta SQL para evitar inyección de código
    $stmt = $pdo->prepare($sql);
    
    // Ejecutar la consulta preparada
    $result = $stmt->execute(['id' => $id]);
    
    if ($result && $stmt->rowCount() > 0) {
        echo json_encode(['success' => 'Usuario eliminado exitosamente']);
    } else {
        echo json_encode(['error' => 'No se pudo eliminar el usuario']);
    }
    
} catch (PDOException $e) {
    // Capturar errores específicos de la base de datos
    echo json_encode(['error' => 'Error de base de datos: ' . $e->getMessage()]);
} catch (Exception $e) {
    // Capturar cualquier otro error del servidor
    echo json_encode(['error' => 'Error del servidor: ' . $e->getMessage()]);
}
?>