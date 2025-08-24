<?php
// mail_simple.php - Versión simplificada para pruebas

// Configurar headers para JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

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

// Validar formato de email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Formato de email inválido']);
    exit;
}

// Sanitizar datos
$nombre = htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8');
$email = filter_var($email, FILTER_SANITIZE_EMAIL);
$asunto = htmlspecialchars($asunto, ENT_QUOTES, 'UTF-8');
$mensaje = htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8');

// Configuración del correo
$to = 'salinasgeganb@gmail.com';
$subject = 'Nuevo mensaje de contacto: ' . $asunto;

// Cuerpo del correo simple
$emailBody = "
Nuevo mensaje de contacto desde el sitio web:

Nombre: {$nombre}
Email: {$email}
Asunto: {$asunto}
Mensaje: {$mensaje}

Fecha: " . date('d/m/Y H:i:s') . "
";

// Headers del correo
$headers = "From: {$email}\r\n";
$headers .= "Reply-To: {$email}\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// Intentar enviar el correo
try {
    // Verificar si la función mail está disponible
    if (!function_exists('mail')) {
        throw new Exception('Función mail() no disponible');
    }
    
    $mailSent = mail($to, $subject, $emailBody, $headers);
    
    if ($mailSent) {
        // Guardar log del envío exitoso
        $logEntry = date('Y-m-d H:i:s') . " - Email enviado exitosamente desde: {$email} - Asunto: {$asunto}\n";
        file_put_contents('email_log.txt', $logEntry, FILE_APPEND | LOCK_EX);
        
        // Respuesta exitosa
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => '¡Mensaje enviado con éxito! Nos pondremos en contacto contigo pronto.'
        ]);
    } else {
        throw new Exception('Error al enviar el email - mail() retornó false');
    }
} catch (Exception $e) {
    // Guardar log del error
    $errorLog = date('Y-m-d H:i:s') . " - Error enviando email: " . $e->getMessage() . " - Desde: {$email}\n";
    file_put_contents('email_error_log.txt', $errorLog, FILE_APPEND | LOCK_EX);
    
    http_response_code(500);
    echo json_encode([
        'error' => 'Error interno del servidor: ' . $e->getMessage(),
        'details' => 'Por favor, verifica la configuración del servidor de correo.'
    ]);
}
?> 