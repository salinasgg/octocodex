<?php
/**
 * ================================================
 * SISTEMA DE ASIGNACIONES DE PROYECTOS
 * ================================================
 * API PHP para gestionar asignaciones de usuarios a proyectos
 * Soporta operaciones CRUD y consultas especializadas
 */

require_once 'config_bd.php';
session_start();

// Verificar autenticación
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(401);
    echo json_encode([
        'exito' => false, 
        'mensaje' => 'No autorizado. Inicia sesión para continuar.'
    ]);
    exit;
}

// Configurar headers para API
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Obtener método y acción
$method = $_SERVER['REQUEST_METHOD'];
$accion = $_REQUEST['accion'] ?? '';

// Log de debug
error_log("Asignaciones API - Método: $method, Acción: $accion");

try {
    switch ($accion) {
        case 'listar_usuarios_disponibles':
            listarUsuariosDisponibles();
            break;
            
        case 'obtener_asignaciones':
            obtenerAsignacionesProyecto();
            break;
            
        case 'asignar_usuario':
            asignarUsuarioProyecto();
            break;
            
        case 'actualizar_asignacion':
            actualizarAsignacion();
            break;
            
        case 'eliminar_asignacion':
            eliminarAsignacion();
            break;
            
        case 'obtener_estadisticas':
            obtenerEstadisticasAsignaciones();
            break;
            
        case 'listar_proyectos_con_asignaciones':
            listarProyectosConAsignaciones();
            break;
            
        case 'listar_usuarios_con_asignaciones':
            listarUsuariosConAsignaciones();
            break;
            
        case 'obtener_asignaciones_usuario':
            obtenerAsignacionesUsuario();
            break;
            
        default:
            throw new Exception('Acción no válida: ' . $accion);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Error del servidor: ' . $e->getMessage(),
        'error_code' => $e->getCode()
    ]);
}

/**
 * Listar usuarios disponibles para asignar a proyectos
 */
function listarUsuariosDisponibles() {
    global $pdo;
    
    $proyecto_id = $_GET['proyecto_id'] ?? null;
    
    $sql = "SELECT 
                u.id,
                u.us_nombre,
                u.us_apellido,
                u.us_email,
                u.us_rol,
                u.us_foto_perfil,
                CONCAT(u.us_nombre, ' ', u.us_apellido) as nombre_completo,
                CASE 
                    WHEN ap.id IS NOT NULL THEN 'asignado'
                    ELSE 'disponible'
                END as estado_asignacion,
                ap.rol_proyecto,
                ap.estado_asignacion as estado_actual
            FROM usuarios u
            LEFT JOIN asignaciones_proyectos ap ON (u.id = ap.usuario_id AND ap.proyecto_id = :proyecto_id)
            WHERE u.us_activo = 1
            ORDER BY 
                CASE WHEN ap.id IS NOT NULL THEN 0 ELSE 1 END,
                u.us_nombre, u.us_apellido";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':proyecto_id', $proyecto_id, PDO::PARAM_INT);
    $stmt->execute();
    
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'exito' => true,
        'usuarios' => $usuarios,
        'total' => count($usuarios)
    ]);
}

/**
 * Obtener asignaciones de un proyecto específico
 */
