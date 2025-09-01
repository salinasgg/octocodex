<?php
/**
 * ================================================
 * LISTAR PROYECTOS PARA ASIGNACIONES
 * ================================================
 * Archivo específico para listar proyectos disponibles para asignaciones
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
            listarProyectos($pdo);
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
 * Listar proyectos activos disponibles para asignaciones
 */
function listarProyectos($pdo) {
    try {
        $sql = "SELECT 
                    id,
                    pr_titulo as titulo,
                    pr_descripcion as descripcion,
                    pr_estado as estado,
                    pr_prioridad as prioridad,
                    pr_fecha_inicio as fecha_inicio
                FROM proyectos 
                WHERE pr_activo = 1 
                ORDER BY pr_fecha_inicio DESC, pr_titulo ASC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        
        $proyectos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'exito' => true,
            'proyectos' => $proyectos,
            'total' => count($proyectos)
        ]);
        
    } catch (PDOException $e) {
        error_log("Error al listar proyectos: " . $e->getMessage());
        throw new Exception('Error al obtener proyectos: ' . $e->getMessage());
    }
}

?>