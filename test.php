<?php
// test.php - Archivo de prueba para verificar configuración

// Configurar headers
header('Content-Type: application/json');

// Información del servidor
$serverInfo = array(
    'php_version' => phpversion(),
    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
    'mail_function' => function_exists('mail') ? 'Available' : 'Not Available',
    'error_reporting' => error_reporting(),
    'display_errors' => ini_get('display_errors'),
    'log_errors' => ini_get('log_errors'),
    'error_log' => ini_get('error_log'),
    'post_max_size' => ini_get('post_max_size'),
    'upload_max_filesize' => ini_get('upload_max_filesize'),
    'max_execution_time' => ini_get('max_execution_time'),
    'memory_limit' => ini_get('memory_limit')
);

// Verificar si podemos escribir archivos
$testFile = 'test_write.txt';
$canWrite = false;
try {
    $canWrite = file_put_contents($testFile, 'Test write at ' . date('Y-m-d H:i:s'));
    if ($canWrite) {
        unlink($testFile); // Limpiar archivo de prueba
    }
} catch (Exception $e) {
    $canWrite = false;
}

$serverInfo['can_write_files'] = $canWrite ? 'Yes' : 'No';

// Verificar configuración de correo
$mailConfig = array(
    'sendmail_path' => ini_get('sendmail_path'),
    'smtp_host' => ini_get('SMTP'),
    'smtp_port' => ini_get('smtp_port')
);

$serverInfo['mail_config'] = $mailConfig;

// Respuesta
echo json_encode([
    'status' => 'success',
    'message' => 'Test completado',
    'server_info' => $serverInfo,
    'timestamp' => date('Y-m-d H:i:s')
]);
?> 