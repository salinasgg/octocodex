<?php
// ===== ABM PROYECTOS - OPERACIONES CRUD =====
/**
 * Archivo para manejar todas las operaciones CRUD de proyectos
 * Crear, Leer, Actualizar, Eliminar proyectos
 */

// Configurar para evitar warnings en la salida JSON
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
ini_set('display_errors', 0);

// Incluir configuración de base de datos
require_once __DIR__ . '/config_bd.php';

// Iniciar sesión para verificar permisos
session_start();

error_log("🔄 Verificando sesión - logged_in: " . (isset($_SESSION['logged_in']) ? $_SESSION['logged_in'] : 'NO SET'));
error_log("🔄 User ID: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'NO SET'));

// Verificar que el usuario esté logueado
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    error_log("❌ Usuario no autorizado - redirigiendo");
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

// Configurar headers para JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

try {
    // Obtener conexión a la base de datos
    $database = Database::getInstance();
    $pdo = $database->getConnection();
    
    // Verificar que la conexión esté funcionando
    try {
        $test_query = "SELECT 1 as test";
        $test_stmt = $pdo->prepare($test_query);
        $test_stmt->execute();
        $test_result = $test_stmt->fetch();
        error_log("✅ Conexión a base de datos verificada correctamente");
    } catch (Exception $e) {
        error_log("❌ Error en la conexión a la base de datos: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['exito' => false, 'mensaje' => 'Error de conexión a la base de datos']);
        exit;
    }
    
    // Obtener método HTTP y acción
    $method = $_SERVER['REQUEST_METHOD'];
    $accion = $_REQUEST['accion'] ?? '';
    
    error_log("🔄 ABM Proyectos - Método: $method, Acción: $accion");
    error_log("🔄 POST data: " . print_r($_POST, true));
    error_log("🔄 GET data: " . print_r($_GET, true));
    
    switch ($accion) {
        case 'listar':
            listarProyectos($pdo);
            break;
            
        case 'obtener':
            obtenerProyecto($pdo);
            break;
            
        case 'crear':
            crearProyecto($pdo);
            break;
            
        case 'actualizar':
            actualizarProyecto($pdo);
            break;
            
        case 'eliminar':
            error_log("🔄 Ejecutando función eliminarProyecto");
            eliminarProyecto($pdo);
            break;
            
        case 'cambiar_estado':
            cambiarEstadoProyecto($pdo);
            break;
            
        case 'listar_clientes':
            listarClientes($pdo);
            break;
            
        case 'estadisticas':
            obtenerEstadisticas($pdo);
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Acción no válida']);
            break;
    }
    
} catch (Exception $e) {
    http_response_code(500);
    error_log("Error en ABM Proyectos: " . $e->getMessage());
    echo json_encode(['error' => 'Error interno del servidor: ' . $e->getMessage()]);
}

/**
 * Listar todos los proyectos con paginación y filtros
 */
function listarProyectos($pdo) {
    try {
        // Parámetros de paginación y filtros
        $pagina = (int)($_GET['pagina'] ?? 1);
        $limite = (int)($_GET['limite'] ?? 50);
        $buscar = $_GET['buscar'] ?? '';
        $estado = $_GET['estado'] ?? '';
        $cliente_id = $_GET['cliente_id'] ?? '';
        
        $offset = ($pagina - 1) * $limite;
        
        // Construir query base
        $sql = "SELECT p.*, 
                       c.cl_nombre, c.cl_apellido, c.cl_empresa,
                       u.us_nombre, u.us_apellido,
                       (SELECT COUNT(*) FROM tareas_proyecto tp WHERE tp.proyecto_id = p.id) as total_tareas,
                       (SELECT COUNT(*) FROM tareas_proyecto tp WHERE tp.proyecto_id = p.id AND tp.ta_estado = 'completada') as tareas_completadas
                FROM proyectos p 
                LEFT JOIN clientes c ON p.cliente_id = c.id 
                LEFT JOIN usuarios u ON p.usuario_id = u.id 
                WHERE p.pr_activo = 1";
        
        $params = [];
        
        // Agregar filtros
        if (!empty($buscar)) {
            $sql .= " AND (p.pr_titulo LIKE :buscar OR p.pr_descripcion LIKE :buscar OR c.cl_empresa LIKE :buscar)";
            $params[':buscar'] = "%$buscar%";
        }
        
        if (!empty($estado)) {
            $sql .= " AND p.pr_estado = :estado";
            $params[':estado'] = $estado;
        }
        
        if (!empty($cliente_id)) {
            $sql .= " AND p.cliente_id = :cliente_id";
            $params[':cliente_id'] = $cliente_id;
        }
        
        // Contar total de registros
        $countSql = str_replace("SELECT p.*, c.cl_nombre, c.cl_apellido, c.cl_empresa, u.us_nombre, u.us_apellido, (SELECT COUNT(*) FROM tareas_proyecto tp WHERE tp.proyecto_id = p.id) as total_tareas, (SELECT COUNT(*) FROM tareas_proyecto tp WHERE tp.proyecto_id = p.id AND tp.ta_estado = 'completada') as tareas_completadas", "SELECT COUNT(*)", $sql);
        $stmtCount = $pdo->prepare($countSql);
        $stmtCount->execute($params);
        $totalRegistros = $stmtCount->fetchColumn();
        
        // Agregar ORDER BY y LIMIT
        $sql .= " ORDER BY p.fecha_creacion DESC LIMIT :limite OFFSET :offset";
        $params[':limite'] = $limite;
        $params[':offset'] = $offset;
        
        // Ejecutar query principal
        $stmt = $pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();
        
        $proyectos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Formatear datos
        foreach ($proyectos as &$proyecto) {
            $proyecto['cliente_nombre'] = trim(($proyecto['cl_nombre'] ?? '') . ' ' . ($proyecto['cl_apellido'] ?? ''));
            $proyecto['cliente_empresa'] = $proyecto['cl_empresa'] ?? '';
            $proyecto['usuario_nombre'] = trim(($proyecto['us_nombre'] ?? '') . ' ' . ($proyecto['us_apellido'] ?? ''));
            
            // Calcular progreso basado en tareas si no está definido
            if ($proyecto['total_tareas'] > 0) {
                $proyecto['progreso_tareas'] = round(($proyecto['tareas_completadas'] / $proyecto['total_tareas']) * 100);
            } else {
                $proyecto['progreso_tareas'] = 0;
            }
        }
        
        $respuesta = [
            'exito' => true,
            'proyectos' => $proyectos,
            'paginacion' => [
                'pagina_actual' => $pagina,
                'total_registros' => $totalRegistros,
                'registros_por_pagina' => $limite,
                'total_paginas' => ceil($totalRegistros / $limite)
            ]
        ];
        
        echo json_encode($respuesta);
        
    } catch (Exception $e) {
        throw new Exception("Error al listar proyectos: " . $e->getMessage());
    }
}

/**
 * Obtener un proyecto específico por ID
 */
function obtenerProyecto($pdo) {
    try {
        $id = $_GET['id'] ?? 0;
        
        if (!$id) {
            throw new Exception("ID de proyecto requerido");
        }
        
        $sql = "SELECT p.*, 
                       c.cl_nombre, c.cl_apellido, c.cl_empresa,
                       u.us_nombre, u.us_apellido
                FROM proyectos p 
                LEFT JOIN clientes c ON p.cliente_id = c.id 
                LEFT JOIN usuarios u ON p.usuario_id = u.id 
                WHERE p.id = :id AND p.pr_activo = 1";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        $proyecto = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$proyecto) {
            throw new Exception("Proyecto no encontrado");
        }
        
        // Obtener tareas del proyecto
        $sqlTareas = "SELECT * FROM tareas_proyecto WHERE proyecto_id = :proyecto_id ORDER BY fecha_creacion DESC";
        $stmtTareas = $pdo->prepare($sqlTareas);
        $stmtTareas->execute([':proyecto_id' => $id]);
        $proyecto['tareas'] = $stmtTareas->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['exito' => true, 'proyecto' => $proyecto]);
        
    } catch (Exception $e) {
        throw new Exception("Error al obtener proyecto: " . $e->getMessage());
    }
}

