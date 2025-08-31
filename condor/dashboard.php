<?php
// ===== DASHBOARD PRINCIPAL =====
/**
 * Página principal del dashboard después del login exitoso
 * Verifica la sesión del usuario y muestra el contenido correspondiente
 */

// Iniciar sesión
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Si no está logueado, redirigir al login
    header('Location: index.php');
    exit;
}

// Obtener datos del usuario de la sesión
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$rol = $_SESSION['rol'];
$email = $_SESSION['email'];
$nombre_completo = $_SESSION['nombre_completo'];
$login_time = $_SESSION['login_time'];

// Función para cerrar sesión
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Condor</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #8b5cf6;
            --primary-dark: #7c3aed;
            --secondary-color: #f8fafc;
            --text-dark: #1e293b;
            --text-light: #64748b;
            --border-color: #e2e8f0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--secondary-color);
        }

        .navbar {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar {
            background: white;
            min-height: calc(100vh - 76px);
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar .nav-link {
            color: var(--text-dark);
            padding: 0.75rem 1rem;
            border-radius: 8px;
            margin: 0.25rem 0;
            transition: all 0.3s ease;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: var(--primary-color);
            color: white;
        }

        .main-content {
            padding: 2rem;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        /* Estilos específicos para asignaciones */
        .assignments-container {
            font-family: 'Inter', sans-serif;
        }

        .text-primary {
            color: #8b5cf6 !important;
        }

        .nav-tabs .nav-link {
            color: #64748b;
            border: none;
            margin-right: 8px;
            padding: 12px 20px;
            border-radius: 8px 8px 0 0;
            transition: all 0.3s ease;
        }

        .nav-tabs .nav-link:hover {
            border-color: transparent;
            background: rgba(139, 92, 246, 0.1);
            color: #8b5cf6;
        }

        .nav-tabs .nav-link.active {
            background: #8b5cf6;
            color: white !important;
            border-color: #8b5cf6;
            font-weight: 600;
        }

        .header-section {
            position: relative;
            overflow: hidden;
        }

        .header-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            50% { transform: translate(10px, -10px) rotate(5deg); }
        }

        .alert-info {
            background: rgba(139, 92, 246, 0.1);
            border: 1px solid rgba(139, 92, 246, 0.2);
            color: #7c3aed;
        }

        .btn-primary {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            border: none;
            font-weight: 600;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #7c3aed, #6d28d9);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(139, 92, 246, 0.3);
        }

        .stats-card {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
        }

        .welcome-section {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .user-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            font-weight: 600;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="#">
                <i class="fas fa-rocket me-2"></i>Condor
            </a>
            
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-2"></i><?php echo htmlspecialchars($nombre_completo); ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Mi Perfil</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Configuración</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="?logout=1" id="logout-btn"><i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0">
                <div class="sidebar p-3">
                    <nav class="nav flex-column">
                        <a class="nav-link active" href="#">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                        <a class="nav-link" href="#" id="usuarios-btn">
                            <i class="fas fa-users me-2"></i>Usuarios
                        </a>
                        <a class="nav-link" href="#" id="asignaciones-btn">
                            <i class="fas fa-user-cog me-2"></i>Asignaciones de Proyectos
                        </a>
                        <a class="nav-link" href="#">
                            <i class="fas fa-chart-bar me-2"></i>Reportes
                        </a>
                        <a class="nav-link" href="#">
                            <i class="fas fa-cog me-2"></i>Configuración
                        </a>
                        <a class="nav-link" href="#">
                            <i class="fas fa-question-circle me-2"></i>Ayuda
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <div class="main-content">
                    
                    <!-- Welcome Section -->
                    <div class="welcome-section">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="user-avatar">
                                    <?php echo strtoupper(substr($nombre_completo, 0, 1)); ?>
                                </div>
                            </div>
                            <div class="col">
                                <h2 class="mb-1">¡Bienvenido, POR DEFECTO <?php echo htmlspecialchars($nombre_completo); ?>!</h2>
                                <p class="text-muted mb-0">
                                    <i class="fas fa-clock me-1"></i>
                                    Último acceso: <?php echo date('d/m/Y H:i', $login_time); ?>
                                </p>
                            </div>
                            <div class="col-auto">
                                <span class="badge bg-primary fs-6"><?php echo ucfirst($rol === 'administrador' ? 'Administrador' : 'Usuario'); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Stats Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="card stats-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Usuarios Activos</h6>
                                            <h3 class="mb-0">1,234</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-users fa-2x opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card stats-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Reportes</h6>
                                            <h3 class="mb-0">567</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-chart-line fa-2x opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card stats-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Proyectos</h6>
                                            <h3 class="mb-0">89</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-project-diagram fa-2x opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card stats-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Tareas</h6>
                                            <h3 class="mb-0">234</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-tasks fa-2x opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Content Cards -->
                    <div class="row">
                        <div class="col-md-8 mb-4">
                            <div class="card">
                                <div class="card-header bg-white">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-chart-area me-2"></i>Actividad Reciente
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="list-group list-group-flush">
                                        <div class="list-group-item border-0 px-0">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1">Nuevo usuario registrado</h6>
                                                <small class="text-muted">Hace 3 minutos</small>
                                            </div>
                                            <p class="mb-1">Juan Pérez se ha registrado en el sistema.</p>
                                        </div>
                                        <div class="list-group-item border-0 px-0">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1">Reporte generado</h6>
                                                <small class="text-muted">Hace 15 minutos</small>
                                            </div>
                                            <p class="mb-1">Se ha generado el reporte mensual de ventas.</p>
                                        </div>
                                        <div class="list-group-item border-0 px-0">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1">Proyecto actualizado</h6>
                                                <small class="text-muted">Hace 1 hora</small>
                                            </div>
                                            <p class="mb-1">El proyecto "Condor" ha sido actualizado.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-4">
                            <div class="card">
                                <div class="card-header bg-white">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-bell me-2"></i>Notificaciones
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="list-group list-group-flush">
                                        <div class="list-group-item border-0 px-0">
                                            <small class="text-muted">Hace 5 minutos</small>
                                            <p class="mb-1">Nueva actualización disponible</p>
                                        </div>
                                        <div class="list-group-item border-0 px-0">
                                            <small class="text-muted">Hace 30 minutos</small>
                                            <p class="mb-1">Backup completado exitosamente</p>
                                        </div>
                                        <div class="list-group-item border-0 px-0">
                                            <small class="text-muted">Hace 2 horas</small>
                                            <p class="mb-1">Mantenimiento programado para mañana</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/logout.js"></script>
    <script src="js/funciones.js"></script>
    <script>
        // Script para el dashboard
        console.log('Dashboard cargado correctamente');
        
        // Mostrar información del usuario en consola
        console.log('Usuario logueado:', {
            id: <?php echo $user_id; ?>,
            username: '<?php echo $username; ?>',
            rol: '<?php echo $rol; ?>',
            email: '<?php echo $email; ?>'
        });

        // ==================== GESTIÓN DE ASIGNACIONES ====================

        // Funciones de navegación
        function mostrarAsignaciones() {
            console.log('🔍 Mostrando vista de asignaciones');
            
            // Actualizar navegación activa
            $('.nav-link').removeClass('active');
            $('#asignaciones-btn').addClass('active');
            
            // Cargar contenido de asignaciones
            $('.main-content').html(`
                <div class="assignments-container">
                    <div class="container-fluid">
                        <!-- Header de asignaciones -->
                        <div class="header-section" style="background: var(--gradiente-violeta, linear-gradient(135deg, #667eea 0%, #764ba2 50%, #8b5cf6 100%)); padding: 30px; border-radius: 16px; color: white; margin-bottom: 30px;">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h1 class="mb-2">
                                        <i class="fas fa-user-cog me-3"></i>Gestión de Asignaciones de Proyectos
                                    </h1>
                                    <p class="mb-0 opacity-90">Administra la asignación de usuarios a proyectos y supervisa el equipo de trabajo</p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <button class="btn btn-light" onclick="cargarEstadisticasAsignaciones()">
                                        <i class="fas fa-sync-alt me-2"></i>Actualizar
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Estadísticas principales -->
                        <div class="row mb-4" id="estadisticas-row">
                            <div class="col-md-3 mb-3">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <i class="fas fa-project-diagram fa-2x text-primary mb-3"></i>
                                        <h3 class="text-primary" id="stat-proyectos">0</h3>
                                        <p class="text-muted mb-0">Proyectos con Asignaciones</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <i class="fas fa-users fa-2x text-success mb-3"></i>
                                        <h3 class="text-success" id="stat-usuarios">0</h3>
                                        <p class="text-muted mb-0">Usuarios Asignados</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <i class="fas fa-tasks fa-2x text-info mb-3"></i>
                                        <h3 class="text-info" id="stat-asignaciones">0</h3>
                                        <p class="text-muted mb-0">Total Asignaciones</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <i class="fas fa-clock fa-2x text-warning mb-3"></i>
                                        <h3 class="text-warning" id="stat-horas">0</h3>
                                        <p class="text-muted mb-0">Horas Trabajadas</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pestañas de contenido -->
                        <ul class="nav nav-tabs mb-4" id="assignmentTabs">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#tab-proyectos">
                                    <i class="fas fa-project-diagram me-2"></i>Por Proyectos
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#tab-usuarios">
                                    <i class="fas fa-users me-2"></i>Por Usuarios
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#tab-roles">
                                    <i class="fas fa-user-tag me-2"></i>Por Roles
                                </a>
                            </li>
                        </ul>

                        <!-- Contenido de pestañas -->
                        <div class="tab-content">
                            <!-- Vista por proyectos -->
                            <div class="tab-pane fade show active" id="tab-proyectos">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-white">
                                        <h5 class="mb-0">
                                            <i class="fas fa-project-diagram me-2"></i>Asignaciones por Proyecto
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div id="loading-proyectos" class="text-center py-4">
                                            <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                                            <p class="mt-2 text-muted">Cargando asignaciones...</p>
                                        </div>
                                        <div id="contenido-proyectos" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Vista por usuarios -->
                            <div class="tab-pane fade" id="tab-usuarios">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-white">
                                        <h5 class="mb-0">
                                            <i class="fas fa-users me-2"></i>Asignaciones por Usuario
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div id="loading-usuarios" class="text-center py-4">
                                            <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                                            <p class="mt-2 text-muted">Cargando usuarios...</p>
                                        </div>
                                        <div id="contenido-usuarios" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Vista por roles -->
                            <div class="tab-pane fade" id="tab-roles">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-white">
                                        <h5 class="mb-0">
                                            <i class="fas fa-user-tag me-2"></i>Distribución por Roles
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div id="loading-roles" class="text-center py-4">
                                            <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                                            <p class="mt-2 text-muted">Analizando roles...</p>
                                        </div>
                                        <div id="contenido-roles" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `);
            
            // Cargar datos iniciales
            setTimeout(() => {
                cargarEstadisticasAsignaciones();
                cargarAsignacionesPorProyectos();
            }, 100);
        }

        // Cargar estadísticas generales
        function cargarEstadisticasAsignaciones() {
            $.ajax({
                url: 'php/asignaciones_proyectos.php',
                method: 'GET',
                data: { accion: 'obtener_estadisticas' },
                dataType: 'json',
                success: function(response) {
                    if (response.exito) {
                        const stats = response.estadisticas;
                        $('#stat-proyectos').text(stats.proyectos_con_asignaciones || 0);
                        $('#stat-usuarios').text(stats.usuarios_asignados || 0);
                        $('#stat-asignaciones').text(stats.total_asignaciones || 0);
                        $('#stat-horas').text(Math.round(stats.total_horas_trabajadas || 0));
                    }
                },
                error: function() {
                    console.error('Error al cargar estadísticas');
                }
            });
        }

        // Cargar asignaciones por proyectos
        function cargarAsignacionesPorProyectos() {
            // Simular carga de datos de proyectos con asignaciones
            setTimeout(() => {
                $('#loading-proyectos').hide();
                $('#contenido-proyectos').show().html(`
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Funcionalidad en desarrollo</strong><br>
                        Esta vista mostrará todos los proyectos con sus respectivas asignaciones de usuarios.
                        Para gestionar asignaciones específicas, ve a la sección de Proyectos y edita cada proyecto individualmente.
                    </div>
                    <div class="text-center py-4">
                        <a href="proyectos.php" class="btn btn-primary">
                            <i class="fas fa-arrow-right me-2"></i>Ir a Gestión de Proyectos
                        </a>
                    </div>
                `);
            }, 1000);
        }

        // Eventos del DOM
        $(document).ready(function() {
            // Inicializar funciones existentes
            if (typeof funciones !== 'undefined' && funciones.mostrarModalUsuarios) {
                funciones.mostrarModalUsuarios();
            }

            // Evento para mostrar asignaciones
            $('#asignaciones-btn').click(function(e) {
                e.preventDefault();
                mostrarAsignaciones();
            });

            // Eventos para cambio de pestañas
            $(document).on('shown.bs.tab', 'a[data-bs-toggle="tab"]', function (e) {
                const target = $(e.target).attr('href');
                
                if (target === '#tab-usuarios') {
                    setTimeout(() => {
                        $('#loading-usuarios').hide();
                        $('#contenido-usuarios').show().html(`
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Vista de usuarios con sus asignaciones en desarrollo.
                            </div>
                        `);
                    }, 500);
                } else if (target === '#tab-roles') {
                    setTimeout(() => {
                        $('#loading-roles').hide();
                        $('#contenido-roles').show().html(`
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Análisis de distribución de roles en desarrollo.
                            </div>
                        `);
                    }, 500);
                }
            });
        });
    </script>
</body>
</html>