function obtenerAsignacionesProyecto() {
    global $pdo;
    
    $proyecto_id = $_GET['proyecto_id'] ?? null;
    
    if (!$proyecto_id) {
        throw new Exception('ID de proyecto requerido');
    }
    
    $sql = "SELECT * FROM vista_asignaciones_detalle 
            WHERE proyecto_id = :proyecto_id 
            ORDER BY 
                CASE rol_proyecto 
                    WHEN 'lider' THEN 1 
                    WHEN 'desarrollador' THEN 2 
                    WHEN 'consultor' THEN 3 
                    WHEN 'revisor' THEN 4 
                    ELSE 5 
                END,
                fecha_asignacion DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':proyecto_id', $proyecto_id, PDO::PARAM_INT);
    $stmt->execute();
    
    $asignaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Obtener estadísticas adicionales
    $sql_stats = "SELECT 
                    COUNT(*) as total_asignados,
                    SUM(horas_asignadas) as total_horas_asignadas,
                    SUM(horas_trabajadas) as total_horas_trabajadas,
                    COUNT(CASE WHEN estado_asignacion = 'activo' THEN 1 END) as asignados_activos
                  FROM asignaciones_proyectos 
                  WHERE proyecto_id = :proyecto_id";
    
    $stmt_stats = $pdo->prepare($sql_stats);
    $stmt_stats->bindParam(':proyecto_id', $proyecto_id, PDO::PARAM_INT);
    $stmt_stats->execute();
    
    $estadisticas = $stmt_stats->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'exito' => true,
        'asignaciones' => $asignaciones,
        'estadisticas' => $estadisticas
    ]);
}

/**
 * Asignar usuario a proyecto
 */
function asignarUsuarioProyecto() {
    global $pdo;
    
    $proyecto_id = $_POST['proyecto_id'] ?? null;
    $usuario_id = $_POST['usuario_id'] ?? null;
    $rol_proyecto = $_POST['rol_proyecto'] ?? 'colaborador';
    $horas_asignadas = $_POST['horas_asignadas'] ?? null;
    $fecha_inicio = $_POST['fecha_inicio'] ?? null;
    $notas = $_POST['notas'] ?? null;
    
    // Validaciones
    if (!$proyecto_id || !$usuario_id) {
        throw new Exception('Proyecto y usuario son requeridos');
    }
    
    // Verificar si ya existe la asignación
    $check_sql = "SELECT id FROM asignaciones_proyectos 
                  WHERE proyecto_id = :proyecto_id AND usuario_id = :usuario_id";
    $check_stmt = $pdo->prepare($check_sql);
    $check_stmt->bindParam(':proyecto_id', $proyecto_id, PDO::PARAM_INT);
    $check_stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $check_stmt->execute();
    
    if ($check_stmt->fetch()) {
        throw new Exception('El usuario ya está asignado a este proyecto');
    }
    
    // Insertar nueva asignación
    $sql = "INSERT INTO asignaciones_proyectos 
            (proyecto_id, usuario_id, rol_proyecto, horas_asignadas, fecha_inicio, notas) 
            VALUES (:proyecto_id, :usuario_id, :rol_proyecto, :horas_asignadas, :fecha_inicio, :notas)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':proyecto_id', $proyecto_id, PDO::PARAM_INT);
    $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmt->bindParam(':rol_proyecto', $rol_proyecto);
    $stmt->bindParam(':horas_asignadas', $horas_asignadas);
    $stmt->bindParam(':fecha_inicio', $fecha_inicio);
    $stmt->bindParam(':notas', $notas);
    
    if ($stmt->execute()) {
        $asignacion_id = $pdo->lastInsertId();
        
        // Obtener detalles de la asignación creada
        $detail_sql = "SELECT * FROM vista_asignaciones_detalle WHERE asignacion_id = :id";
        $detail_stmt = $pdo->prepare($detail_sql);
        $detail_stmt->bindParam(':id', $asignacion_id, PDO::PARAM_INT);
        $detail_stmt->execute();
        $asignacion = $detail_stmt->fetch(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'exito' => true,
            'mensaje' => 'Usuario asignado exitosamente al proyecto',
            'asignacion_id' => $asignacion_id,
            'asignacion' => $asignacion
        ]);
    } else {
        throw new Exception('Error al asignar usuario al proyecto');
    }
}

/**
 * Actualizar asignación existente
 */
