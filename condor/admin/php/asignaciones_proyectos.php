<?php
/**
 * ================================================
 * SISTEMA DE ASIGNACIONES DE PROYECTOS
 * ================================================
 * API PHP para gestionar asignaciones de usuarios a proyectos
 * Soporta operaciones CRUD y consultas especializadas
 */

require_once '../../php/config_bd.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Obtener conexión a la base de datos
try {
    $database = Database::getInstance();
    $pdo = $database->getConnection();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Error de conexión a la base de datos: ' . $e->getMessage()
    ]);
    exit;
}

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
$accion = $_REQUEST['accion'] ?? $_POST['accion'] ?? '';

// Log de debug
error_log("Asignaciones API - Método: $method, Acción: $accion");
error_log("POST data: " . print_r($_POST, true));
error_log("REQUEST data: " . print_r($_REQUEST, true));

/**
 * Procesar la acción solicitada
 */
function procesarAccion() {
    global $accion;
    
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
            
        case 'obtener_roles_proyecto':
            obtenerRolesProyecto();
            break;
            
        case 'verificar_asignacion_existente':
            verificarAsignacionExistente();
            break;
            
        case 'listar_proyectos':
            listarProyectos();
            break;
            
        case 'listar_usuarios':
            listarUsuarios();
            break;
            
        case 'obtener_estadisticas_usuario':
            obtenerEstadisticasUsuario();
            break;
            
        default:
            throw new Exception('Acción no válida: ' . $accion);
    }
}