/**
 * Crear un nuevo proyecto
 */
function crearProyecto($pdo) {
    try {
        // Validar datos requeridos
        $titulo = $_POST['pr_titulo'] ?? '';
        if (empty($titulo)) {
            throw new Exception("El título del proyecto es requerido");
        }
        
        $descripcion = $_POST['pr_descripcion'] ?? '';
        $estado = $_POST['pr_estado'] ?? 'propuesta';
        $fecha_inicio = $_POST['pr_fecha_inicio'] ?? null;
        $fecha_fin = $_POST['pr_fecha_fin'] ?? null;
        $fecha_estimada = $_POST['pr_fecha_estimada'] ?? null;
        $presupuesto = $_POST['pr_presupuesto'] ?? null;
        $prioridad = $_POST['pr_prioridad'] ?? 'media';
        $progreso = (int)($_POST['pr_progreso'] ?? 0);
        $cliente_id = $_POST['cliente_id'] ?? null;
        $usuario_id = $_SESSION['user_id'];
        
        // Insertar proyecto
        $sql = "INSERT INTO proyectos (pr_titulo, pr_descripcion, pr_estado, pr_fecha_inicio, pr_fecha_fin, 
                                     pr_fecha_estimada, pr_presupuesto, pr_prioridad, pr_progreso, 
                                     cliente_id, usuario_id, pr_activo) 
                VALUES (:titulo, :descripcion, :estado, :fecha_inicio, :fecha_fin, 
                        :fecha_estimada, :presupuesto, :prioridad, :progreso, 
                        :cliente_id, :usuario_id, 1)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':titulo' => $titulo,
            ':descripcion' => $descripcion,
            ':estado' => $estado,
            ':fecha_inicio' => $fecha_inicio,
            ':fecha_fin' => $fecha_fin,
            ':fecha_estimada' => $fecha_estimada,
            ':presupuesto' => $presupuesto,
            ':prioridad' => $prioridad,
            ':progreso' => $progreso,
            ':cliente_id' => $cliente_id,
            ':usuario_id' => $usuario_id
        ]);
        
        $proyecto_id = $pdo->lastInsertId();
        
        echo json_encode([
            'exito' => true, 
            'mensaje' => 'Proyecto creado exitosamente',
            'proyecto_id' => $proyecto_id
        ]);
        
    } catch (Exception $e) {
        throw new Exception("Error al crear proyecto: " . $e->getMessage());
    }
}

