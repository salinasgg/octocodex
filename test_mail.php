<?php
// test_mail.php - Archivo de prueba para diagnosticar problemas

// Habilitar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configurar headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET');
header('Access-Control-Allow-Headers: Content-Type');

// Verificar si es una petición POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del formulario
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : 'Test User';
    $email = isset($_POST['email']) ? trim($_POST['email']) : 'test@example.com';
    $asunto = isset($_POST['asunto']) ? trim($_POST['asunto']) : 'Test Subject';
    $mensaje = isset($_POST['mensaje']) ? trim($_POST['mensaje']) : 'Test Message';
    
    // Crear respuesta de prueba
    $response = [
        'success' => true,
        'message' => '¡Mensaje de prueba enviado con éxito!',
        'debug_info' => [
            'method' => $_SERVER['REQUEST_METHOD'],
            'nombre' => $nombre,
            'email' => $email,
            'asunto' => $asunto,
            'mensaje' => $mensaje,
            'timestamp' => date('Y-m-d H:i:s'),
            'php_version' => phpversion(),
            'mail_function' => function_exists('mail') ? 'Available' : 'Not Available'
        ]
    ];
    
    // Guardar log de prueba
    $logEntry = date('Y-m-d H:i:s') . " - PRUEBA: Mensaje recibido desde: {$email} - Asunto: {$asunto}\n";
    file_put_contents('test_log.txt', $logEntry, FILE_APPEND | LOCK_EX);
    
    http_response_code(200);
    echo json_encode($response);
} else {
    // Si es GET, mostrar información del servidor
    $serverInfo = [
        'php_version' => phpversion(),
        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
        'request_method' => $_SERVER['REQUEST_METHOD'],
        'mail_function' => function_exists('mail') ? 'Available' : 'Not Available',
        'error_reporting' => error_reporting(),
        'display_errors' => ini_get('display_errors'),
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'message' => 'Servidor funcionando correctamente',
        'server_info' => $serverInfo
    ]);
}
?> 