// Ejecutar la acción
try {
    procesarAccion();
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
    
    // Usar consulta directa en lugar de vista por compatibilidad
    $sql = "SELECT 
                ap.id as asignacion_id,
                ap.proyecto_id,
                ap.usuario_id,
                ap.rol_proyecto,
                ap.estado_asignacion,
                ap.fecha_asignacion,
                ap.fecha_inicio,
                ap.fecha_fin,
                ap.horas_asignadas,
                ap.horas_trabajadas,
                ap.notas,
                p.pr_titulo as proyecto_titulo,
                p.pr_estado as proyecto_estado,
                p.pr_prioridad as proyecto_prioridad,
                u.us_nombre as usuario_nombre,
                u.us_apellido as usuario_apellido,
                u.us_email as usuario_email,
                CONCAT(u.us_nombre, ' ', u.us_apellido) as nombre_completo
            FROM asignaciones_proyectos ap
            INNER JOIN proyectos p ON ap.proyecto_id = p.id
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
    
    // Log de debug para ver qué datos llegan
    error_log("=== DEBUG ASIGNACIÓN ===");
    error_log("POST data: " . print_r($_POST, true));
    
    $proyecto_id = $_POST['proyecto_id'] ?? null;
    $usuario_id = $_POST['usuario_id'] ?? null;
    $rol_proyecto = $_POST['rol_proyecto'] ?? 'colaborador';
    $horas_asignadas = $_POST['horas_asignadas'] ?? null;
    $fecha_inicio = $_POST['fecha_inicio'] ?? null;
    $notas = $_POST['notas'] ?? null;
    
    error_log("Proyecto ID: $proyecto_id");
    error_log("Usuario ID: $usuario_id");
    error_log("Rol: $rol_proyecto");
    error_log("Horas: $horas_asignadas");
    error_log("Fecha inicio: $fecha_inicio");
    error_log("Notas: $notas");
    
    // Validaciones
    if (!$proyecto_id || !$usuario_id) {
        error_log("Error: Proyecto y usuario son requeridos");
        throw new Exception('Proyecto y usuario son requeridos');
    }
    
    // Verificar que el proyecto existe
    $proyecto_sql = "SELECT id FROM proyectos WHERE id = :proyecto_id";
    $proyecto_stmt = $pdo->prepare($proyecto_sql);
    $proyecto_stmt->bindParam(':proyecto_id', $proyecto_id, PDO::PARAM_INT);
    $proyecto_stmt->execute();
    
    if (!$proyecto_stmt->fetch()) {
        error_log("Error: Proyecto con ID $proyecto_id no existe");
        throw new Exception('El proyecto especificado no existe');
    }
    
    // Verificar que el usuario existe y está activo
    $usuario_sql = "SELECT id FROM usuarios WHERE id = :usuario_id AND us_activo = 1";
    $usuario_stmt = $pdo->prepare($usuario_sql);
    $usuario_stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $usuario_stmt->execute();
    
    if (!$usuario_stmt->fetch()) {
        error_log("Error: Usuario con ID $usuario_id no existe o no está activo");
        throw new Exception('El usuario especificado no existe o no está activo');
    }
    
    // Verificar si ya existe la asignación
    $check_sql = "SELECT id FROM asignaciones_proyectos 
                  WHERE proyecto_id = :proyecto_id AND usuario_id = :usuario_id";
    $check_stmt = $pdo->prepare($check_sql);
    $check_stmt->bindParam(':proyecto_id', $proyecto_id, PDO::PARAM_INT);
    $check_stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $check_stmt->execute();
    
    if ($check_stmt->fetch()) {
        error_log("Error: Usuario ya asignado al proyecto");
        throw new Exception('El usuario ya está asignado a este proyecto');
    }
    
    // Insertar nueva asignación
    $sql = "INSERT INTO asignaciones_proyectos 
            (proyecto_id, usuario_id, rol_proyecto, horas_asignadas, fecha_inicio, notas, estado_asignacion, fecha_asignacion) 
            VALUES (:proyecto_id, :usuario_id, :rol_proyecto, :horas_asignadas, :fecha_inicio, :notas, 'activo', NOW())";
    
    error_log("SQL a ejecutar: $sql");
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':proyecto_id', $proyecto_id, PDO::PARAM_INT);
    $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmt->bindParam(':rol_proyecto', $rol_proyecto);
    $stmt->bindParam(':horas_asignadas', $horas_asignadas);
    $stmt->bindParam(':fecha_inicio', $fecha_inicio);
    $stmt->bindParam(':notas', $notas);
    
    try {
        if ($stmt->execute()) {
            $asignacion_id = $pdo->lastInsertId();
            error_log("Asignación creada con ID: $asignacion_id");
            
            // Obtener detalles de la asignación creada
            try {
                $detail_sql = "SELECT 
                    ap.id as asignacion_id,
                    ap.proyecto_id,
                    ap.usuario_id,
                    ap.rol_proyecto,
                    ap.estado_asignacion,
                    ap.fecha_asignacion,
                    ap.fecha_inicio,
                    ap.fecha_fin,
                    ap.horas_asignadas,
                    ap.horas_trabajadas,
                    ap.notas,
                    p.pr_titulo as proyecto_titulo,
                    u.us_nombre as usuario_nombre,
                    u.us_apellido as usuario_apellido,
                    CONCAT(u.us_nombre, ' ', u.us_apellido) as nombre_completo
                FROM asignaciones_proyectos ap
                INNER JOIN proyectos p ON ap.proyecto_id = p.id
                INNER JOIN usuarios u ON ap.usuario_id = u.id
                WHERE ap.id = :id";
                $detail_stmt = $pdo->prepare($detail_sql);
                $detail_stmt->bindParam(':id', $asignacion_id, PDO::PARAM_INT);
                $detail_stmt->execute();
                $asignacion = $detail_stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$asignacion) {
                    // Si la vista falla, obtener datos básicos de la tabla
                    $detail_sql = "SELECT ap.*, p.pr_titulo, u.us_nombre, u.us_apellido 
                                   FROM asignaciones_proyectos ap 
                                   INNER JOIN proyectos p ON ap.proyecto_id = p.id 
                                   INNER JOIN usuarios u ON ap.usuario_id = u.id 
                                   WHERE ap.id = :id";
                    $detail_stmt = $pdo->prepare($detail_sql);
                    $detail_stmt->bindParam(':id', $asignacion_id, PDO::PARAM_INT);
                    $detail_stmt->execute();
                    $asignacion = $detail_stmt->fetch(PDO::FETCH_ASSOC);
                }
            } catch (Exception $e) {
                error_log("Error al obtener detalles de asignación: " . $e->getMessage());
                // Continuar sin los detalles completos
                $asignacion = null;
            }
            
            error_log("Asignación exitosa");
            echo json_encode([
                'exito' => true,
                'mensaje' => 'Usuario asignado exitosamente al proyecto',
                'asignacion_id' => $asignacion_id,
                'asignacion' => $asignacion
            ]);
        } else {
            error_log("Error en execute(): " . print_r($stmt->errorInfo(), true));
            throw new Exception('Error al asignar usuario al proyecto');
        }
    } catch (PDOException $e) {
        error_log("PDO Exception: " . $e->getMessage());
        throw new Exception('Error de base de datos: ' . $e->getMessage());
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
    
    // Debug log
    error_log("=== ELIMINAR ASIGNACIÓN ===");
    error_log("POST data: " . print_r($_POST, true));
    
    $asignacion_id = $_POST['asignacion_id'] ?? null;
    
    error_log("Asignacion ID recibido: " . $asignacion_id);
    
    if (!$asignacion_id) {
        error_log("ERROR: ID de asignación requerido");
        throw new Exception('ID de asignación requerido');
    }
    
    // Verificar que la asignación existe antes de eliminar
    $check_sql = "SELECT id, usuario_id, proyecto_id FROM asignaciones_proyectos WHERE id = :asignacion_id";
    $check_stmt = $pdo->prepare($check_sql);
    $check_stmt->bindParam(':asignacion_id', $asignacion_id, PDO::PARAM_INT);
    $check_stmt->execute();
    
    $asignacion = $check_stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$asignacion) {
        error_log("ERROR: Asignación con ID $asignacion_id no encontrada");
        throw new Exception('Asignación no encontrada');
    }
    
    error_log("Asignación encontrada: " . print_r($asignacion, true));
    
    // Proceder con la eliminación
    $sql = "DELETE FROM asignaciones_proyectos WHERE id = :asignacion_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':asignacion_id', $asignacion_id, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        $rowsAffected = $stmt->rowCount();
        error_log("Filas afectadas: " . $rowsAffected);
        
        if ($rowsAffected > 0) {
            error_log("✅ Asignación eliminada exitosamente");
            echo json_encode([
                'exito' => true,
                'mensaje' => 'Asignación eliminada exitosamente',
                'eliminada' => $asignacion,
                'filas_afectadas' => $rowsAffected
            ]);
        } else {
            error_log("⚠️ No se eliminó ninguna fila");
            throw new Exception('No se pudo eliminar la asignación');
        }
    } else {
        error_log("ERROR en execute(): " . print_r($stmt->errorInfo(), true));
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
                p.pr_fecha_estimada,
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
            GROUP BY p.id, p.pr_titulo, p.pr_descripcion, p.pr_estado, p.pr_prioridad, p.pr_fecha_inicio, p.pr_fecha_estimada
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
    
    // Usar consulta directa en lugar de vista por compatibilidad
    $sql = "SELECT 
                ap.id as asignacion_id,
                ap.proyecto_id,
                ap.usuario_id,
                ap.rol_proyecto,
                ap.estado_asignacion,
                ap.fecha_asignacion,
                ap.fecha_inicio,
                ap.fecha_fin,
                ap.horas_asignadas,
                ap.horas_trabajadas,
                ap.notas,
                p.pr_titulo as proyecto_titulo,
                p.pr_estado as proyecto_estado,
                p.pr_prioridad as proyecto_prioridad,
                u.us_nombre as usuario_nombre,
                u.us_apellido as usuario_apellido,
                u.us_email as usuario_email,
                CONCAT(u.us_nombre, ' ', u.us_apellido) as nombre_completo
            FROM asignaciones_proyectos ap
            INNER JOIN proyectos p ON ap.proyecto_id = p.id
            INNER JOIN usuarios u ON ap.usuario_id = u.id
            WHERE ap.usuario_id = :usuario_id 
            ORDER BY ap.fecha_asignacion DESC";
    
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

/**
 * Obtener roles disponibles para un proyecto específico
 */
function obtenerRolesProyecto() {
    global $pdo;
    
    $proyecto_id = $_GET['proyecto_id'] ?? null;
    
    if (!$proyecto_id) {
        throw new Exception('ID de proyecto requerido');
    }
    
    // Verificar que el proyecto existe
    $proyecto_sql = "SELECT id, pr_titulo FROM proyectos WHERE id = :proyecto_id";
    $proyecto_stmt = $pdo->prepare($proyecto_sql);
    $proyecto_stmt->bindParam(':proyecto_id', $proyecto_id, PDO::PARAM_INT);
    $proyecto_stmt->execute();
    
    $proyecto = $proyecto_stmt->fetch(PDO::FETCH_ASSOC);
    if (!$proyecto) {
        throw new Exception('El proyecto especificado no existe');
    }
    
    // Obtener roles actuales asignados al proyecto
    $roles_sql = "SELECT 
                    ap.rol_proyecto,
                    COUNT(*) as cantidad,
                    GROUP_CONCAT(CONCAT(u.us_nombre, ' ', u.us_apellido) SEPARATOR ', ') as usuarios_asignados
                  FROM asignaciones_proyectos ap
                  INNER JOIN usuarios u ON ap.usuario_id = u.id
                  WHERE ap.proyecto_id = :proyecto_id AND ap.estado_asignacion = 'activo'
                  GROUP BY ap.rol_proyecto
                  ORDER BY 
                    CASE ap.rol_proyecto 
                        WHEN 'lider' THEN 1 
                        WHEN 'desarrollador' THEN 2 
                        WHEN 'consultor' THEN 3 
                        WHEN 'revisor' THEN 4 
                        ELSE 5 
                    END";
    
    $roles_stmt = $pdo->prepare($roles_sql);
    $roles_stmt->bindParam(':proyecto_id', $proyecto_id, PDO::PARAM_INT);
    $roles_stmt->execute();
    
    $roles_asignados = $roles_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Definir todos los roles disponibles
    $roles_disponibles = [
        'lider' => 'Líder de Proyecto',
        'desarrollador' => 'Desarrollador',
        'consultor' => 'Consultor',
        'revisor' => 'Revisor',
        'colaborador' => 'Colaborador'
    ];
    
    // Crear respuesta con información del proyecto y roles
    $respuesta = [
        'proyecto' => [
            'id' => $proyecto['id'],
            'titulo' => $proyecto['pr_titulo']
        ],
        'roles_disponibles' => $roles_disponibles,
        'roles_asignados' => $roles_asignados,
        'total_asignaciones' => array_sum(array_column($roles_asignados, 'cantidad'))
    ];
    
    echo json_encode([
        'exito' => true,
        'datos' => $respuesta
    ]);
}

/**
 * Verificar si ya existe una asignación para un usuario en un proyecto específico
 */
function verificarAsignacionExistente() {
    global $pdo;
    
    $usuario_id = $_GET['usuario_id'] ?? null;
    $proyecto_id = $_GET['proyecto_id'] ?? null;
    
    if (!$usuario_id || !$proyecto_id) {
        throw new Exception('ID de usuario y proyecto son requeridos');
    }
    
    // Verificar que el usuario existe y está activo
    $usuario_sql = "SELECT id, us_nombre, us_apellido FROM usuarios WHERE id = :usuario_id AND us_activo = 1";
    $usuario_stmt = $pdo->prepare($usuario_sql);
    $usuario_stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $usuario_stmt->execute();
    
    $usuario = $usuario_stmt->fetch(PDO::FETCH_ASSOC);
    if (!$usuario) {
        throw new Exception('El usuario especificado no existe o no está activo');
    }
    
    // Verificar que el proyecto existe
    $proyecto_sql = "SELECT id, pr_titulo FROM proyectos WHERE id = :proyecto_id";
    $proyecto_stmt = $pdo->prepare($proyecto_sql);
    $proyecto_stmt->bindParam(':proyecto_id', $proyecto_id, PDO::PARAM_INT);
    $proyecto_stmt->execute();
    
    $proyecto = $proyecto_stmt->fetch(PDO::FETCH_ASSOC);
    if (!$proyecto) {
        throw new Exception('El proyecto especificado no existe');
    }
    
    // Verificar si ya existe una asignación
    $asignacion_sql = "SELECT 
                        ap.id as asignacion_id,
                        ap.rol_proyecto,
                        ap.estado_asignacion,
                        ap.fecha_asignacion,
                        ap.horas_asignadas,
                        ap.horas_trabajadas,
                        ap.fecha_inicio,
                        ap.fecha_fin,
                        ap.notas
                       FROM asignaciones_proyectos ap
                       WHERE ap.proyecto_id = :proyecto_id AND ap.usuario_id = :usuario_id";
    
    $asignacion_stmt = $pdo->prepare($asignacion_sql);
    $asignacion_stmt->bindParam(':proyecto_id', $proyecto_id, PDO::PARAM_INT);
    $asignacion_stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $asignacion_stmt->execute();
    
    $asignacion_existente = $asignacion_stmt->fetch(PDO::FETCH_ASSOC);
    
    // Crear respuesta
    $respuesta = [
        'existe' => $asignacion_existente !== false,
        'usuario' => [
            'id' => $usuario['id'],
            'nombre' => $usuario['us_nombre'] . ' ' . $usuario['us_apellido']
        ],
        'proyecto' => [
            'id' => $proyecto['id'],
            'titulo' => $proyecto['pr_titulo']
        ]
    ];
    
    if ($asignacion_existente) {
        $respuesta['asignacion'] = $asignacion_existente;
        $respuesta['mensaje'] = 'El usuario ya está asignado a este proyecto';
    } else {
        $respuesta['mensaje'] = 'El usuario no está asignado a este proyecto';
    }
    
    echo json_encode([
        'exito' => true,
        'datos' => $respuesta
    ]);
}

/**
 * Listar todos los proyectos
 */
function listarProyectos() {
    global $pdo;
    
    $sql = "SELECT 
                id,
                pr_titulo,
                pr_descripcion,
                pr_estado,
                pr_prioridad,
                pr_fecha_inicio,
                pr_fecha_estimada
            FROM proyectos 
            ORDER BY pr_fecha_inicio DESC, pr_titulo";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    
    $proyectos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'exito' => true,
        'proyectos' => $proyectos,
        'total' => count($proyectos)
    ]);
}