/**
 * Actualizar un proyecto existente
 */
function actualizarProyecto($pdo) {
    try {
        error_log("🚀 FUNCIÓN actualizarProyecto INICIADA");
        error_log("📥 POST completo: " . print_r($_POST, true));
        error_log("📥 GET completo: " . print_r($_GET, true));
        
        $id = $_POST['proyecto_id'] ?? 0;
        error_log("📥 ID del proyecto a actualizar: " . $id);
        error_log("📥 Tipo de ID: " . gettype($id));
        
        if (!$id) {
            error_log("❌ ID inválido detectado: '$id'");
            throw new Exception("ID de proyecto requerido");
        }
        
        // Validar que el proyecto existe
        $sqlCheck = "SELECT id FROM proyectos WHERE id = :id AND pr_activo = 1";
        $stmtCheck = $pdo->prepare($sqlCheck);
        $stmtCheck->execute([':id' => $id]);
        
        if (!$stmtCheck->fetch()) {
            throw new Exception("Proyecto no encontrado");
        }
        
        $titulo = $_POST['pr_titulo'] ?? '';
        if (empty($titulo)) {
            throw new Exception("El título del proyecto es requerido");
        }
        
        $descripcion = $_POST['pr_descripcion'] ?? '';
        $estado = $_POST['pr_estado'] ?? 'propuesta';
        $fecha_inicio = $_POST['pr_fecha_inicio'] ?? null;
        $fecha_fin = $_POST['pr_fecha_fin'] ?? null;
        $fecha_estimada = $_POST['pr_fecha_estimada'] ?? null;
        $presupuesto = $_POST['pr_presupuesto'] ?? null;
        $prioridad = $_POST['pr_prioridad'] ?? 'media';
        $progreso = (int)($_POST['pr_progreso'] ?? 0);
        $cliente_id = $_POST['cliente_id'] ?? null;
        
        // Actualizar proyecto
        $sql = "UPDATE proyectos SET 
                pr_titulo = :titulo,
                pr_descripcion = :descripcion,
                pr_estado = :estado,
                pr_fecha_inicio = :fecha_inicio,
                pr_fecha_fin = :fecha_fin,
                pr_fecha_estimada = :fecha_estimada,
                pr_presupuesto = :presupuesto,
                pr_prioridad = :prioridad,
                pr_progreso = :progreso,
                cliente_id = :cliente_id,
                fecha_actualizacion = CURRENT_TIMESTAMP
                WHERE id = :id";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            ':titulo' => $titulo,
            ':descripcion' => $descripcion,
            ':estado' => $estado,
            ':fecha_inicio' => $fecha_inicio,
            ':fecha_fin' => $fecha_fin,
            ':fecha_estimada' => $fecha_estimada,
            ':presupuesto' => $presupuesto,
            ':prioridad' => $prioridad,
            ':progreso' => $progreso,
            ':cliente_id' => $cliente_id,
            ':id' => $id
        ]);
        
        error_log("🔍 Resultado de actualización: " . ($result ? 'EXITOSO' : 'FALLIDO'));
        error_log("🔍 Filas afectadas: " . $stmt->rowCount());
        
        if ($result && $stmt->rowCount() > 0) {
            error_log("✅ Proyecto actualizado exitosamente");
            echo json_encode([
                'exito' => true, 
                'mensaje' => 'Proyecto actualizado exitosamente'
            ]);
        } else {
            error_log("⚠️ No se pudo actualizar el proyecto o no hubo cambios");
            echo json_encode([
                'exito' => true, 
                'mensaje' => 'Proyecto actualizado (sin cambios detectados)'
            ]);
        }
        
    } catch (Exception $e) {
        throw new Exception("Error al actualizar proyecto: " . $e->getMessage());
    }
}

