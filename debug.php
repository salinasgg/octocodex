<?php
// debug.php - Archivo de debug ultra-simplificado

// Habilitar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configurar headers
header('Content-Type: application/json');

// Verificar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

// Obtener datos del formulario
$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$asunto = isset($_POST['asunto']) ? trim($_POST['asunto']) : '';
$mensaje = isset($_POST['mensaje']) ? trim($_POST['mensaje']) : '';

// Validaciones básicas
if (empty($nombre) || empty($email) || empty($asunto) || empty($mensaje)) {
    http_response_code(400);
    echo json_encode(['error' => 'Todos los campos son requeridos']);
    exit;
}

// Simular envío exitoso (sin usar mail())
try {
    // Guardar log del envío exitoso
    $logEntry = date('Y-m-d H:i:s') . " - Email simulado desde: {$email} - Asunto: {$asunto}\n";
    file_put_contents('email_log.txt', $logEntry, FILE_APPEND | LOCK_EX);
    
    // Respuesta exitosa
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => '¡Mensaje enviado con éxito! (Simulado)',
        'debug_info' => [
            'nombre' => $nombre,
            'email' => $email,
            'asunto' => $asunto,
            'mensaje' => $mensaje,
            'timestamp' => date('Y-m-d H:i:s')
        ]
    ]);
} catch (Exception $e) {
    // Guardar log del error
    $errorLog = date('Y-m-d H:i:s') . " - Error: " . $e->getMessage() . " - Desde: {$email}\n";
    file_put_contents('email_error_log.txt', $errorLog, FILE_APPEND | LOCK_EX);
    
    http_response_code(500);
    echo json_encode([
        'error' => 'Error interno del servidor: ' . $e->getMessage()
    ]);
}
?> 