/**
 * Listar todos los usuarios activos
 */
function listarUsuarios() {
    global $pdo;
    
    $sql = "SELECT 
                id,
                us_nombre,
                us_apellido,
                us_email,
                us_rol,
                us_foto_perfil,
                CONCAT(us_nombre, ' ', us_apellido) as nombre_completo
            FROM usuarios 
            WHERE us_activo = 1
            ORDER BY us_nombre, us_apellido";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'exito' => true,
        'usuarios' => $usuarios,
        'total' => count($usuarios)
    ]);
}

/**
 * ================================================
 * FUNCIÓN: obtenerEstadisticasUsuario
 * ================================================
 * Obtiene estadísticas de asignaciones para un usuario específico
 * Incluye: total proyectos asignados, horas totales, roles diferentes
 */
function obtenerEstadisticasUsuario() {
    global $pdo;
    
    try {
        // Validar parámetros
        $usuario_id = $_GET['usuario_id'] ?? $_POST['usuario_id'] ?? null;
        
        if (!$usuario_id) {
            throw new Exception('ID de usuario requerido');
        }
        
        if (!is_numeric($usuario_id)) {
            throw new Exception('ID de usuario inválido');
        }
        
        error_log("Obteniendo estadísticas para usuario ID: " . $usuario_id);
        
        // Consulta para obtener estadísticas del usuario
        $sql = "SELECT 
                    COUNT(DISTINCT ap.proyecto_id) as total_proyectos,
                    COALESCE(SUM(ap.horas_asignadas), 0) as total_horas,
                    COUNT(DISTINCT ap.rol_proyecto) as roles_diferentes,
                    COUNT(CASE WHEN ap.estado_asignacion = 'activo' THEN 1 END) as asignaciones_activas,
                    COUNT(CASE WHEN ap.estado_asignacion = 'completado' THEN 1 END) as asignaciones_completadas,
                    GROUP_CONCAT(DISTINCT ap.rol_proyecto ORDER BY ap.rol_proyecto) as roles_lista
                FROM asignaciones_proyectos ap
                WHERE ap.usuario_id = :usuario_id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':usuario_id' => $usuario_id]);
        $estadisticas = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Si no hay asignaciones, devolver valores en cero
        if (!$estadisticas || $estadisticas['total_proyectos'] == 0) {
            $estadisticas = [
                'total_proyectos' => 0,
                'total_horas' => 0,
                'roles_diferentes' => 0,
                'asignaciones_activas' => 0,
                'asignaciones_completadas' => 0,
                'roles_lista' => null
            ];
        }
        
        // Obtener información adicional de proyectos activos
        $sql_proyectos = "SELECT 
                            p.pr_titulo,
                            ap.rol_proyecto,
                            ap.horas_asignadas,
                            ap.estado_asignacion,
                            ap.fecha_asignacion
                        FROM asignaciones_proyectos ap
                        JOIN proyectos p ON ap.proyecto_id = p.id
                        WHERE ap.usuario_id = :usuario_id
                        AND ap.estado_asignacion = 'activo'
                        ORDER BY ap.fecha_asignacion DESC";
        
        $stmt_proyectos = $pdo->prepare($sql_proyectos);
        $stmt_proyectos->execute([':usuario_id' => $usuario_id]);
        $proyectos_activos = $stmt_proyectos->fetchAll(PDO::FETCH_ASSOC);
        
        error_log("Estadísticas obtenidas: " . json_encode($estadisticas));
        
        echo json_encode([
            'exito' => true,
            'estadisticas' => $estadisticas,
            'proyectos_activos' => $proyectos_activos,
            'mensaje' => 'Estadísticas obtenidas correctamente'
        ]);
        
    } catch (Exception $e) {
        error_log("Error en obtenerEstadisticasUsuario: " . $e->getMessage());
        echo json_encode([
            'exito' => false,
            'mensaje' => 'Error al obtener estadísticas: ' . $e->getMessage(),
            'estadisticas' => [
                'total_proyectos' => 0,
                'total_horas' => 0,
                'roles_diferentes' => 0,
                'asignaciones_activas' => 0,
                'asignaciones_completadas' => 0,
                'roles_lista' => null
            ]
        ]);
    }
}

?>