/**
 * Eliminar un proyecto (eliminación lógica)
 */
function eliminarProyecto($pdo) {
    try {
        error_log("🚀 FUNCIÓN eliminarProyecto INICIADA");
        error_log("📥 POST completo: " . print_r($_POST, true));
        error_log("📥 GET completo: " . print_r($_GET, true));
        error_log("📥 ID recibido: " . ($_POST['id'] ?? $_GET['id'] ?? 'NO ENCONTRADO'));
        
        $id = $_POST['id'] ?? $_GET['id'] ?? 0;
        error_log("📥 ID procesado: " . $id);
        error_log("📥 Tipo de ID: " . gettype($id));
        
        if (!$id || $id == 0 || $id == '0') {
            error_log("❌ ID inválido detectado: '$id'");
            throw new Exception("ID de proyecto requerido");
        }
        
        // Verificar que el proyecto existe
        $sqlCheck = "SELECT id FROM proyectos WHERE id = :id AND pr_activo = 1";
        $stmtCheck = $pdo->prepare($sqlCheck);
        $stmtCheck->execute([':id' => $id]);
        
        if (!$stmtCheck->fetch()) {
            throw new Exception("Proyecto no encontrado");
        }
        
        // Eliminación lógica
        $sql = "UPDATE proyectos SET pr_activo = 0, fecha_actualizacion = CURRENT_TIMESTAMP WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        echo json_encode([
            'exito' => true, 
            'mensaje' => 'Proyecto eliminado exitosamente'
        ]);
        
    } catch (Exception $e) {
        throw new Exception("Error al eliminar proyecto: " . $e->getMessage());
    }
}

