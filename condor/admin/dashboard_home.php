<?php
/**
 * Dashboard Principal - Página de Inicio
 * Widgets con información relevante del sistema
 */

// Verificar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../index.php');
    exit;
}

// Debug temporal - mostrar variables de sesión
echo "<!-- DEBUG SESSION: ";
echo "logged_in: " . ($_SESSION['logged_in'] ?? 'NO') . ", ";
echo "user_id: " . ($_SESSION['user_id'] ?? 'NO') . ", ";
echo "us_foto_perfil: " . ($_SESSION['us_foto_perfil'] ?? 'NO') . ", ";
echo "us_url_perfil: " . ($_SESSION['us_url_perfil'] ?? 'NO') . ", ";
echo "us_nombre: " . ($_SESSION['us_nombre'] ?? 'NO') . ", ";
echo "us_apellido: " . ($_SESSION['us_apellido'] ?? 'NO') . ", ";
echo "nombre_completo: " . ($_SESSION['nombre_completo'] ?? 'NO');
echo " -->";

require_once '../php/config_bd.php';

// Obtener conexión a la base de datos
try {
    $database = Database::getInstance();
    $pdo = $database->getConnection();
} catch (Exception $e) {
    die('Error de conexión: ' . $e->getMessage());
}

// Obtener estadísticas generales
$stats = [];

