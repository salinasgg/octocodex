<?php
// basic_test.php - Prueba básica de PHP

// Habilitar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configurar headers
header('Content-Type: application/json');

// Información básica
$info = [
    'php_version' => phpversion(),
    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
    'request_method' => $_SERVER['REQUEST_METHOD'] ?? 'Unknown',
    'post_data' => $_POST,
    'timestamp' => date('Y-m-d H:i:s'),
    'can_write_files' => is_writable('.'),
    'error_reporting' => error_reporting(),
    'display_errors' => ini_get('display_errors')
];

// Respuesta
echo json_encode([
    'status' => 'success',
    'message' => 'PHP está funcionando correctamente',
    'info' => $info
]);
?> 