/**
 * Cambiar estado de un proyecto (para funcionalidad drag & drop)
 */
function cambiarEstadoProyecto($pdo) {
    try {
        $id = $_POST['id'] ?? 0;
        $nuevo_estado = $_POST['estado'] ?? '';
        
        if (!$id || !$nuevo_estado) {
            throw new Exception("ID de proyecto y nuevo estado requeridos");
        }
        
        $estados_validos = ['propuesta', 'en_desarrollo', 'en_revision', 'finalizado', 'pausado', 'cancelado'];
        if (!in_array($nuevo_estado, $estados_validos)) {
            throw new Exception("Estado no válido");
        }
        
        // Verificar que el proyecto existe
        $sqlCheck = "SELECT id, pr_estado FROM proyectos WHERE id = :id AND pr_activo = 1";
        $stmtCheck = $pdo->prepare($sqlCheck);
        $stmtCheck->execute([':id' => $id]);
        $proyecto = $stmtCheck->fetch(PDO::FETCH_ASSOC);
        
        if (!$proyecto) {
            throw new Exception("Proyecto no encontrado");
        }
        
        // Actualizar estado
        $sql = "UPDATE proyectos SET pr_estado = :estado, fecha_actualizacion = CURRENT_TIMESTAMP WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':estado' => $nuevo_estado, ':id' => $id]);
        
        echo json_encode([
            'exito' => true, 
            'mensaje' => 'Estado del proyecto actualizado exitosamente',
            'estado_anterior' => $proyecto['pr_estado'],
            'estado_nuevo' => $nuevo_estado
        ]);
        
    } catch (Exception $e) {
        throw new Exception("Error al cambiar estado del proyecto: " . $e->getMessage());
    }
}

/**
 * Listar clientes para el select del formulario
 */
function listarClientes($pdo) {
    try {
        $sql = "SELECT id, cl_nombre, cl_apellido, cl_empresa 
                FROM clientes 
                WHERE cl_activo = 1 
                ORDER BY cl_empresa ASC, cl_apellido ASC, cl_nombre ASC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        
        $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Formatear nombres
        foreach ($clientes as &$cliente) {
            $nombre_completo = trim($cliente['cl_nombre'] . ' ' . $cliente['cl_apellido']);
            $cliente['nombre_display'] = $cliente['cl_empresa'] ? 
                $cliente['cl_empresa'] . " (" . $nombre_completo . ")" : 
                $nombre_completo;
        }
        
        echo json_encode(['exito' => true, 'clientes' => $clientes]);
        
    } catch (Exception $e) {
        throw new Exception("Error al listar clientes: " . $e->getMessage());
    }
}

/**
 * Obtener estadísticas de proyectos para el dashboard
 */
function obtenerEstadisticas($pdo) {
    try {
        // Contar proyectos por estado
        $sqlEstados = "SELECT pr_estado, COUNT(*) as cantidad 
                       FROM proyectos 
                       WHERE pr_activo = 1 
                       GROUP BY pr_estado";
        
        $stmt = $pdo->prepare($sqlEstados);
        $stmt->execute();
        $estadisticas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Proyectos por prioridad
        $sqlPrioridad = "SELECT pr_prioridad, COUNT(*) as cantidad 
                         FROM proyectos 
                         WHERE pr_activo = 1 
                         GROUP BY pr_prioridad";
        
        $stmt = $pdo->prepare($sqlPrioridad);
        $stmt->execute();
        $prioridades = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Total de proyectos
        $sqlTotal = "SELECT COUNT(*) as total FROM proyectos WHERE pr_activo = 1";
        $stmt = $pdo->prepare($sqlTotal);
        $stmt->execute();
        $total = $stmt->fetchColumn();
        
        echo json_encode([
            'exito' => true, 
            'estadisticas' => [
                'por_estado' => $estadisticas,
                'por_prioridad' => $prioridades,
                'total' => $total
            ]
        ]);
        
    } catch (Exception $e) {
        throw new Exception("Error al obtener estadísticas: " . $e->getMessage());
    }
}
?>