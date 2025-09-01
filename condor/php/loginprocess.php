<?php
// ===== ARCHIVO DE PROCESAMIENTO DE LOGIN =====
/**
 * Este archivo procesa las solicitudes de login del formulario
 * Valida las credenciales del usuario y establece la sesión
 * Retorna respuestas JSON para el frontend
 */

// Configuración de errores para desarrollo
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuración de seguridad y headers
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Manejar preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Incluir configuración de base de datos
require_once 'config_bd.php';

// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ===== CLASE PARA MANEJAR RESPUESTAS JSON =====
class JsonResponse {
    /**
     * Genera una respuesta JSON de éxito
     * @param string $message Mensaje de éxito
     * @param array $data Datos adicionales
     * @return string JSON de respuesta
     */
    public static function success($message, $data = null) {
        return json_encode([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Genera una respuesta JSON de error
     * @param string $message Mensaje de error
     * @param int $code Código de estado HTTP
     * @return string JSON de respuesta
     */
    public static function error($message, $code = 400) {
        http_response_code($code);
        return json_encode([
            'success' => false,
            'message' => $message,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
}

// ===== CLASE PARA MANEJAR AUTENTICACIÓN =====
class AuthManager {
    private $db;
    
    /**
     * Constructor que recibe la conexión a la base de datos
     * @param PDO $db Conexión PDO a la base de datos
     */
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Autentica un usuario con sus credenciales
     * @param string $username Nombre de usuario
     * @param string $password Contraseña
     * @return array Resultado de la autenticación
     */
    public function authenticate($username, $password) {
        try {
            // Preparar consulta SQL para buscar el usuario
            $stmt = $this->db->prepare("
                SELECT id, us_username, us_password, us_rol, us_email, us_activo, us_fecha_ultimo_acceso, 
                       us_nombre, us_apellido, us_bio, us_foto_perfil, us_url_perfil, us_ultimo_ip
                FROM usuarios 
                WHERE us_username = :username AND us_activo = 1
            ");
            
            // Ejecutar la consulta con el nombre de usuario
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch();
            
            // Verificar si el usuario existe
            if (!$user) {
                return ['success' => false, 'message' => 'Usuario no encontrado o inactivo'];
            }
            
            // Verificar si la contraseña es correcta usando password_verify
            if (!password_verify($password, $user['us_password'])) {
                return ['success' => false, 'message' => 'Contraseña incorrecta'];
            }
            
            // Actualizar último acceso del usuario
            $this->updateLastAccess($user['id']);
            
            // Remover la contraseña del array antes de retornar
            unset($user['us_password']);
            
            // Retornar éxito con datos del usuario
            return [
                'success' => true, 
                'message' => 'Autenticación exitosa',
                'user' => $user
            ];
            
        } catch (PDOException $e) {
            // Registrar error en el log
            error_log("Error en autenticación: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error interno del servidor'];
        }
    }
    
    /**
     * Actualiza el timestamp de último acceso del usuario
     * @param int $userId ID del usuario
     */
    private function updateLastAccess($userId) {
        try {
            $stmt = $this->db->prepare("
                UPDATE usuarios 
                SET us_fecha_ultimo_acceso = NOW(), us_ultimo_ip = :ip
                WHERE id = :id
            ");
            $stmt->execute(['id' => $userId, 'ip' => $_SERVER['REMOTE_ADDR'] ?? '']);
        } catch (PDOException $e) {
            error_log("Error actualizando último acceso: " . $e->getMessage());
        }
    }
}

// ===== FUNCIÓN PARA VALIDAR DATOS DE ENTRADA =====
/**
 * Valida y sanitiza los datos recibidos del formulario
 * @param array $data Datos del formulario
 * @return array Array con errores (vacío si no hay errores)
 */
function validateInput($data) {
    $errors = [];
    
    // Validar username
    if (empty($data['username'])) {
        $errors[] = 'El nombre de usuario es obligatorio';
    } elseif (strlen($data['username']) < 3) {
        $errors[] = 'El nombre de usuario debe tener al menos 3 caracteres';
    } elseif (strlen($data['username']) > 50) {
        $errors[] = 'El nombre de usuario no puede exceder 50 caracteres';
    }
    
    // Validar password
    if (empty($data['password'])) {
        $errors[] = 'La contraseña es obligatoria';
    } elseif (strlen($data['password']) < 6) {
        $errors[] = 'La contraseña debe tener al menos 6 caracteres';
    }
    
    return $errors;
}

// ===== FUNCIÓN PARA SANITIZAR DATOS =====
/**
 * Limpia y sanitiza los datos de entrada
 * @param array $data Datos de entrada
 * @return array Datos sanitizados
 */
function sanitizeInput($data) {
    $sanitized = [];
    
    // Sanitizar username
    if (isset($data['username'])) {
        $sanitized['username'] = trim(strip_tags($data['username']));
    }
    
    // La contraseña no se sanitiza para preservar caracteres especiales
    if (isset($data['password'])) {
        $sanitized['password'] = $data['password'];
    }
    
    // Sanitizar checkbox "recordarme"
    $sanitized['remember'] = isset($data['remember']) && $data['remember'] === 'true';
    
    return $sanitized;
}

// ===== MANEJO PRINCIPAL DE LA PETICIÓN =====

// Verificar que la petición sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo JsonResponse::error('Método no permitido', 405);
    exit;
}

try {
    // Obtener datos JSON del cuerpo de la petición
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Si no hay datos JSON, intentar con $_POST
    if (!$input) {
        $input = $_POST;
    }
    
    // Sanitizar los datos de entrada
    $sanitizedData = sanitizeInput($input);
    
    // Validar los datos sanitizados
    $validationErrors = validateInput($sanitizedData);
    
    // Si hay errores de validación, retornar error
    if (!empty($validationErrors)) {
        echo JsonResponse::error(implode(', ', $validationErrors), 400);
        exit;
    }
    
    // Obtener instancia de la base de datos
    $database = Database::getInstance();
    $pdo = $database->getConnection();
    
    // Crear instancia del manejador de autenticación
    $authManager = new AuthManager($pdo);
    
    // Intentar autenticar al usuario
    $result = $authManager->authenticate($sanitizedData['username'], $sanitizedData['password']);
    
    // Si la autenticación fue exitosa
    if ($result['success']) {
        // Guardar datos del usuario en la sesión
        $_SESSION['user_id'] = $result['user']['id'];
        $_SESSION['username'] = $result['user']['us_username'];
        $_SESSION['rol'] = $result['user']['us_rol'];
        $_SESSION['email'] = $result['user']['us_email'];
        $_SESSION['nombre_completo'] = $result['user']['us_nombre'] . ' ' . $result['user']['us_apellido'];
        $_SESSION['us_nombre'] = $result['user']['us_nombre'];
        $_SESSION['us_apellido'] = $result['user']['us_apellido'];
        $_SESSION['us_foto_perfil'] = $result['user']['us_foto_perfil'];
        $_SESSION['us_url_perfil'] = $result['user']['us_url_perfil'];
        $_SESSION['logged_in'] = true;
        $_SESSION['login_time'] = time();
        
        // Si el usuario marcó "recordarme", configurar cookie
        if ($sanitizedData['remember']) {
            // Crear token de sesión persistente
            $rememberToken = bin2hex(random_bytes(32));
            setcookie('remember_token', $rememberToken, time() + (30 * 24 * 60 * 60), '/');
            
            // Aquí podrías guardar el token en la base de datos
            // $this->saveRememberToken($result['user']['id'], $rememberToken);
        }
        
        // Determinar URL de redirección según el rol
        $redirectUrl = 'dashboard.php'; // URL por defecto
        
        switch (strtolower($result['user']['us_rol'])) {
            case 'administrador':
                $redirectUrl = 'admin/dashboard.php';
                break;
            case 'usuario':
                $redirectUrl = 'usuario/dashboard.php';
                break;
            default:
                $redirectUrl = 'dashboard.php';
        }
        
        // Retornar respuesta exitosa con URL de redirección
        echo JsonResponse::success($result['message'], [
            'user' => $result['user'],
            'redirect' => $redirectUrl
        ]);
        
    } else {
        // Retornar error de autenticación
        echo JsonResponse::error($result['message'], 401);
    }
    
} catch (Exception $e) {
    // Registrar error en el log
    error_log("Error en loginprocess.php: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    
    // Retornar error más detallado en desarrollo
    $errorMessage = 'Error interno del servidor';
    if (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
        $errorMessage = 'Error: ' . $e->getMessage();
    }
    
    echo JsonResponse::error($errorMessage, 500);
}
?>