function actualizarAsignacion() {
    global $pdo;
    
    $asignacion_id = $_POST['asignacion_id'] ?? null;
    $rol_proyecto = $_POST['rol_proyecto'] ?? null;
    $horas_asignadas = $_POST['horas_asignadas'] ?? null;
    $horas_trabajadas = $_POST['horas_trabajadas'] ?? null;
    $estado_asignacion = $_POST['estado_asignacion'] ?? null;
    $fecha_inicio = $_POST['fecha_inicio'] ?? null;
    $fecha_fin = $_POST['fecha_fin'] ?? null;
    $notas = $_POST['notas'] ?? null;
    
    if (!$asignacion_id) {
        throw new Exception('ID de asignación requerido');
    }
    
    $sql = "UPDATE asignaciones_proyectos SET 
            rol_proyecto = COALESCE(:rol_proyecto, rol_proyecto),
            horas_asignadas = COALESCE(:horas_asignadas, horas_asignadas),
            horas_trabajadas = COALESCE(:horas_trabajadas, horas_trabajadas),
            estado_asignacion = COALESCE(:estado_asignacion, estado_asignacion),
            fecha_inicio = COALESCE(:fecha_inicio, fecha_inicio),
            fecha_fin = COALESCE(:fecha_fin, fecha_fin),
            notas = COALESCE(:notas, notas),
            updated_at = CURRENT_TIMESTAMP
            WHERE id = :asignacion_id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':asignacion_id', $asignacion_id, PDO::PARAM_INT);
    $stmt->bindParam(':rol_proyecto', $rol_proyecto);
    $stmt->bindParam(':horas_asignadas', $horas_asignadas);
    $stmt->bindParam(':horas_trabajadas', $horas_trabajadas);
    $stmt->bindParam(':estado_asignacion', $estado_asignacion);
    $stmt->bindParam(':fecha_inicio', $fecha_inicio);
    $stmt->bindParam(':fecha_fin', $fecha_fin);
    $stmt->bindParam(':notas', $notas);
    
    if ($stmt->execute()) {
        echo json_encode([
            'exito' => true,
            'mensaje' => 'Asignación actualizada exitosamente'
        ]);
    } else {
        throw new Exception('Error al actualizar la asignación');
    }
}

/**
 * Eliminar asignación
 */
function eliminarAsignacion() {
    global $pdo;
    
    $asignacion_id = $_POST['asignacion_id'] ?? null;
    
    if (!$asignacion_id) {
        throw new Exception('ID de asignación requerido');
    }
    
    $sql = "DELETE FROM asignaciones_proyectos WHERE id = :asignacion_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':asignacion_id', $asignacion_id, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        echo json_encode([
            'exito' => true,
            'mensaje' => 'Asignación eliminada exitosamente'
        ]);
    } else {
        throw new Exception('Error al eliminar la asignación');
    }
}

/**
 * Obtener estadísticas generales de asignaciones
 */
function obtenerEstadisticasAsignaciones() {
    global $pdo;
    
    $sql = "SELECT 
                COUNT(DISTINCT ap.proyecto_id) as proyectos_con_asignaciones,
                COUNT(DISTINCT ap.usuario_id) as usuarios_asignados,
                COUNT(*) as total_asignaciones,
                AVG(ap.horas_asignadas) as promedio_horas_asignadas,
                SUM(ap.horas_trabajadas) as total_horas_trabajadas,
                COUNT(CASE WHEN ap.estado_asignacion = 'activo' THEN 1 END) as asignaciones_activas,
                COUNT(CASE WHEN ap.rol_proyecto = 'lider' THEN 1 END) as lideres_proyecto,
                COUNT(CASE WHEN ap.rol_proyecto = 'desarrollador' THEN 1 END) as desarrolladores,
                COUNT(CASE WHEN ap.rol_proyecto = 'consultor' THEN 1 END) as consultores
            FROM asignaciones_proyectos ap";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    
    $estadisticas = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'exito' => true,
        'estadisticas' => $estadisticas
    ]);
}

/**
 * Listar proyectos con sus asignaciones
 */
