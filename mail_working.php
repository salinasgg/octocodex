<?php
// mail_working.php - Envío de correos funcional

// Habilitar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configurar headers
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

// Cuerpo del correo HTML
$emailBody = "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Nuevo mensaje de contacto</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 10px 10px 0 0; }
        .content { background: #f8f9fa; padding: 20px; border-radius: 0 0 10px 10px; }
        .field { margin-bottom: 15px; }
        .label { font-weight: bold; color: #007bff; }
        .value { background: white; padding: 10px; border-radius: 5px; border-left: 4px solid #007bff; }
        .footer { text-align: center; margin-top: 20px; padding: 15px; background: #e9ecef; border-radius: 5px; font-size: 12px; color: #6c757d; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h2>📧 Nuevo mensaje de contacto</h2>
            <p>Has recibido un nuevo mensaje desde el sitio web DevStudio</p>
        </div>
        
        <div class='content'>
            <div class='field'>
                <div class='label'>👤 Nombre:</div>
                <div class='value'>{$nombre}</div>
            </div>
            
            <div class='field'>
                <div class='label'>📧 Email:</div>
                <div class='value'>{$email}</div>
            </div>
            
            <div class='field'>
                <div class='label'>📝 Asunto:</div>
                <div class='value'>{$asunto}</div>
            </div>
            
            <div class='field'>
                <div class='label'>💬 Mensaje:</div>
                <div class='value'>" . nl2br($mensaje) . "</div>
            </div>
        </div>
        
        <div class='footer'>
            <p>Este mensaje fue enviado automáticamente desde el formulario de contacto de DevStudio</p>
            <p>Fecha y hora: " . date('d/m/Y H:i:s') . "</p>
        </div>
    </div>
</body>
</html>
";

// Headers del correo
$headers = array(
    'MIME-Version: 1.0',
    'Content-type: text/html; charset=UTF-8',
    'From: noreply@devstudio.com',
    'Reply-To: ' . $email,
    'X-Mailer: PHP/' . phpversion(),
    'X-Priority: 1',
    'X-MSMail-Priority: High'
);

// Intentar enviar el correo
try {
    // Verificar si la función mail está disponible
    if (!function_exists('mail')) {
        throw new Exception('Función mail() no disponible');
    }
    
    // Configurar parámetros adicionales para mejorar deliverability
    $additional_headers = implode("\r\n", $headers);
    
    $mailSent = mail($to, $subject, $emailBody, $additional_headers);
    
    if ($mailSent) {
        // Guardar log del envío exitoso
        $logEntry = date('Y-m-d H:i:s') . " - Email enviado exitosamente desde: {$email} - Asunto: {$asunto}\n";
        file_put_contents('email_log.txt', $logEntry, FILE_APPEND | LOCK_EX);
        
        // Respuesta exitosa
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => '¡Mensaje enviado con éxito! Nos pondremos en contacto contigo pronto.',
            'debug_info' => [
                'to' => $to,
                'from' => $email,
                'subject' => $subject,
                'timestamp' => date('Y-m-d H:i:s')
            ]
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
        'details' => 'El correo no se pudo enviar. Esto puede deberse a la configuración del servidor.',
        'suggestion' => 'Para solucionar esto, configura un servidor SMTP en tu hosting o usa un servicio de terceros.'
    ]);
}
?> 