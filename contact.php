<?php
// Configuración de headers para CORS
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

// Verificar si es una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

// Obtener datos del formulario
$input = json_decode(file_get_contents('php://input'), true);

// Validar datos requeridos
if (!isset($input['nombre']) || !isset($input['email']) || !isset($input['asunto']) || !isset($input['mensaje'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Todos los campos son requeridos']);
    exit;
}

$nombre = trim($input['nombre']);
$email = trim($input['email']);
$asunto = trim($input['asunto']);
$mensaje = trim($input['mensaje']);

// Validaciones adicionales
if (empty($nombre) || empty($email) || empty($asunto) || empty($mensaje)) {
    http_response_code(400);
    echo json_encode(['error' => 'Todos los campos deben estar completos']);
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

// Configuración del email
$to = 'info@devstudio.com'; // Cambiar por el email real
$subject = 'Nuevo mensaje de contacto: ' . $asunto;

// Construir el mensaje
$emailBody = "
<html>
<head>
    <title>Nuevo mensaje de contacto</title>
</head>
<body>
    <h2>Nuevo mensaje de contacto desde el sitio web</h2>
    <table>
        <tr>
            <td><strong>Nombre:</strong></td>
            <td>{$nombre}</td>
        </tr>
        <tr>
            <td><strong>Email:</strong></td>
            <td>{$email}</td>
        </tr>
        <tr>
            <td><strong>Asunto:</strong></td>
            <td>{$asunto}</td>
        </tr>
        <tr>
            <td><strong>Mensaje:</strong></td>
            <td>" . nl2br($mensaje) . "</td>
        </tr>
    </table>
    <hr>
    <p><small>Este mensaje fue enviado desde el formulario de contacto de DevStudio.</small></p>
</body>
</html>
";

// Headers del email
$headers = array(
    'MIME-Version: 1.0',
    'Content-type: text/html; charset=UTF-8',
    'From: ' . $email,
    'Reply-To: ' . $email,
    'X-Mailer: PHP/' . phpversion()
);

// Intentar enviar el email
try {
    $mailSent = mail($to, $subject, $emailBody, implode("\r\n", $headers));
    
    if ($mailSent) {
        // Guardar en archivo de log (opcional)
        $logEntry = date('Y-m-d H:i:s') . " - Email enviado desde: {$email} - Asunto: {$asunto}\n";
        file_put_contents('contact_log.txt', $logEntry, FILE_APPEND | LOCK_EX);
        
        // Respuesta exitosa
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Mensaje enviado con éxito. Nos pondremos en contacto contigo pronto.'
        ]);
    } else {
        throw new Exception('Error al enviar el email');
    }
} catch (Exception $e) {
    // Log del error
    $errorLog = date('Y-m-d H:i:s') . " - Error enviando email: " . $e->getMessage() . "\n";
    file_put_contents('error_log.txt', $errorLog, FILE_APPEND | LOCK_EX);
    
    http_response_code(500);
    echo json_encode([
        'error' => 'Error interno del servidor. Por favor, intenta nuevamente más tarde.'
    ]);
}

// Función para validar y sanitizar datos
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Función para validar longitud de campos
function validateLength($field, $value, $min, $max) {
    $length = strlen($value);
    if ($length < $min || $length > $max) {
        return false;
    }
    return true;
}

// Validaciones adicionales de longitud
if (!validateLength('nombre', $nombre, 2, 100)) {
    http_response_code(400);
    echo json_encode(['error' => 'El nombre debe tener entre 2 y 100 caracteres']);
    exit;
}

if (!validateLength('asunto', $asunto, 5, 200)) {
    http_response_code(400);
    echo json_encode(['error' => 'El asunto debe tener entre 5 y 200 caracteres']);
    exit;
}

if (!validateLength('mensaje', $mensaje, 10, 1000)) {
    http_response_code(400);
    echo json_encode(['error' => 'El mensaje debe tener entre 10 y 1000 caracteres']);
    exit;
}

// Protección contra spam básica
$spamKeywords = ['casino', 'viagra', 'loan', 'credit', 'debt'];
$messageLower = strtolower($mensaje);
foreach ($spamKeywords as $keyword) {
    if (strpos($messageLower, $keyword) !== false) {
        http_response_code(400);
        echo json_encode(['error' => 'Mensaje detectado como spam']);
        exit;
    }
}

// Rate limiting básico (opcional)
$ip = $_SERVER['REMOTE_ADDR'];
$rateLimitFile = 'rate_limit_' . md5($ip) . '.txt';
$currentTime = time();
$timeWindow = 3600; // 1 hora

if (file_exists($rateLimitFile)) {
    $lastRequest = file_get_contents($rateLimitFile);
    if ($currentTime - $lastRequest < $timeWindow) {
        http_response_code(429);
        echo json_encode(['error' => 'Demasiadas solicitudes. Por favor, espera antes de enviar otro mensaje.']);
        exit;
    }
}

file_put_contents($rateLimitFile, $currentTime);
?> 