function listarProyectosConAsignaciones() {
    global $pdo;
    
    $sql = "SELECT 
                p.id as proyecto_id,
                p.pr_titulo as proyecto_titulo,
                p.pr_descripcion,
                p.pr_estado as proyecto_estado,
                p.pr_prioridad as proyecto_prioridad,
                p.pr_fecha_inicio,
                p.pr_fecha_fin_estimada,
                COUNT(ap.id) as total_asignados,
                SUM(ap.horas_asignadas) as total_horas_asignadas,
                SUM(ap.horas_trabajadas) as total_horas_trabajadas,
                COUNT(CASE WHEN ap.estado_asignacion = 'activo' THEN 1 END) as asignados_activos,
                GROUP_CONCAT(
                    DISTINCT CONCAT(u.us_nombre, ' ', u.us_apellido) 
                    ORDER BY ap.rol_proyecto, u.us_nombre 
                    SEPARATOR ', '
                ) as usuarios_asignados,
                GROUP_CONCAT(
                    DISTINCT ap.rol_proyecto 
                    ORDER BY 
                        CASE ap.rol_proyecto 
                            WHEN 'lider' THEN 1 
                            WHEN 'desarrollador' THEN 2 
                            WHEN 'consultor' THEN 3 
                            WHEN 'revisor' THEN 4 
                            ELSE 5 
                        END
                    SEPARATOR ', '
                ) as roles_proyecto
            FROM proyectos p
            LEFT JOIN asignaciones_proyectos ap ON p.id = ap.proyecto_id
            LEFT JOIN usuarios u ON ap.usuario_id = u.id
            GROUP BY p.id, p.pr_titulo, p.pr_descripcion, p.pr_estado, p.pr_prioridad, p.pr_fecha_inicio, p.pr_fecha_fin_estimada
            HAVING total_asignados > 0
            ORDER BY p.pr_fecha_inicio DESC, p.pr_titulo";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    
    $proyectos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Obtener detalles de asignaciones para cada proyecto
    foreach ($proyectos as &$proyecto) {
        $detalle_sql = "SELECT 
                            ap.id as asignacion_id,
                            ap.rol_proyecto,
                            ap.estado_asignacion,
                            ap.horas_asignadas,
                            ap.horas_trabajadas,
                            ap.fecha_asignacion,
                            u.id as usuario_id,
                            CONCAT(u.us_nombre, ' ', u.us_apellido) as nombre_completo,
                            u.us_email,
                            u.us_foto_perfil
                        FROM asignaciones_proyectos ap
                        INNER JOIN usuarios u ON ap.usuario_id = u.id
                        WHERE ap.proyecto_id = :proyecto_id
                        ORDER BY 
                            CASE ap.rol_proyecto 
                                WHEN 'lider' THEN 1 
                                WHEN 'desarrollador' THEN 2 
                                WHEN 'consultor' THEN 3 
                                WHEN 'revisor' THEN 4 
                                ELSE 5 
                            END,
                            ap.fecha_asignacion DESC";
        
        $detalle_stmt = $pdo->prepare($detalle_sql);
        $detalle_stmt->bindParam(':proyecto_id', $proyecto['proyecto_id'], PDO::PARAM_INT);
        $detalle_stmt->execute();
        $proyecto['asignaciones_detalle'] = $detalle_stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    echo json_encode([
        'exito' => true,
        'proyectos' => $proyectos,
        'total' => count($proyectos)
    ]);
}

/**
 * Listar usuarios con sus asignaciones
 */
function listarUsuariosConAsignaciones() {
    global $pdo;
    
    $sql = "SELECT 
                u.id as usuario_id,
                u.us_nombre,
                u.us_apellido,
                CONCAT(u.us_nombre, ' ', u.us_apellido) as nombre_completo,
                u.us_email,
                u.us_rol as rol_sistema,
                u.us_foto_perfil,
                u.us_activo,
                COUNT(ap.id) as total_proyectos_asignados,
                SUM(ap.horas_asignadas) as total_horas_asignadas,
                SUM(ap.horas_trabajadas) as total_horas_trabajadas,
                COUNT(CASE WHEN ap.estado_asignacion = 'activo' THEN 1 END) as asignaciones_activas,
                GROUP_CONCAT(
                    DISTINCT p.pr_titulo 
                    ORDER BY ap.fecha_asignacion DESC 
                    SEPARATOR ', '
                ) as proyectos_asignados,
                GROUP_CONCAT(
                    DISTINCT ap.rol_proyecto 
                    ORDER BY 
                        CASE ap.rol_proyecto 
                            WHEN 'lider' THEN 1 
                            WHEN 'desarrollador' THEN 2 
                            WHEN 'consultor' THEN 3 
                            WHEN 'revisor' THEN 4 
                            ELSE 5 
                        END
                    SEPARATOR ', '
                ) as roles_principales,
                MAX(ap.fecha_asignacion) as ultima_asignacion
            FROM usuarios u
            LEFT JOIN asignaciones_proyectos ap ON u.id = ap.usuario_id
            LEFT JOIN proyectos p ON ap.proyecto_id = p.id
            WHERE u.us_activo = 1
            GROUP BY u.id, u.us_nombre, u.us_apellido, u.us_email, u.us_rol, u.us_foto_perfil, u.us_activo
            HAVING total_proyectos_asignados > 0
            ORDER BY total_proyectos_asignados DESC, u.us_nombre, u.us_apellido";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Obtener detalles de asignaciones para cada usuario
    foreach ($usuarios as &$usuario) {
        $detalle_sql = "SELECT 
                            ap.id as asignacion_id,
                            ap.rol_proyecto,
                            ap.estado_asignacion,
                            ap.horas_asignadas,
                            ap.horas_trabajadas,
                            ap.fecha_asignacion,
                            ap.fecha_inicio,
                            ap.fecha_fin,
                            p.id as proyecto_id,
                            p.pr_titulo as proyecto_titulo,
                            p.pr_estado as proyecto_estado,
                            p.pr_prioridad as proyecto_prioridad
                        FROM asignaciones_proyectos ap
                        INNER JOIN proyectos p ON ap.proyecto_id = p.id
                        WHERE ap.usuario_id = :usuario_id
                        ORDER BY ap.fecha_asignacion DESC";
        
        $detalle_stmt = $pdo->prepare($detalle_sql);
        $detalle_stmt->bindParam(':usuario_id', $usuario['usuario_id'], PDO::PARAM_INT);
        $detalle_stmt->execute();
        $usuario['asignaciones_detalle'] = $detalle_stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    echo json_encode([
        'exito' => true,
        'usuarios' => $usuarios,
        'total' => count($usuarios)
    ]);
}

/**
 * Obtener asignaciones específicas de un usuario
 */
function obtenerAsignacionesUsuario() {
    global $pdo;
    
    $usuario_id = $_GET['usuario_id'] ?? null;
    
    if (!$usuario_id) {
        throw new Exception('ID de usuario requerido');
    }
    
    $sql = "SELECT * FROM vista_asignaciones_detalle 
            WHERE usuario_id = :usuario_id 
            ORDER BY fecha_asignacion DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmt->execute();
    
    $asignaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Obtener estadísticas adicionales del usuario
    $sql_stats = "SELECT 
                    COUNT(*) as total_asignaciones,
                    SUM(horas_asignadas) as total_horas_asignadas,
                    SUM(horas_trabajadas) as total_horas_trabajadas,
                    COUNT(CASE WHEN estado_asignacion = 'activo' THEN 1 END) as asignaciones_activas,
                    COUNT(DISTINCT proyecto_id) as proyectos_diferentes
                  FROM asignaciones_proyectos 
                  WHERE usuario_id = :usuario_id";
    
    $stmt_stats = $pdo->prepare($sql_stats);
    $stmt_stats->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmt_stats->execute();
    
    $estadisticas = $stmt_stats->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'exito' => true,
        'asignaciones' => $asignaciones,
        'estadisticas' => $estadisticas
    ]);
}

?>