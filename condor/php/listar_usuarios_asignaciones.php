<?php
/**
 * ================================================
 * LISTAR USUARIOS PARA ASIGNACIONES
 * ================================================
 * Archivo específico para listar usuarios disponibles para asignaciones
 */

require_once 'config_bd.php';
session_start();

// Configurar headers para API
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
header('Access-Control-Allow-Credentials: true');

// Verificar autenticación básica
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Modo debug para pruebas
    if (isset($_GET['debug']) && $_GET['debug'] === '1') {
        $_SESSION['logged_in'] = true;
        $_SESSION['user_id'] = 1;
    } else {
        http_response_code(401);
        echo json_encode([
            'exito' => false, 
            'mensaje' => 'No autorizado. Inicia sesión para continuar.'
        ]);
        exit;
    }
}

try {
    // Obtener conexión a la base de datos
    $database = Database::getInstance();
    $pdo = $database->getConnection();
    
    // Obtener acción
    $accion = $_REQUEST['accion'] ?? 'listar';
    
    switch ($accion) {
        case 'listar':
            listarUsuarios($pdo);
            break;
            
        case 'disponibles':
            listarUsuariosDisponibles($pdo);
            break;
            
        default:
            throw new Exception('Acción no válida: ' . $accion);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Error del servidor: ' . $e->getMessage()
    ]);
}

/**
 * Listar todos los usuarios activos
 */
function listarUsuarios($pdo) {
    try {
        $sql = "SELECT 
                    id,
                    us_username as username,
                    us_nombre as nombre,
                    us_apellido as apellido,
                    CONCAT(us_nombre, ' ', us_apellido) as nombre_completo,
                    us_email as email,
                    us_rol as rol,
                    us_foto_perfil as foto_perfil
                FROM usuarios 
                WHERE us_activo = 1 
                ORDER BY us_nombre ASC, us_apellido ASC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'exito' => true,
            'usuarios' => $usuarios,
            'total' => count($usuarios)
        ]);
        
    } catch (PDOException $e) {
        error_log("Error al listar usuarios: " . $e->getMessage());
        throw new Exception('Error al obtener usuarios: ' . $e->getMessage());
    }
}

/**
 * Listar usuarios disponibles para un proyecto específico
 */
function listarUsuariosDisponibles($pdo) {
    $proyecto_id = $_GET['proyecto_id'] ?? null;
    
    try {
        $sql = "SELECT 
                    u.id,
                    u.us_username as username,
                    u.us_nombre as nombre,
                    u.us_apellido as apellido,
                    CONCAT(u.us_nombre, ' ', u.us_apellido) as nombre_completo,
                    u.us_email as email,
                    u.us_rol as rol,
                    u.us_foto_perfil as foto_perfil,
                    CASE 
                        WHEN ap.id IS NOT NULL THEN 'asignado'
                        ELSE 'disponible'
                    END as estado_asignacion,
                    ap.rol_proyecto as rol_actual
                FROM usuarios u
                LEFT JOIN asignaciones_proyectos ap ON (u.id = ap.usuario_id AND ap.proyecto_id = :proyecto_id)
                WHERE u.us_activo = 1
                ORDER BY 
                    CASE WHEN ap.id IS NOT NULL THEN 0 ELSE 1 END,
                    u.us_nombre ASC, u.us_apellido ASC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':proyecto_id', $proyecto_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Separar usuarios disponibles de asignados
        $disponibles = [];
        $asignados = [];
        
        foreach ($usuarios as $usuario) {
            if ($usuario['estado_asignacion'] === 'disponible') {
                $disponibles[] = $usuario;
            } else {
                $asignados[] = $usuario;
            }
        }
        
        echo json_encode([
            'exito' => true,
            'usuarios' => $usuarios,
            'disponibles' => $disponibles,
            'asignados' => $asignados,
            'total' => count($usuarios),
            'total_disponibles' => count($disponibles),
            'total_asignados' => count($asignados)
        ]);
        
    } catch (PDOException $e) {
        error_log("Error al listar usuarios disponibles: " . $e->getMessage());
        throw new Exception('Error al obtener usuarios disponibles: ' . $e->getMessage());
    }
}

?>