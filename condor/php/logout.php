<?php
// ===== ARCHIVO DE PROCESAMIENTO DE LOGOUT =====
/**
 * Este archivo procesa el cierre de sesión del usuario
 * Destruye la sesión y redirige al login
 */

// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ===== FUNCIÓN PARA LIMPIAR COOKIES =====
function clearCookies() {
    // Eliminar cookie de "recordarme" si existe
    if (isset($_COOKIE['remember_token'])) {
        setcookie('remember_token', '', time() - 3600, '/');
    }
    
    // Eliminar otras cookies de sesión si las hay
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
}

// ===== FUNCIÓN PARA REGISTRAR LOGOUT =====
function logLogout($userId = null, $username = null) {
    try {
        // Incluir configuración de base de datos
        require_once 'config_bd.php';
        
        // Obtener instancia de la base de datos
        $database = Database::getInstance();
        $pdo = $database->getConnection();
        
        // Registrar el logout en la tabla de logs
        $stmt = $pdo->prepare("
            INSERT INTO logs_acceso (
                usuario_id, username, ip_address, user_agent, 
                tipo_acceso, fecha_acceso, detalles
            ) VALUES (?, ?, ?, ?, 'logout', NOW(), ?)
        ");
        
        $stmt->execute([
            $userId,
            $username,
            $_SERVER['REMOTE_ADDR'] ?? '',
            $_SERVER['HTTP_USER_AGENT'] ?? '',
            'Logout manual del usuario'
        ]);
        
    } catch (Exception $e) {
        // Si hay error al registrar el log, no interrumpir el proceso de logout
        error_log("Error registrando logout: " . $e->getMessage());
    }
}

// ===== PROCESO PRINCIPAL DE LOGOUT =====

// Obtener información del usuario antes de destruir la sesión
$userId = $_SESSION['user_id'] ?? null;
$username = $_SESSION['username'] ?? null;

// Registrar el logout
logLogout($userId, $username);

// Limpiar todas las variables de sesión
$_SESSION = array();

// Destruir la sesión
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destruir la sesión
session_destroy();

// Limpiar cookies adicionales
clearCookies();

// ===== REDIRECCIÓN =====

// Verificar si la petición es AJAX
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    
    // Respuesta JSON para peticiones AJAX
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => true,
        'message' => 'Sesión cerrada exitosamente',
        'redirect' => '../index.php'
    ]);
    exit;
    
} else {
    // Redirección normal para peticiones no-AJAX
    header('Location: ../index.php');
    exit;
}
?>