try {
    // Total de proyectos por estado
    $stmt = $pdo->query("
        SELECT 
            pr_estado,
            COUNT(*) as total,
            COUNT(CASE WHEN pr_prioridad = 'alta' THEN 1 END) as alta_prioridad
        FROM proyectos 
        GROUP BY pr_estado
    ");
    $stats['proyectos'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Total de usuarios activos
    $stmt = $pdo->query("
        SELECT 
            COUNT(*) as total_usuarios,
            COUNT(CASE WHEN us_activo = 1 THEN 1 END) as usuarios_activos,
            COUNT(CASE WHEN us_rol = 'administrador' THEN 1 END) as administradores,
            COUNT(CASE WHEN us_rol = 'usuario' THEN 1 END) as usuarios_normales
        FROM usuarios
    ");
    $stats['usuarios'] = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Asignaciones recientes
    $stmt = $pdo->query("
        SELECT 
            COUNT(*) as total_asignaciones,
            COUNT(CASE WHEN estado_asignacion = 'activo' THEN 1 END) as asignaciones_activas,
            AVG(horas_asignadas) as promedio_horas
        FROM asignaciones_proyectos
    ");
    $stats['asignaciones'] = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Proyectos recientes (detectar columna de fecha disponible)
    try {
        // Primero intentar con pr_fecha_inicio
        $stmt = $pdo->query("
            SELECT 
                p.*,
                COUNT(ap.id) as usuarios_asignados,
                SUM(ap.horas_asignadas) as total_horas
            FROM proyectos p
            LEFT JOIN asignaciones_proyectos ap ON p.id = ap.proyecto_id
            GROUP BY p.id
            ORDER BY COALESCE(p.pr_fecha_inicio, p.id) DESC
            LIMIT 5
        ");
        $stats['proyectos_recientes'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Si falla, usar solo el id para ordenar
        $stmt = $pdo->query("
            SELECT 
                p.*,
                COUNT(ap.id) as usuarios_asignados,
                SUM(ap.horas_asignadas) as total_horas
            FROM proyectos p
            LEFT JOIN asignaciones_proyectos ap ON p.id = ap.proyecto_id
            GROUP BY p.id
            ORDER BY p.id DESC
            LIMIT 5
        ");
        $stats['proyectos_recientes'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Últimas asignaciones
    $stmt = $pdo->query("
        SELECT 
            ap.*,
            p.pr_titulo,
            CONCAT(u.us_nombre, ' ', u.us_apellido) as nombre_usuario,
            u.us_foto_perfil
        FROM asignaciones_proyectos ap
        INNER JOIN proyectos p ON ap.proyecto_id = p.id
        INNER JOIN usuarios u ON ap.usuario_id = u.id
        ORDER BY ap.fecha_asignacion DESC
        LIMIT 6
    ");
    $stats['asignaciones_recientes'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    error_log("Error al obtener estadísticas del dashboard: " . $e->getMessage());
    // Inicializar arrays vacíos para evitar errores en la vista
    $stats = [
        'proyectos' => [],
        'usuarios' => ['total_usuarios' => 0, 'usuarios_activos' => 0, 'administradores' => 0, 'usuarios_normales' => 0],
        'asignaciones' => ['total_asignaciones' => 0, 'asignaciones_activas' => 0, 'promedio_horas' => 0],
        'proyectos_recientes' => [],
        'asignaciones_recientes' => []
    ];
}
?>

<!-- Dashboard Principal -->
<div class="container-fluid p-0">
    <!-- Header de bienvenida -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-lg" style="background: var(--gradiente-violeta); border-radius: 20px;">
                <div class="card-body text-white p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                                                         <h1 class="display-6 fw-bold mb-2">
                                 ¡Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre_completo'] ?? 'Usuario'); ?>! 
                             </h1>
                            <p class="lead mb-0 opacity-90">
                                Aquí tienes un resumen del estado actual de tus proyectos y asignaciones
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div style="padding: 1rem;  ">
                                <?php if (isset($_SESSION['us_foto_perfil']) && $_SESSION['us_foto_perfil']): ?>
                                    <img src="../<?php echo htmlspecialchars($_SESSION['us_foto_perfil']); ?>" 
                                         class="rounded-circle" width="100" height="100" alt="Foto de perfil"
                                         style="box-shadow: 0 4px 8px rgba(0,0,0,0.2); border: 2px solid black; object-fit: cover;"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <?php else: ?>
                                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-white" 
                                         style="width: 100px; height: 100px;">
                                        <i class="fas fa-user fa-2x text-secondary"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas principales -->
    <div class="row mb-4">
        <!-- Total Proyectos -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Proyectos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php 
                                $total_proyectos = array_sum(array_column($stats['proyectos'] ?? [], 'total'));
                                echo $total_proyectos; 
                                ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="rounded-circle p-3" style="background: linear-gradient(45deg, #667eea, #764ba2);">
                                <i class="fas fa-project-diagram fa-2x text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Usuarios Activos -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Usuarios Activos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo $stats['usuarios']['usuarios_activos'] ?? 0; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="rounded-circle p-3" style="background: linear-gradient(45deg, #06d6a0, #118ab2);">
                                <i class="fas fa-users fa-2x text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Asignaciones Activas -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Asignaciones Activas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo $stats['asignaciones']['asignaciones_activas'] ?? 0; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="rounded-circle p-3" style="background: linear-gradient(45deg, #ffd166, #f77f00);">
                                <i class="fas fa-user-check fa-2x text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Promedio Horas -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Promedio Horas/Asignación
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo number_format($stats['asignaciones']['promedio_horas'] ?? 0, 1); ?>h
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="rounded-circle p-3" style="background: linear-gradient(45deg, #f72585, #b5179e);">
                                <i class="fas fa-clock fa-2x text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Segunda fila - Gráficos y tablas -->
    <div class="row">
        <!-- Distribución de Proyectos por Estado -->
        <div class="col-xl-6 col-lg-7 mb-4">
            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between" 
                     style="background: rgba(139, 92, 246, 0.1); border-radius: 15px 15px 0 0;">
                    <h6 class="m-0 font-weight-bold" style="color: #8b5cf6;">
                        <i class="fas fa-chart-pie me-2"></i>Distribución de Proyectos por Estado
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <?php if (!empty($stats['proyectos'])): ?>
                            <div class="row">
                                <?php 
                                $colores = [
                                    'propuesta' => ['bg' => '#e3f2fd', 'text' => '#1976d2', 'icon' => 'fas fa-lightbulb'],
                                    'en_desarrollo' => ['bg' => '#e8f5e8', 'text' => '#2e7d32', 'icon' => 'fas fa-cogs'],
                                    'en_revision' => ['bg' => '#fff3e0', 'text' => '#f57c00', 'icon' => 'fas fa-eye'],
                                    'completado' => ['bg' => '#e8f5e8', 'text' => '#388e3c', 'icon' => 'fas fa-check-circle'],
                                    'pausado' => ['bg' => '#fce4ec', 'text' => '#c2185b', 'icon' => 'fas fa-pause']
                                ];
                                foreach ($stats['proyectos'] as $proyecto): 
                                    $estado = $proyecto['pr_estado'];
                                    $color = $colores[$estado] ?? ['bg' => '#f5f5f5', 'text' => '#666', 'icon' => 'fas fa-folder'];
                                ?>
                                    <div class="col-sm-6 mb-3">
                                        <div class="d-flex align-items-center p-3 rounded-3" style="background: <?php echo $color['bg']; ?>;">
                                            <div class="me-3">
                                                <i class="<?php echo $color['icon']; ?> fa-2x" style="color: <?php echo $color['text']; ?>;"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold" style="color: <?php echo $color['text']; ?>;">
                                                    <?php echo $proyecto['total']; ?>
                                                </div>
                                                <div class="small text-muted">
                                                    <?php echo ucfirst(str_replace('_', ' ', $estado)); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-chart-pie fa-3x mb-3"></i>
                                <p>No hay datos de proyectos disponibles</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actividad Reciente -->
        <div class="col-xl-6 col-lg-5 mb-4">
            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between"
                     style="background: rgba(139, 92, 246, 0.1); border-radius: 15px 15px 0 0;">
                    <h6 class="m-0 font-weight-bold" style="color: #8b5cf6;">
                        <i class="fas fa-list-alt me-2"></i>Asignaciones Recientes
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <?php if (!empty($stats['asignaciones_recientes'])): ?>
                            <?php foreach (array_slice($stats['asignaciones_recientes'], 0, 6) as $asignacion): ?>
                                <div class="list-group-item border-0 py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 me-3">
                                                                                         <?php if ($asignacion['us_foto_perfil']): ?>
                                                 <img src="../<?php echo htmlspecialchars($asignacion['us_foto_perfil']); ?>" 
                                                      class="rounded-circle" width="40" height="40" alt="Avatar"
                                                      onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <?php else: ?>
                                                <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                                     style="width: 40px; height: 40px; background: var(--gradiente-violeta);">
                                                    <i class="fas fa-user text-white"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold text-dark">
                                                <?php echo htmlspecialchars($asignacion['nombre_usuario']); ?>
                                            </div>
                                            <div class="small text-muted">
                                                Asignado a: <?php echo htmlspecialchars($asignacion['pr_titulo']); ?>
                                            </div>
                                            <div class="small text-primary">
                                                Rol: <?php echo htmlspecialchars($asignacion['rol_proyecto']); ?>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <div class="small text-muted">
                                                <?php echo date('d/m', strtotime($asignacion['fecha_asignacion'])); ?>
                                            </div>
                                            <?php if ($asignacion['horas_asignadas']): ?>
                                                <div class="small text-secondary">
                                                    <?php echo $asignacion['horas_asignadas']; ?>h
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center text-muted py-5">
                                <i class="fas fa-user-plus fa-3x mb-3"></i>
                                <p>No hay asignaciones recientes</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tercera fila - Proyectos recientes -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-header py-3" 
                     style="background: rgba(139, 92, 246, 0.1); border-radius: 15px 15px 0 0;">
                    <h6 class="m-0 font-weight-bold" style="color: #8b5cf6;">
                        <i class="fas fa-project-diagram me-2"></i>Proyectos Recientes
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead style="background: #f8f9fa;">
                                <tr>
                                    <th class="border-0 p-3">Proyecto</th>
                                    <th class="border-0 p-3">Estado</th>
                                    <th class="border-0 p-3">Prioridad</th>
                                    <th class="border-0 p-3">Usuarios</th>
                                    <th class="border-0 p-3">Horas</th>
                                    <th class="border-0 p-3">Fecha Inicio</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($stats['proyectos_recientes'])): ?>
                                    <?php foreach ($stats['proyectos_recientes'] as $proyecto): ?>
                                        <tr>
                                            <td class="p-3">
                                                <div class="fw-semibold text-dark">
                                                    <?php echo htmlspecialchars($proyecto['pr_titulo']); ?>
                                                </div>
                                                <div class="small text-muted">
                                                    <?php echo htmlspecialchars(substr($proyecto['pr_descripcion'] ?? '', 0, 50) . '...'); ?>
                                                </div>
                                            </td>
                                            <td class="p-3">
                                                <?php
                                                $estado_colors = [
                                                    'propuesta' => 'info',
                                                    'en_desarrollo' => 'warning', 
                                                    'en_revision' => 'primary',
                                                    'completado' => 'success',
                                                    'pausado' => 'danger'
                                                ];
                                                $color = $estado_colors[$proyecto['pr_estado']] ?? 'secondary';
                                                ?>
                                                <span class="badge bg-<?php echo $color; ?> bg-opacity-20 text-dark px-3 py-2">
                                                    <?php 
                                                    echo $proyecto['pr_estado'] ? ucfirst(str_replace('_', ' ', $proyecto['pr_estado'])) : 'Sin estado';
                                                    ?>
                                                </span>
                                            </td>
                                            <td class="p-3">
                                                <?php
                                                $prioridad_colors = [
                                                    'alta' => 'danger',
                                                    'media' => 'warning',
                                                    'baja' => 'success'
                                                ];
                                                $color = $prioridad_colors[$proyecto['pr_prioridad']] ?? 'secondary';
                                                ?>
                                                <span class="badge bg-<?php echo $color; ?> bg-opacity-20 text-dark">
                                                    <?php 
                                                    echo $proyecto['pr_prioridad'] ? ucfirst($proyecto['pr_prioridad']) : 'Sin prioridad';
                                                    ?>
                                                </span>
                                            </td>
                                            <td class="p-3">
                                                <span class="fw-semibold"><?php echo $proyecto['usuarios_asignados']; ?></span>
                                                <i class="fas fa-users ms-1 text-muted"></i>
                                            </td>
                                            <td class="p-3">
                                                <span class="fw-semibold"><?php echo $proyecto['total_horas'] ?? 0; ?>h</span>
                                            </td>
                                            <td class="p-3 text-muted">
                                                <?php 
                                                if (isset($proyecto['pr_fecha_inicio']) && $proyecto['pr_fecha_inicio']) {
                                                    echo date('d/m/Y', strtotime($proyecto['pr_fecha_inicio']));
                                                } else {
                                                    echo '<span class="text-muted">Sin fecha</span>';
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-5">
                                            <i class="fas fa-folder-open fa-3x mb-3"></i>
                                            <p>No hay proyectos recientes</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
:root {
    --gradiente-violeta: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #8b5cf6 100%);
}

.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
}

.list-group-item:hover {
    background-color: rgba(139, 92, 246, 0.05);
}

.table tbody tr:hover {
    background-color: rgba(139, 92, 246, 0.05);
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.card {
    animation: fadeIn 0.6s ease-out;
}
</style>