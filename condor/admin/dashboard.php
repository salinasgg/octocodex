<?php
// ===== DASHBOARD PRINCIPAL =====
/**
 * Página principal del dashboard después del login exitoso
 * Verifica la sesión del usuario y muestra el contenido correspondiente
 */

// Headers para evitar caché
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Iniciar sesión
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Si no está logueado, redirigir al login
    header('Location: ../index.php');
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
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Dashboard - Condor</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Estilos -->
    <link rel="stylesheet" href="css/estilo_dashboard_admin.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../../condor/css/variables.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/enconstruccion.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../../condor/css/style_editar_user.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../../condor/css/style_ver_cliente.css?v=<?php echo time(); ?>">
</head>
<body>



    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="#">
                <img src="../../logo/logo.png" alt="Logo" class="me-2" style="height: 35px;">Octocodex
            </a>
            <div class="row justify-content-center navbar-admin" >
                <div class="col-auto">
                    <ul class="nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                Herramientas
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="https://gemini.google.com/" target="_blank"></i>Gemini AI</a></li>
                                <li><a class="dropdown-item" href="https://chat.openai.com/" target="_blank"></i>Chat GPT</a></li>
                                <li><a class="dropdown-item" href="https://claude.ai/"></i>Claude AI</a></li>
                                <li><a class="dropdown-item" href="https://copilot.microsoft.com/"></i>Copilot</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                Cartera de Clientes
                            </a>
                            <ul class="dropdown-menu">
                                <!-- <li><a class="dropdown-item" href="#" id="ver-clientes"><i class="fas fa-address-book me-2"></i>Ver Clientes</a></li>
                                <li><a class="dropdown-item" href="#" id="agregar-cliente"><i class="fas fa-user-plus me-2"></i>Agregar Cliente</a></li> -->
                                <li><a class="dropdown-item" href="#" id="gestionar-clientes"><i class="fas fa-tasks me-2"></i>Gestionar Clientes</a></li>
                            </ul>
                        </li>
                        <!-- <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                Requerimientos
                            </a>
                            <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-tools me-2"></i>Evolutivo </a>   </li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-wrench me-2"></i>Correctivo </a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-cogs me-2"></i>Incidente</a></li>
                            </ul>
                        </li> -->
                        <li class="nav-item">
                            <a class="nav-link" href="#" id="proyectos-btn">Proyectos</a>
                        </li>
                        <!-- <li class="nav-item">
                            <a class="nav-link disabled" href="#">Disabled</a>
                        </li> -->
                    </ul>
                </div>
            </div>
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-2"></i><?php echo htmlspecialchars($nombre_completo); ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" id="perfil-btn"><i class="fas fa-user me-2"></i>Mi Perfil</a></li>
                        <!-- <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Configuración</a></li> -->
                        <li><a class="dropdown-item" href="#" id="cambiar-password-btn"><i class="fas fa-key me-2"></i>Cambiar Password</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#" id="logout-btn"><i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0" id="sidebar">
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
                        <!-- <a class="nav-link" href="#">
                            <i class="fas fa-chart-bar me-2"></i>Reportes
                        </a>
                        <a class="nav-link" href="#">
                            <i class="fas fa-cog me-2"></i>Configuración
                        </a>
                        <a class="nav-link" href="#">
                            <i class="fas fa-question-circle me-2"></i>Ayuda
                        </a> -->
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 col-lg-10">
                <div class="main-content">
                                         <div class="container-fluid">
      
                         
                         <div class="row construccion">
                             
                         </div>
                     </div>
                    <!-- Welcome Section -->
                    <!-- <div class="welcome-section">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="user-avatar">
                                    <?php //echo strtoupper(substr($nombre_completo, 0, 1)); ?>
                                </div>
                            </div>
                            <div class="col">
                                <h2 class="mb-1">¡BienvenidA, Administrador <?php //echo htmlspecialchars($nombre_completo); ?>!</h2>
                                <p class="text-muted mb-0">
                                    <i class="fas fa-clock me-1"></i>
                                    Último acceso: <?php //echo date('d/m/Y H:i', $login_time); ?>
                                </p>
                            </div>
                            <div class="col-auto">
                                <span class="badge bg-primary fs-6"><?php //<secho ucfirst($rol === 'administrador' ? 'Administrador' : 'Usuario'); ?></span>
                            </div>
                        </div>
                    </div> -->

                    <!-- Stats Cards -->
                    <!-- <div class="row mb-4">
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
                    </div> -->

                    <!-- Content Cards -->
                    <!-- <div class="row">
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
                    </div> -->

                </div>
            </div>
        </div>
    </div>

   <div>
    <?php include '../php/modal_editar_usuario.php'; ?>
    <?php include '../php/modal_nuevo_usuario.php'; ?>
   </div>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/logout.js?v=<?php echo time(); ?>"></script>
    <script src="../js/perfil.js?v=<?php echo time(); ?>"></script>    
    <script src="../js/showMessage.js?v=<?php echo time(); ?>"></script>
    <script src="../js/funciones.js?v=<?php echo time(); ?>"></script>
    <script src="../js/nuevo_usuario.js?v=<?php echo time(); ?>"></script>
    <!-- El ?v=echo time();  agrega un timestamp como parámetro de versión al archivo JS.
         Esto fuerza al navegador a descargar una nueva copia del archivo en lugar de usar la versión en caché,
         asegurando que siempre se cargue la última versión del JavaScript -->
    <script src="../../condor/js/ABMClientes.js?v=<?php echo time(); ?>"></script>
    <script src="../../condor/js/EditarClienteNuevo.js?v=<?php echo time(); ?>"></script>
    <script src="../../condor/js/cambio_clave.js?v=<?php echo time(); ?>"></script>
    <script src="../../condor/js/ABMProyectos.js?v=<?php echo time(); ?>"></script>

    
    

    <script>

        funciones.mostrarModalUsuarios();
        funciones.inicializarModalEvents(); // Inicializar eventos de la modal

        // Función para mostrar asignaciones de proyectos
        function mostrarAsignaciones() {
            $('.nav-link').removeClass('active');
            $('#asignaciones-btn').addClass('active');
            
            $('.main-content').html(`
                <div class="assignments-container">
                    <!-- Header Section -->
                    <div class="header-section" style="background: var(--gradiente-violeta); padding: 2rem; border-radius: 15px; margin-bottom: 2rem; color: white; box-shadow: 0 8px 32px rgba(139, 92, 246, 0.3);">
                        <div class="row align-items-center">
                            <div class="col">
                                <h1 class="mb-2" style="font-size: 2.5rem; font-weight: 700;">
                                    <i class="fas fa-user-cog me-3"></i>Gestión de Asignaciones de Proyectos
                                </h1>
                                <p class="mb-0 opacity-75" style="font-size: 1.1rem;">
                                    Administra las asignaciones de usuarios a proyectos de manera eficiente
                                </p>
                            </div>
                            <div class="col-auto">
                                <div class="stats-mini d-flex gap-3">
                                    <div class="stat-item text-center">
                                        <div class="stat-number" style="font-size: 2rem; font-weight: 800;" id="total-asignaciones">0</div>
                                        <div class="stat-label opacity-75">Asignaciones</div>
                                    </div>
                                    <div class="stat-item text-center">
                                        <div class="stat-number" style="font-size: 2rem; font-weight: 800;" id="total-usuarios">0</div>
                                        <div class="stat-label opacity-75">Usuarios</div>
                                    </div>
                                    <div class="stat-item text-center">
                                        <div class="stat-number" style="font-size: 2rem; font-weight: 800;" id="total-proyectos">0</div>
                                        <div class="stat-label opacity-75">Proyectos</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabs Navigation -->
                    <ul class="nav nav-tabs nav-justified mb-4" style="border-bottom: 3px solid #e9ecef;">
                        <li class="nav-item">
                            <a class="nav-link active assignment-tab" data-tab="overview" href="#" 
                               style="font-weight: 600; color: #8b5cf6; border-color: transparent;">
                                <i class="fas fa-chart-pie me-2"></i>Vista General
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link assignment-tab" data-tab="projects" href="#"
                               style="font-weight: 600; color: #6b7280; border-color: transparent;">
                                <i class="fas fa-project-diagram me-2"></i>Por Proyecto
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link assignment-tab" data-tab="users" href="#"
                               style="font-weight: 600; color: #6b7280; border-color: transparent;">
                                <i class="fas fa-users me-2"></i>Por Usuario
                            </a>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content">
                        <!-- Overview Tab -->
                        <div class="tab-pane active" id="overview-content">
                            <div class="row mb-4">
                                <div class="col-md-8">
                                    <div class="card shadow-sm border-0" style="border-radius: 15px;">
                                        <div class="card-header" style="background: rgba(139, 92, 246, 0.1); border-radius: 15px 15px 0 0; border-bottom: 2px solid #8b5cf6;">
                                            <h5 class="mb-0" style="color: #8b5cf6; font-weight: 600;">
                                                <i class="fas fa-chart-bar me-2"></i>Estadísticas Generales
                                            </h5>
                                        </div>
                                        <div class="card-body p-4">
                                            <div class="row text-center">
                                                <div class="col-md-3">
                                                    <div class="stat-card p-3 rounded-3" style="background: linear-gradient(135deg, rgba(34, 197, 94, 0.1), rgba(34, 197, 94, 0.2)); border: 2px solid rgba(34, 197, 94, 0.3);">
                                                        <i class="fas fa-users fa-2x text-success mb-2"></i>
                                                        <div class="stat-number h3 mb-1 text-success" id="stat-usuarios-asignados">0</div>
                                                        <div class="stat-label text-muted">Usuarios Asignados</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="stat-card p-3 rounded-3" style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(59, 130, 246, 0.2)); border: 2px solid rgba(59, 130, 246, 0.3);">
                                                        <i class="fas fa-project-diagram fa-2x text-primary mb-2"></i>
                                                        <div class="stat-number h3 mb-1 text-primary" id="stat-proyectos-con-asignaciones">0</div>
                                                        <div class="stat-label text-muted">Proyectos Activos</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="stat-card p-3 rounded-3" style="background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(245, 158, 11, 0.2)); border: 2px solid rgba(245, 158, 11, 0.3);">
                                                        <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                                                        <div class="stat-number h3 mb-1 text-warning" id="stat-horas-trabajadas">0h</div>
                                                        <div class="stat-label text-muted">Horas Trabajadas</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="stat-card p-3 rounded-3" style="background: linear-gradient(135deg, rgba(139, 92, 246, 0.1), rgba(139, 92, 246, 0.2)); border: 2px solid rgba(139, 92, 246, 0.3);">
                                                        <i class="fas fa-user-check fa-2x" style="color: #8b5cf6;" class="mb-2"></i>
                                                        <div class="stat-number h3 mb-1" style="color: #8b5cf6;" id="stat-asignaciones-activas">0</div>
                                                        <div class="stat-label text-muted">Activas</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card shadow-sm border-0" style="border-radius: 15px;">
                                        <div class="card-header" style="background: rgba(139, 92, 246, 0.1); border-radius: 15px 15px 0 0; border-bottom: 2px solid #8b5cf6;">
                                            <h5 class="mb-0" style="color: #8b5cf6; font-weight: 600;">
                                                <i class="fas fa-user-tag me-2"></i>Roles de Proyecto
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="role-stats">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <span class="role-name">
                                                        <i class="fas fa-crown text-warning me-2"></i>Líderes
                                                    </span>
                                                    <span class="badge rounded-pill" style="background: #8b5cf6;" id="role-lideres">0</span>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <span class="role-name">
                                                        <i class="fas fa-code text-info me-2"></i>Desarrolladores
                                                    </span>
                                                    <span class="badge bg-info rounded-pill" id="role-desarrolladores">0</span>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <span class="role-name">
                                                        <i class="fas fa-user-tie text-success me-2"></i>Consultores
                                                    </span>
                                                    <span class="badge bg-success rounded-pill" id="role-consultores">0</span>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="role-name">
                                                        <i class="fas fa-search text-secondary me-2"></i>Revisores
                                                    </span>
                                                    <span class="badge bg-secondary rounded-pill" id="role-revisores">0</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Projects Tab -->
                        <div class="tab-pane" id="projects-content">
                            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                                <div class="card-header" style="background: rgba(139, 92, 246, 0.1); border-radius: 15px 15px 0 0; border-bottom: 2px solid #8b5cf6;">
                                    <h5 class="mb-0" style="color: #8b5cf6; font-weight: 600;">
                                        <i class="fas fa-project-diagram me-2"></i>Asignaciones por Proyecto
                                    </h5>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead style="background: #f8f9fa;">
                                                <tr>
                                                    <th class="border-0 p-3">Proyecto</th>
                                                    <th class="border-0 p-3">Estado</th>
                                                    <th class="border-0 p-3">Usuarios Asignados</th>
                                                    <th class="border-0 p-3">Roles</th>
                                                    <th class="border-0 p-3">Horas</th>
                                                    <th class="border-0 p-3">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody id="projects-table-body">
                                                <!-- Contenido dinámico -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Users Tab -->
                        <div class="tab-pane" id="users-content">
                            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                                <div class="card-header" style="background: rgba(139, 92, 246, 0.1); border-radius: 15px 15px 0 0; border-bottom: 2px solid #8b5cf6;">
                                    <h5 class="mb-0" style="color: #8b5cf6; font-weight: 600;">
                                        <i class="fas fa-users me-2"></i>Asignaciones por Usuario
                                    </h5>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead style="background: #f8f9fa;">
                                                <tr>
                                                    <th class="border-0 p-3">Usuario</th>
                                                    <th class="border-0 p-3">Email</th>
                                                    <th class="border-0 p-3">Proyectos Asignados</th>
                                                    <th class="border-0 p-3">Roles Principales</th>
                                                    <th class="border-0 p-3">Horas Totales</th>
                                                    <th class="border-0 p-3">Estado</th>
                                                </tr>
                                            </thead>
                                            <tbody id="users-table-body">
                                                <!-- Contenido dinámico -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `);

            // Cargar datos iniciales
            cargarEstadisticasAsignaciones();
            setupTabNavigation();
        }

        // Configurar navegación por tabs
        function setupTabNavigation() {
            $('.assignment-tab').on('click', function(e) {
                e.preventDefault();
                
                const tab = $(this).data('tab');
                
                // Actualizar tabs activos
                $('.assignment-tab').removeClass('active').css('color', '#6b7280');
                $(this).addClass('active').css('color', '#8b5cf6');
                
                // Mostrar contenido del tab
                $('.tab-pane').removeClass('active');
                $(`#${tab}-content`).addClass('active');
                
                // Cargar datos específicos del tab
                switch(tab) {
                    case 'projects':
                        cargarAsignacionesPorProyecto();
                        break;
                    case 'users':
                        cargarAsignacionesPorUsuario();
                        break;
                }
            });
        }

        // Cargar estadísticas generales
        function cargarEstadisticasAsignaciones() {
            console.log('Cargando estadísticas desde: /octocodex/condor/php/asignaciones_proyectos.php');
            $.ajax({
                url: '/octocodex/condor/php/asignaciones_proyectos.php',
                method: 'GET',
                data: { accion: 'obtener_estadisticas' },
                dataType: 'json',
                xhrFields: {
                    withCredentials: true
                },
                crossDomain: false,
                success: function(response) {
                    if (response.exito) {
                        const stats = response.estadisticas;
                        $('#stat-usuarios-asignados').text(stats.usuarios_asignados || 0);
                        $('#stat-proyectos-con-asignaciones').text(stats.proyectos_con_asignaciones || 0);
                        $('#stat-horas-trabajadas').text((stats.total_horas_trabajadas || 0) + 'h');
                        $('#stat-asignaciones-activas').text(stats.asignaciones_activas || 0);
                        
                        // Roles
                        $('#role-lideres').text(stats.lideres_proyecto || 0);
                        $('#role-desarrolladores').text(stats.desarrolladores || 0);
                        $('#role-consultores').text(stats.consultores || 0);
                        $('#role-revisores').text(stats.revisores || 0);
                        
                        // Stats mini
                        $('#total-asignaciones').text(stats.total_asignaciones || 0);
                        $('#total-usuarios').text(stats.usuarios_asignados || 0);
                        $('#total-proyectos').text(stats.proyectos_con_asignaciones || 0);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error al cargar estadísticas:', error);
                }
            });
        }

        // Cargar asignaciones por proyecto
        function cargarAsignacionesPorProyecto() {
            console.log('Cargando proyectos desde: /octocodex/condor/php/asignaciones_proyectos.php');
            $('#projects-table-body').html(`
                <tr>
                    <td colspan="6" class="text-center p-4">
                        <div class="d-flex justify-content-center align-items-center">
                            <div class="spinner-border" style="color: #8b5cf6;" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                            <span class="ms-3">Cargando asignaciones por proyecto...</span>
                        </div>
                    </td>
                </tr>
            `);

            $.ajax({
                url: '/octocodex/condor/php/asignaciones_proyectos.php',
                method: 'GET',
                data: { accion: 'listar_proyectos_con_asignaciones' },
                dataType: 'json',
                xhrFields: {
                    withCredentials: true
                },
                crossDomain: false,
                success: function(response) {
                    if (response.exito) {
                        mostrarProyectosConAsignaciones(response.proyectos);
                    } else {
                        $('#projects-table-body').html(`
                            <tr>
                                <td colspan="6" class="text-center p-4">
                                    <div class="alert alert-warning mb-0">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        ${response.mensaje || 'No se pudieron cargar los proyectos'}
                                    </div>
                                </td>
                            </tr>
                        `);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error al cargar proyectos:', error);
                    console.error('Status:', status);
                    console.error('Response:', xhr.responseText);
                    let errorMsg = 'Error al conectar con el servidor';
                    
                    if (xhr.responseJSON && xhr.responseJSON.mensaje) {
                        errorMsg = xhr.responseJSON.mensaje;
                    } else if (xhr.responseText) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            errorMsg = response.mensaje || response.error || errorMsg;
                        } catch (e) {
                            errorMsg = `Error ${xhr.status}: ${xhr.statusText}`;
                        }
                    }
                    
                    $('#projects-table-body').html(`
                        <tr>
                            <td colspan="6" class="text-center p-4">
                                <div class="alert alert-danger mb-0">
                                    <i class="fas fa-times-circle me-2"></i>
                                    ${errorMsg}
                                </div>
                            </td>
                        </tr>
                    `);
                }
            });
        }

        // Mostrar proyectos con asignaciones
        function mostrarProyectosConAsignaciones(proyectos) {
            if (!proyectos || proyectos.length === 0) {
                $('#projects-table-body').html(`
                    <tr>
                        <td colspan="6" class="text-center p-4">
                            <div class="text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                <h5>No hay proyectos con asignaciones</h5>
                                <p>Los proyectos aparecerán aquí cuando tengan usuarios asignados</p>
                            </div>
                        </td>
                    </tr>
                `);
                return;
            }

            let html = '';
            proyectos.forEach(proyecto => {
                const estadoBadge = obtenerBadgeEstado(proyecto.proyecto_estado);
                const prioridadBadge = obtenerBadgePrioridad(proyecto.proyecto_prioridad);
                
                html += `
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="project-icon me-3" style="width: 40px; height: 40px; background: var(--gradiente-violeta); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white;">
                                    <i class="fas fa-project-diagram"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">${proyecto.proyecto_titulo}</h6>
                                    <small class="text-muted">ID: ${proyecto.proyecto_id}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                ${estadoBadge}
                                ${prioridadBadge}
                            </div>
                        </td>
                        <td>
                            <div class="user-avatars d-flex">
                                ${generarAvatarsUsuarios(proyecto.asignaciones_detalle)}
                            </div>
                            <small class="text-muted">${proyecto.total_asignados} usuario(s)</small>
                        </td>
                        <td>
                            <div class="roles-container">
                                ${generarBadgesRoles(proyecto.roles_proyecto)}
                            </div>
                        </td>
                        <td>
                            <div class="hours-info">
                                <div class="d-flex justify-content-between">
                                    <small>Asignadas:</small>
                                    <strong>${proyecto.total_horas_asignadas || 0}h</strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <small>Trabajadas:</small>
                                    <strong style="color: #8b5cf6;">${proyecto.total_horas_trabajadas || 0}h</strong>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                        onclick="verDetalleProyecto(${proyecto.proyecto_id})" 
                                        title="Ver detalle">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-success" 
                                        onclick="gestionarAsignacionesProyecto(${proyecto.proyecto_id})" 
                                        title="Gestionar asignaciones">
                                    <i class="fas fa-users-cog"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
            
            $('#projects-table-body').html(html);
        }

        // Cargar asignaciones por usuario
        function cargarAsignacionesPorUsuario() {
            console.log('Cargando usuarios desde: /octocodex/condor/php/asignaciones_proyectos.php');
            $('#users-table-body').html(`
                <tr>
                    <td colspan="6" class="text-center p-4">
                        <div class="d-flex justify-content-center align-items-center">
                            <div class="spinner-border" style="color: #8b5cf6;" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                            <span class="ms-3">Cargando asignaciones por usuario...</span>
                        </div>
                    </td>
                </tr>
            `);

            $.ajax({
                url: '/octocodex/condor/php/asignaciones_proyectos.php',
                method: 'GET',
                data: { accion: 'listar_usuarios_con_asignaciones' },
                dataType: 'json',
                xhrFields: {
                    withCredentials: true
                },
                crossDomain: false,
                success: function(response) {
                    if (response.exito) {
                        mostrarUsuariosConAsignaciones(response.usuarios);
                    } else {
                        $('#users-table-body').html(`
                            <tr>
                                <td colspan="6" class="text-center p-4">
                                    <div class="alert alert-warning mb-0">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        ${response.mensaje || 'No se pudieron cargar los usuarios'}
                                    </div>
                                </td>
                            </tr>
                        `);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error al cargar usuarios:', error);
                    console.error('Status:', status);
                    console.error('Response:', xhr.responseText);
                    let errorMsg = 'Error al conectar con el servidor';
                    
                    if (xhr.responseJSON && xhr.responseJSON.mensaje) {
                        errorMsg = xhr.responseJSON.mensaje;
                    } else if (xhr.responseText) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            errorMsg = response.mensaje || response.error || errorMsg;
                        } catch (e) {
                            errorMsg = `Error ${xhr.status}: ${xhr.statusText}`;
                        }
                    }
                    
                    $('#users-table-body').html(`
                        <tr>
                            <td colspan="6" class="text-center p-4">
                                <div class="alert alert-danger mb-0">
                                    <i class="fas fa-times-circle me-2"></i>
                                    ${errorMsg}
                                </div>
                            </td>
                        </tr>
                    `);
                }
            });
        }

        // Mostrar usuarios con asignaciones
        function mostrarUsuariosConAsignaciones(usuarios) {
            if (!usuarios || usuarios.length === 0) {
                $('#users-table-body').html(`
                    <tr>
                        <td colspan="6" class="text-center p-4">
                            <div class="text-muted">
                                <i class="fas fa-users fa-3x mb-3 d-block"></i>
                                <h5>No hay usuarios con asignaciones</h5>
                                <p>Los usuarios aparecerán aquí cuando tengan proyectos asignados</p>
                            </div>
                        </td>
                    </tr>
                `);
                return;
            }

            let html = '';
            usuarios.forEach(usuario => {
                const avatar = generarAvatarUsuario(usuario);
                const estadoBadge = usuario.asignaciones_activas > 0 
                    ? '<span class="badge bg-success">Activo</span>' 
                    : '<span class="badge bg-secondary">Inactivo</span>';
                
                html += `
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                ${avatar}
                                <div class="ms-3">
                                    <h6 class="mb-1">${usuario.nombre_completo}</h6>
                                    <small class="text-muted">ID: ${usuario.usuario_id} • ${usuario.rol_sistema}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="email-info">
                                <i class="fas fa-envelope me-2 text-muted"></i>
                                <span>${usuario.us_email}</span>
                            </div>
                        </td>
                        <td>
                            <div class="projects-info">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="badge" style="background: #8b5cf6;">${usuario.total_proyectos_asignados}</span>
                                    <span class="ms-2">proyecto(s)</span>
                                </div>
                                <small class="text-muted" title="${usuario.proyectos_asignados}">
                                    ${truncarTexto(usuario.proyectos_asignados, 30)}
                                </small>
                            </div>
                        </td>
                        <td>
                            <div class="roles-container">
                                ${generarBadgesRoles(usuario.roles_principales)}
                            </div>
                        </td>
                        <td>
                            <div class="hours-info">
                                <div class="d-flex justify-content-between">
                                    <small>Asignadas:</small>
                                    <strong>${usuario.total_horas_asignadas || 0}h</strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <small>Trabajadas:</small>
                                    <strong style="color: #8b5cf6;">${usuario.total_horas_trabajadas || 0}h</strong>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column align-items-center">
                                ${estadoBadge}
                                <div class="btn-group mt-2" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                            onclick="verDetalleUsuario(${usuario.usuario_id})" 
                                            title="Ver detalle">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-success" 
                                            onclick="gestionarAsignacionesUsuario(${usuario.usuario_id})" 
                                            title="Gestionar asignaciones">
                                        <i class="fas fa-tasks"></i>
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                `;
            });
            
            $('#users-table-body').html(html);
        }

        // ===== FUNCIONES AUXILIARES PARA FORMATEO =====

        // Generar badge de estado
        function obtenerBadgeEstado(estado) {
            const estados = {
                'activo': '<span class="badge bg-success">Activo</span>',
                'completado': '<span class="badge bg-primary">Completado</span>',
                'pausado': '<span class="badge bg-warning">Pausado</span>',
                'cancelado': '<span class="badge bg-danger">Cancelado</span>',
                'planificacion': '<span class="badge bg-info">Planificación</span>',
                'desarrollo': '<span class="badge bg-warning">Desarrollo</span>',
                'testing': '<span class="badge bg-secondary">Testing</span>',
                'produccion': '<span class="badge bg-success">Producción</span>'
            };
            return estados[estado] || '<span class="badge bg-secondary">' + (estado || 'N/A') + '</span>';
        }

        // Generar badge de prioridad
        function obtenerBadgePrioridad(prioridad) {
            const prioridades = {
                'alta': '<span class="badge" style="background: #dc3545;">Alta</span>',
                'media': '<span class="badge" style="background: #ffc107; color: #000;">Media</span>',
                'baja': '<span class="badge" style="background: #28a745;">Baja</span>',
                'critica': '<span class="badge" style="background: #6f42c1;">Crítica</span>'
            };
            return prioridades[prioridad] || '<span class="badge bg-secondary">' + (prioridad || 'N/A') + '</span>';
        }

        // Generar avatars de usuarios
        function generarAvatarsUsuarios(asignaciones) {
            if (!asignaciones || asignaciones.length === 0) {
                return '<small class="text-muted">Sin asignaciones</small>';
            }

            let html = '';
            const maxAvatars = 3;
            
            for (let i = 0; i < Math.min(asignaciones.length, maxAvatars); i++) {
                const asignacion = asignaciones[i];
                const iniciales = obtenerIniciales(asignacion.nombre_completo);
                const colorRol = obtenerColorRol(asignacion.rol_proyecto);
                
                html += `
                    <div class="user-avatar me-1" 
                         style="width: 35px; height: 35px; background: ${colorRol}; border-radius: 50%; 
                                display: flex; align-items: center; justify-content: center; 
                                color: white; font-size: 12px; font-weight: 600; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"
                         title="${asignacion.nombre_completo} (${asignacion.rol_proyecto})">
                        ${iniciales}
                    </div>
                `;
            }
            
            if (asignaciones.length > maxAvatars) {
                const restantes = asignaciones.length - maxAvatars;
                html += `
                    <div class="user-avatar" 
                         style="width: 35px; height: 35px; background: #6c757d; border-radius: 50%; 
                                display: flex; align-items: center; justify-content: center; 
                                color: white; font-size: 11px; font-weight: 600; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"
                         title="${restantes} usuario(s) más">
                        +${restantes}
                    </div>
                `;
            }
            
            return html;
        }

        // Generar avatar de usuario individual
        function generarAvatarUsuario(usuario) {
            const iniciales = obtenerIniciales(usuario.nombre_completo);
            const colorAvatar = generarColorAvatar(usuario.usuario_id);
            
            return `
                <div class="user-avatar-large" 
                     style="width: 45px; height: 45px; background: ${colorAvatar}; border-radius: 50%; 
                            display: flex; align-items: center; justify-content: center; 
                            color: white; font-size: 16px; font-weight: 700; box-shadow: 0 4px 8px rgba(0,0,0,0.15);">
                    ${iniciales}
                </div>
            `;
        }

        // Generar badges de roles
        function generarBadgesRoles(roles) {
            if (!roles) return '<small class="text-muted">Sin roles</small>';
            
            const rolesArray = roles.split(', ').filter(rol => rol.trim());
            let html = '';
            
            rolesArray.forEach(rol => {
                const colorRol = obtenerColorRol(rol.trim());
                const iconoRol = obtenerIconoRol(rol.trim());
                
                html += `
                    <span class="badge me-1 mb-1" style="background: ${colorRol};">
                        <i class="${iconoRol} me-1"></i>${rol.trim()}
                    </span>
                `;
            });
            
            return html;
        }

        // Obtener iniciales del nombre
        function obtenerIniciales(nombreCompleto) {
            if (!nombreCompleto) return '??';
            
            const nombres = nombreCompleto.trim().split(' ');
            if (nombres.length >= 2) {
                return (nombres[0].charAt(0) + nombres[1].charAt(0)).toUpperCase();
            } else {
                return nombres[0].substring(0, 2).toUpperCase();
            }
        }

        // Obtener color por rol
        function obtenerColorRol(rol) {
            const colores = {
                'lider': '#8b5cf6',
                'desarrollador': '#3b82f6',
                'consultor': '#10b981',
                'revisor': '#6b7280',
                'colaborador': '#f59e0b'
            };
            return colores[rol] || '#6b7280';
        }

        // Obtener ícono por rol
        function obtenerIconoRol(rol) {
            const iconos = {
                'lider': 'fas fa-crown',
                'desarrollador': 'fas fa-code',
                'consultor': 'fas fa-user-tie',
                'revisor': 'fas fa-search',
                'colaborador': 'fas fa-user'
            };
            return iconos[rol] || 'fas fa-user';
        }

        // Generar color de avatar basado en ID
        function generarColorAvatar(id) {
            const colores = [
                '#8b5cf6', '#3b82f6', '#10b981', '#f59e0b', 
                '#ef4444', '#8b5cf6', '#06b6d4', '#84cc16',
                '#f97316', '#ec4899', '#6366f1', '#14b8a6'
            ];
            return colores[id % colores.length];
        }

        // Truncar texto
        function truncarTexto(texto, longitud) {
            if (!texto) return 'N/A';
            return texto.length > longitud ? texto.substring(0, longitud) + '...' : texto;
        }

        // ===== FUNCIONES DE GESTIÓN =====

        // Ver detalle de proyecto
        function verDetalleProyecto(proyectoId) {
            console.log('Ver detalle del proyecto:', proyectoId);
            // Aquí se implementaría el modal de detalle del proyecto
            alert('Funcionalidad en desarrollo: Ver detalle del proyecto ' + proyectoId);
        }

        // Gestionar asignaciones de proyecto
        function gestionarAsignacionesProyecto(proyectoId) {
            console.log('Gestionar asignaciones del proyecto:', proyectoId);
            window.currentEntityId = proyectoId;
            window.currentEntityType = 'proyecto';
            
            $('#modal-title-text').html('<i class="fas fa-project-diagram me-2"></i>Gestionar Asignaciones - Proyecto #' + proyectoId);
            cargarAsignacionesModal(proyectoId, 'proyecto');
            cargarSelectoresModal();
            
            const modal = new bootstrap.Modal(document.getElementById('modalGestionAsignaciones'));
            modal.show();
        }

        // Ver detalle de usuario
        function verDetalleUsuario(usuarioId) {
            console.log('Ver detalle del usuario:', usuarioId);
            alert('Funcionalidad en desarrollo: Ver detalle del usuario ' + usuarioId);
        }

        // Gestionar asignaciones de usuario
        function gestionarAsignacionesUsuario(usuarioId) {
            console.log('Gestionar asignaciones del usuario:', usuarioId);
            window.currentEntityId = usuarioId;
            window.currentEntityType = 'usuario';
            
            $('#modal-title-text').html('<i class="fas fa-user me-2"></i>Gestionar Asignaciones - Usuario #' + usuarioId);
            cargarAsignacionesModal(usuarioId, 'usuario');
            cargarSelectoresModal();
            
            const modal = new bootstrap.Modal(document.getElementById('modalGestionAsignaciones'));
            modal.show();
        }

        // ===== FUNCIONES DEL MODAL DE GESTIÓN =====

        // Configurar navegación del modal
        function setupModalNavigation() {
            $('.modal-tab').on('click', function(e) {
                e.preventDefault();
                
                const tab = $(this).data('tab');
                
                // Actualizar tabs activos
                $('.modal-tab').removeClass('active').css('color', '#6b7280');
                $(this).addClass('active').css('color', '#8b5cf6');
                
                // Mostrar contenido del tab
                $('.tab-pane').removeClass('active');
                $(`#${tab}-assignments`).addClass('active');
                
                // Mostrar/ocultar botón de guardar según el tab
                if (tab === 'add') {
                    $('#btnGuardarAsignacion').show();
                } else {
                    $('#btnGuardarAsignacion').hide();
                }
            });
        }

        // Cargar asignaciones en el modal
        function cargarAsignacionesModal(entityId, entityType) {
            $('#current-assignments-body').html(`
                <tr>
                    <td colspan="6" class="text-center p-4">
                        <div class="d-flex justify-content-center align-items-center">
                            <div class="spinner-border" style="color: #8b5cf6;" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                            <span class="ms-3">Cargando asignaciones...</span>
                        </div>
                    </td>
                </tr>
            `);

            let url, params;
            if (entityType === 'proyecto') {
                url = '/octocodex/condor/php/asignaciones_proyectos.php';
                params = { accion: 'obtener_asignaciones', proyecto_id: entityId };
            } else {
                url = '/octocodex/condor/php/asignaciones_proyectos.php';
                params = { accion: 'obtener_asignaciones_usuario', usuario_id: entityId };
            }

            $.ajax({
                url: url,
                method: 'GET',
                data: params,
                dataType: 'json',
                xhrFields: {
                    withCredentials: true
                },
                crossDomain: false,
                success: function(response) {
                    if (response.exito) {
                        mostrarAsignacionesModal(response.asignaciones || []);
                    } else {
                        $('#current-assignments-body').html(`
                            <tr>
                                <td colspan="6" class="text-center p-4">
                                    <div class="alert alert-warning mb-0">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        No se pudieron cargar las asignaciones
                                    </div>
                                </td>
                            </tr>
                        `);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error al cargar asignaciones:', error);
                    $('#current-assignments-body').html(`
                        <tr>
                            <td colspan="6" class="text-center p-4">
                                <div class="alert alert-danger mb-0">
                                    <i class="fas fa-times-circle me-2"></i>
                                    Error al conectar con el servidor
                                </div>
                            </td>
                        </tr>
                    `);
                }
            });
        }

        // Mostrar asignaciones en la tabla del modal
        function mostrarAsignacionesModal(asignaciones) {
            if (!asignaciones || asignaciones.length === 0) {
                $('#current-assignments-body').html(`
                    <tr>
                        <td colspan="6" class="text-center p-4">
                            <div class="text-muted">
                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                <p>No hay asignaciones para mostrar</p>
                            </div>
                        </td>
                    </tr>
                `);
                return;
            }

            let html = '';
            asignaciones.forEach(asignacion => {
                const estadoBadge = obtenerBadgeEstado(asignacion.estado_asignacion);
                const rolBadge = `<span class="badge" style="background: ${obtenerColorRol(asignacion.rol_proyecto)};">
                    <i class="${obtenerIconoRol(asignacion.rol_proyecto)} me-1"></i>${asignacion.rol_proyecto}
                </span>`;
                
                html += `
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                ${generarAvatarUsuario({ 
                                    nombre_completo: asignacion.nombre_completo || asignacion.proyecto_titulo, 
                                    usuario_id: asignacion.asignacion_id 
                                })}
                                <div class="ms-3">
                                    <h6 class="mb-1">${asignacion.nombre_completo || asignacion.proyecto_titulo}</h6>
                                    <small class="text-muted">${asignacion.usuario_email || 'ID: ' + asignacion.proyecto_id}</small>
                                </div>
                            </div>
                        </td>
                        <td>${rolBadge}</td>
                        <td>${estadoBadge}</td>
                        <td>
                            <div class="hours-info">
                                <small>A: ${asignacion.horas_asignadas || 0}h</small><br>
                                <small style="color: #8b5cf6;">T: ${asignacion.horas_trabajadas || 0}h</small>
                            </div>
                        </td>
                        <td>
                            <small>${formatearFecha(asignacion.fecha_asignacion)}</small>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                        onclick="editarAsignacion(${asignacion.asignacion_id})" 
                                        title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                        onclick="eliminarAsignacion(${asignacion.asignacion_id})" 
                                        title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
            
            $('#current-assignments-body').html(html);
        }

        // Cargar selectores del modal
        function cargarSelectoresModal() {
            // Cargar proyectos
            $.ajax({
                url: '/octocodex/condor/php/abm_proyectos.php',
                method: 'GET',
                data: { accion: 'listar' },
                dataType: 'json',
                xhrFields: {
                    withCredentials: true
                },
                crossDomain: false,
                success: function(response) {
                    if (response.exito) {
                        let options = '<option value="">Seleccione un proyecto...</option>';
                        response.proyectos.forEach(proyecto => {
                            options += `<option value="${proyecto.id}">${proyecto.pr_titulo}</option>`;
                        });
                        $('#proyecto-select').html(options);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error al cargar proyectos:', error);
                }
            });

            // Cargar usuarios
            $.ajax({
                url: '/octocodex/condor/php/usuarios.php',
                method: 'GET',
                dataType: 'json',
                xhrFields: {
                    withCredentials: true
                },
                crossDomain: false,
                success: function(usuarios) {
                    if (Array.isArray(usuarios)) {
                        let options = '<option value="">Seleccione un usuario...</option>';
                        usuarios.forEach(usuario => {
                            if (usuario.us_activo == 1) {
                                options += `<option value="${usuario.id}">${usuario.us_nombre} ${usuario.us_apellido}</option>`;
                            }
                        });
                        $('#usuario-select').html(options);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error al cargar usuarios:', error);
                }
            });
        }

        // Formatear fecha
        function formatearFecha(fecha) {
            if (!fecha) return 'N/A';
            const date = new Date(fecha);
            return date.toLocaleDateString('es-ES');
        }

        // Editar asignación
        function editarAsignacion(asignacionId) {
            console.log('Editar asignación:', asignacionId);
            alert('Funcionalidad en desarrollo: Editar asignación ' + asignacionId);
        }

        // Eliminar asignación
        function eliminarAsignacion(asignacionId) {
            if (confirm('¿Está seguro de que desea eliminar esta asignación?')) {
                $.ajax({
                    url: '/octocodex/condor/php/asignaciones_proyectos.php',
                    method: 'POST',
                    data: { 
                        accion: 'eliminar_asignacion',
                        asignacion_id: asignacionId
                    },
                    dataType: 'json',
                    xhrFields: {
                        withCredentials: true
                    },
                    success: function(response) {
                        if (response.exito) {
                            alert('Asignación eliminada exitosamente');
                            // Recargar las asignaciones
                            cargarAsignacionesModal(window.currentEntityId, window.currentEntityType);
                            // Actualizar estadísticas
                            cargarEstadisticasAsignaciones();
                        } else {
                            alert('Error: ' + response.mensaje);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error al eliminar asignación:', error);
                        alert('Error al conectar con el servidor');
                    }
                });
            }
        }

        // Guardar nueva asignación
        $('#btnGuardarAsignacion').on('click', function() {
            const formData = new FormData(document.getElementById('formNuevaAsignacion'));
            formData.append('accion', 'asignar_usuario');

            $.ajax({
                url: '/octocodex/condor/php/asignaciones_proyectos.php',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                xhrFields: {
                    withCredentials: true
                },
                crossDomain: false,
                success: function(response) {
                    if (response.exito) {
                        alert('Asignación creada exitosamente');
                        // Limpiar formulario
                        document.getElementById('formNuevaAsignacion').reset();
                        // Cambiar al tab de asignaciones actuales
                        $('.modal-tab[data-tab="current"]').click();
                        // Recargar las asignaciones
                        cargarAsignacionesModal(window.currentEntityId, window.currentEntityType);
                        // Actualizar estadísticas
                        cargarEstadisticasAsignaciones();
                    } else {
                        alert('Error: ' + response.mensaje);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error al crear asignación:', error);
                    alert('Error al conectar con el servidor');
                }
            });
        });

        // Inicializar eventos del modal
        $(document).ready(function() {
            setupModalNavigation();
        });

        // Event listener para el menú de asignaciones
        $('#asignaciones-btn').on('click', function(e) {
            e.preventDefault();
            mostrarAsignaciones();
        });

        // Script para el dashboard
        console.log('Dashboard cargado correctamente');
        // Cargar el contenido de construccion.php en el div construccion
        $(document).ready(function() {
            $.ajax({
                url: 'construccion.php',
                method: 'GET',
                success: function(response) {
                    $('.construccion').html(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error al cargar construccion.php:', error);
                    $('.construccion').html('<div class="alert alert-danger">Error al cargar el contenido</div>');
                }
            });
        });
        // Hacer el user_id disponible globalmente para otros scripts
        window.userId = <?php echo $user_id; ?>;
        
        // También agregarlo como data attribute al body
        $('body').attr('data-user-id', <?php echo $user_id; ?>);
        
        // Mostrar información del usuario en consola
        console.log('Usuario logueado:', {
            id: <?php echo $user_id; ?>,
            username: '<?php echo $username; ?>',
            rol: '<?php echo $rol; ?>',
            email: '<?php echo $email; ?>'
        });
    </script>

    <!-- Modal para Cambiar Contraseña -->
    <div class="modal fade" id="modalCambiarPassword" tabindex="-1" aria-labelledby="modalCambiarPasswordLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title" id="modalCambiarPasswordLabel">
                        <i class="fas fa-key me-2"></i>
                        Cambiar Contraseña
                    </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    <form id="formCambiarPassword">
                        <!-- Contraseña Actual -->
                        <div class="form-group mb-3">
                            <label class="form-label" for="passwordActual">
                                <img src="../icons/16x/candado16.png" alt="Contraseña Actual" style="vertical-align: middle; margin-right: 10px; width: 16px; height: 16px;">
                                Contraseña Actual <span style="color: red;">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password" id="passwordActual" name="passwordActual" class="form-control" 
                                    placeholder="Ingrese su contraseña actual" required>
                                <button type="button" class="btn btn-outline-secondary toggle-password" data-target="passwordActual">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="error-message" id="error-passwordActual" style="display: none;">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                La contraseña actual es requerida
                            </div>
                        </div>

                        <!-- Nueva Contraseña -->
                        <div class="form-group mb-3">
                            <label class="form-label" for="passwordNueva">
                                <img src="../icons/16x/llave16.png" alt="Nueva Contraseña" style="vertical-align: middle; margin-right: 10px; width: 16px; height: 16px;">
                                Nueva Contraseña <span style="color: red;">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password" id="passwordNueva" name="passwordNueva" class="form-control" 
                                    placeholder="Ingrese la nueva contraseña (mínimo 6 caracteres)" required>
                                <button type="button" class="btn btn-outline-secondary toggle-password" data-target="passwordNueva">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="error-message" id="error-passwordNueva" style="display: none;">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                La nueva contraseña debe tener al menos 6 caracteres
                            </div>
                        </div>

                        <!-- Confirmar Contraseña -->
                        <div class="form-group mb-3">
                            <label class="form-label" for="passwordConfirmar">
                                <img src="../icons/16x/confirma16.png" alt="Confirmar Contraseña" style="vertical-align: middle; margin-right: 10px; width: 16px; height: 16px;">
                                Confirmar Nueva Contraseña <span style="color: red;">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password" id="passwordConfirmar" name="passwordConfirmar" class="form-control" 
                                    placeholder="Confirme la nueva contraseña" required>
                                <button type="button" class="btn btn-outline-secondary toggle-password" data-target="passwordConfirmar">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="error-message" id="error-passwordConfirmar" style="display: none;">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                Las contraseñas no coinciden
                            </div>
                        </div>

                        <!-- Consejos de Seguridad -->
                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Consejos de Seguridad:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Use al menos 6 caracteres</li>
                                <li>Combine letras mayúsculas y minúsculas</li>
                                <li>Incluya números y símbolos</li>
                                <li>Evite usar información personal</li>
                            </ul>
                        </div>
                    </form>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnCambiarPassword" form="formCambiarPassword">
                        <i class="fas fa-save me-2"></i>
                        Cambiar Contraseña
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Gestión de Asignaciones -->
    <div class="modal fade" id="modalGestionAsignaciones" tabindex="-1" aria-labelledby="modalGestionAsignacionesLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header" style="background: var(--gradiente-violeta); color: white;">
                    <h4 class="modal-title" id="modalGestionAsignacionesLabel">
                        <i class="fas fa-users-cog me-2"></i>
                        <span id="modal-title-text">Gestionar Asignaciones</span>
                    </h4>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body p-0">
                    <!-- Tabs Navigation -->
                    <ul class="nav nav-tabs" style="background: #f8f9fa; border-bottom: 2px solid #8b5cf6;">
                        <li class="nav-item">
                            <button class="nav-link active modal-tab" data-tab="current" style="color: #8b5cf6; border-color: transparent;">
                                <i class="fas fa-list me-2"></i>Asignaciones Actuales
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link modal-tab" data-tab="add" style="color: #6b7280; border-color: transparent;">
                                <i class="fas fa-plus me-2"></i>Nueva Asignación
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link modal-tab" data-tab="history" style="color: #6b7280; border-color: transparent;">
                                <i class="fas fa-history me-2"></i>Historial
                            </button>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content p-4">
                        <!-- Current Assignments Tab -->
                        <div class="tab-pane active" id="current-assignments">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead style="background: rgba(139, 92, 246, 0.1);">
                                        <tr>
                                            <th>Usuario/Proyecto</th>
                                            <th>Rol</th>
                                            <th>Estado</th>
                                            <th>Horas</th>
                                            <th>Fecha Asignación</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="current-assignments-body">
                                        <!-- Contenido dinámico -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Add Assignment Tab -->
                        <div class="tab-pane" id="add-assignment">
                            <form id="formNuevaAsignacion">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Proyecto</label>
                                            <select class="form-select" id="proyecto-select" name="proyecto_id" required>
                                                <option value="">Seleccione un proyecto...</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Usuario</label>
                                            <select class="form-select" id="usuario-select" name="usuario_id" required>
                                                <option value="">Seleccione un usuario...</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Rol en el Proyecto</label>
                                            <select class="form-select" name="rol_proyecto" required>
                                                <option value="">Seleccione un rol...</option>
                                                <option value="lider">Líder</option>
                                                <option value="desarrollador">Desarrollador</option>
                                                <option value="consultor">Consultor</option>
                                                <option value="revisor">Revisor</option>
                                                <option value="colaborador">Colaborador</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Horas Asignadas</label>
                                            <input type="number" class="form-control" name="horas_asignadas" min="0" step="0.5" placeholder="Ej: 40">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Fecha de Inicio</label>
                                            <input type="date" class="form-control" name="fecha_inicio">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Notas</label>
                                            <textarea class="form-control" name="notas" rows="3" placeholder="Notas adicionales sobre la asignación"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- History Tab -->
                        <div class="tab-pane" id="history-assignments">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Historial de cambios en las asignaciones
                            </div>
                            <div id="history-content">
                                <!-- Contenido del historial -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer" style="background: #f8f9fa;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cerrar
                    </button>
                    <button type="button" class="btn btn-primary" id="btnGuardarAsignacion" style="background: var(--gradiente-violeta); border: none;">
                        <i class="fas fa-save me-2"></i>Guardar Asignación
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Estilos específicos para el modal de cambiar contraseña */
        #modalCambiarPassword .modal-content {
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            border: none;
        }

        #modalCambiarPassword .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #8b5cf6 100%);
            color: white;
            border-radius: 12px 12px 0 0;
            border-bottom: none;
            padding: 1.5rem;
        }

        #modalCambiarPassword .modal-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
        }

        #modalCambiarPassword .modal-body {
            padding: 2rem;
            background: #f8f9fa;
        }

        #modalCambiarPassword .modal-footer {
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
            padding: 1.5rem;
            border-radius: 0 0 12px 12px;
        }

        #modalCambiarPassword .form-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
        }

        #modalCambiarPassword .form-control {
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 0.75rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: white;
        }

        #modalCambiarPassword .form-control:focus {
            border-color: #8b5cf6;
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
            outline: none;
        }

        #modalCambiarPassword .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        #modalCambiarPassword .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #8b5cf6 100%);
            color: white;
        }

        #modalCambiarPassword .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(139, 92, 246, 0.3);
        }

        #modalCambiarPassword .btn-secondary {
            background: #6c757d;
            color: white;
        }

        #modalCambiarPassword .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

        #modalCambiarPassword .error-message {
            color: #ef4444;
            font-size: 0.85rem;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        #modalCambiarPassword .toggle-password {
            border-left: none;
        }

        #modalCambiarPassword .toggle-password:hover {
            background: #e9ecef;
        }

        /* Responsive */
        @media (max-width: 768px) {
            #modalCambiarPassword .modal-dialog {
                margin: 0.5rem;
            }
            
            #modalCambiarPassword .modal-body {
                padding: 1rem;
            }
            
            #modalCambiarPassword .modal-header {
                padding: 1rem;
            }
            
            #modalCambiarPassword .modal-footer {
                padding: 1rem;
            }
        }

        /* Estilos específicos para el sistema de asignaciones */
        .assignments-container .tab-pane {
            display: none;
        }

        .assignments-container .tab-pane.active {
            display: block;
        }

        .assignments-container .assignment-tab:hover {
            color: #8b5cf6 !important;
            background-color: rgba(139, 92, 246, 0.1);
        }

        .assignments-container .stat-card {
            transition: all 0.3s ease;
        }

        .assignments-container .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .assignments-container .role-stats .role-name {
            font-weight: 500;
            color: #374151;
        }

        .assignments-container .table th {
            font-weight: 600;
            color: #374151;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .assignments-container .table td {
            vertical-align: middle;
            padding: 1rem 0.75rem;
        }

        .assignments-container .table-hover tbody tr:hover {
            background-color: rgba(139, 92, 246, 0.05);
        }

        .assignments-container .spinner-border {
            width: 1.5rem;
            height: 1.5rem;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .assignments-container .card {
            animation: fadeInUp 0.6s ease;
        }

        .assignments-container .stat-card {
            animation: fadeInUp 0.6s ease;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .assignments-container .header-section {
                padding: 1.5rem;
                text-align: center;
            }

            .assignments-container .header-section h1 {
                font-size: 1.8rem;
            }

            .assignments-container .stats-mini {
                justify-content: center;
                margin-top: 1rem;
            }

            .assignments-container .stat-item {
                margin: 0 0.5rem;
            }

            .assignments-container .nav-tabs {
                font-size: 0.9rem;
            }

            .assignments-container .table {
                font-size: 0.85rem;
            }
        }

        /* Estilos para el modal de gestión de asignaciones */
        #modalGestionAsignaciones .modal-content {
            border-radius: 15px;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.2);
            border: none;
        }

        #modalGestionAsignaciones .modal-header {
            border-radius: 15px 15px 0 0;
            border-bottom: none;
            padding: 1.5rem 2rem;
        }

        #modalGestionAsignaciones .modal-body {
            padding: 0;
        }

        #modalGestionAsignaciones .modal-footer {
            border-top: 1px solid #e9ecef;
            padding: 1.5rem 2rem;
            border-radius: 0 0 15px 15px;
        }

        #modalGestionAsignaciones .nav-tabs {
            border-bottom: none;
            margin-bottom: 0;
        }

        #modalGestionAsignaciones .nav-tabs .nav-link {
            border: none;
            padding: 1rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        #modalGestionAsignaciones .nav-tabs .nav-link:hover {
            background-color: rgba(139, 92, 246, 0.1);
            color: #8b5cf6 !important;
        }

        #modalGestionAsignaciones .nav-tabs .nav-link.active {
            background-color: rgba(139, 92, 246, 0.1);
            color: #8b5cf6 !important;
            border-bottom: 3px solid #8b5cf6;
        }

        #modalGestionAsignaciones .tab-pane {
            display: none;
            min-height: 400px;
            max-height: 500px;
            overflow-y: auto;
        }

        #modalGestionAsignaciones .tab-pane.active {
            display: block;
        }

        #modalGestionAsignaciones .form-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
        }

        #modalGestionAsignaciones .form-control,
        #modalGestionAsignaciones .form-select {
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 0.75rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        #modalGestionAsignaciones .form-control:focus,
        #modalGestionAsignaciones .form-select:focus {
            border-color: #8b5cf6;
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
        }

        #modalGestionAsignaciones .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        #modalGestionAsignaciones .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        #modalGestionAsignaciones .table {
            font-size: 0.9rem;
        }

        #modalGestionAsignaciones .table th {
            font-weight: 600;
            color: #374151;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            padding: 1rem 0.75rem;
        }

        #modalGestionAsignaciones .table td {
            vertical-align: middle;
            padding: 1rem 0.75rem;
        }

        #modalGestionAsignaciones .hours-info {
            font-size: 0.85rem;
            line-height: 1.2;
        }

        #modalGestionAsignaciones .user-avatar-large {
            width: 40px !important;
            height: 40px !important;
            font-size: 14px !important;
        }

        /* Responsive para el modal */
        @media (max-width: 768px) {
            #modalGestionAsignaciones .modal-dialog {
                margin: 0.5rem;
            }
            
            #modalGestionAsignaciones .modal-header {
                padding: 1rem;
            }
            
            #modalGestionAsignaciones .modal-footer {
                padding: 1rem;
            }

            #modalGestionAsignaciones .tab-content {
                padding: 1rem;
            }

            #modalGestionAsignaciones .nav-tabs .nav-link {
                padding: 0.75rem 1rem;
                font-size: 0.9rem;
            }

            #modalGestionAsignaciones .table {
                font-size: 0.8rem;
            }

            #modalGestionAsignaciones .btn-group .btn {
                padding: 0.5rem;
                font-size: 0.8rem;
            }
        }
    </style>
</body>
</html>
