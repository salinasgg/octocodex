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
                    <!-- <hr style="border: none; height: 1px; background: var(--gradiente-violeta);"> -->
                        <a class="nav-link active" href="#" id="dashboard-btn">
                            Dashboard
                        </a>
                        <!-- <hr style="border: none; height: 1px; background: var(--gradiente-violeta);"> -->
                        <a class="nav-link" href="#" id="usuarios-btn">
                            Usuarios
                        </a>
                        <!-- <hr style="border: none; height: 1px; background: var(--gradiente-violeta);"> -->
                        <a class="nav-link" href="#" id="asignaciones-btn">
                            Asignaciones de Proyectos
                        </a>
                        <!-- <hr style="border: none; height: 1px; background: var(--gradiente-violeta);"> -->
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

        $('#dashboard-btn').click(function(e) {
            e.preventDefault();
            console.log('🏠 Cargando dashboard home desde botón...');
            console.log('📋 Estado actual del contenedor .construccion:', $('.construccion').length > 0 ? 'Existe' : 'No existe');
            
            // Actualizar navegación activa
            $('.nav-link').removeClass('active');
            $(this).addClass('active');
            
            // Asegurar que el contenedor existe
            if ($('.construccion').length === 0) {
                console.log('⚠️ Contenedor .construccion no existe, recreándolo...');
                $('.main-content').html(`
                    <div class="container-fluid">
                        <div class="row construccion">
                        </div>
                    </div>
                `);
            }
            
            // Cargar en el contenedor correcto (.construccion)
            $('.construccion').html('<div class="text-center p-5"><i class="fas fa-spinner fa-spin fa-3x text-primary"></i><p class="mt-3">Cargando dashboard...</p></div>');
            
            $.ajax({
                url: 'dashboard_home.php',
                method: 'GET',
                success: function(response) {
                    $('.construccion').html(response);
                    console.log('✅ Dashboard home cargado correctamente desde botón');
                },
                error: function(xhr, status, error) {
                    console.error('Error al cargar dashboard_home.php:', xhr.status, xhr.statusText);
                    $('.construccion').html(`
                        <div class="alert alert-danger m-4">
                            <h5><i class="fas fa-exclamation-triangle me-2"></i>Error al cargar el dashboard</h5>
                            <p>No se pudo cargar el contenido principal. Error: ${xhr.status} - ${xhr.statusText}</p>
                            <button class="btn btn-outline-danger btn-sm" onclick="$('#dashboard-btn').click()">
                                <i class="fas fa-redo me-1"></i>Intentar de nuevo
                            </button>
                        </div>
                    `);
                }
            });
        });

        // Función para mostrar asignaciones de proyectos
        function mostrarAsignaciones() {
            console.log('🔧 Cargando sección de asignaciones...');
            console.log('📋 Estado actual del contenedor .construccion:', $('.construccion').length > 0 ? 'Existe' : 'No existe');
            
            $('.nav-link').removeClass('active');
            $('#asignaciones-btn').addClass('active');
            
            // Asegurar que el contenedor existe
            if ($('.construccion').length === 0) {
                console.log('⚠️ Contenedor .construccion no existe, recreándolo...');
                $('.main-content').html(`
                    <div class="container-fluid">
                        <div class="row construccion">
                        </div>
                    </div>
                `);
            }
            
            // Usar el mismo contenedor que dashboard para consistencia
            $('.construccion').html(`
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
                                    <!-- Botón para Nueva Asignación -->
                                    <div class="p-3 border-bottom" style="background: rgba(139, 92, 246, 0.03);">
                                        <button type="button" class="btn btn-primary" id="btnNuevaAsignacionProyecto" 
                                                style="background: var(--gradiente-violeta); border: none; border-radius: 10px; font-weight: 600;">
                                            <i class="fas fa-plus me-2"></i>Nueva Asignación de Proyecto
                                        </button>
                                        <small class="text-muted ms-3">Asigna usuarios a proyectos de manera rápida y eficiente</small>
                                    </div>
                                    
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
            console.log('Cargando estadísticas desde: php/asignaciones_proyectos.php');
            $.ajax({
                url: 'php/asignaciones_proyectos.php',
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
            console.log('🚀 === INICIANDO CARGA DE ASIGNACIONES POR PROYECTO ===');
            console.log('Cargando proyectos desde: php/asignaciones_proyectos.php');
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
                url: 'php/asignaciones_proyectos.php',
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
            console.log('Cargando usuarios desde: php/asignaciones_proyectos.php');
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
                url: 'php/asignaciones_proyectos.php',
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
                'alta': '<span class="badge" style="background: #dc3545; color:black;">Alta</span>',
                'media': '<span class="badge" style="background: #ffc107; color:black;">Media</span>',
                'baja': '<span class="badge" style="background: #28a745; color:black;">Baja</span>',
                'critica': '<span class="badge" style="background: #6f42c1; color:black;">Crítica</span>'
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
            console.log('🔧 Dashboard: Ver detalle del proyecto:', proyectoId);
            console.log('🔍 Verificando disponibilidad de función verDetalleProyectoDesdeAsignaciones...');
            
            // Función para intentar cargar el detalle
            function intentarCargarDetalle() {
                if (typeof verDetalleProyectoDesdeAsignaciones === 'function') {
                    console.log('✅ Función encontrada, llamando verDetalleProyectoDesdeAsignaciones...');
                    verDetalleProyectoDesdeAsignaciones(proyectoId);
                    return true;
                }
                return false;
            }
            
            // Intentar inmediatamente
            if (intentarCargarDetalle()) {
                return;
            }
            
            // Si no está disponible, esperar un poco y reintentar
            console.log('⏳ Función no disponible inmediatamente, esperando carga de scripts...');
            setTimeout(function() {
                if (intentarCargarDetalle()) {
                    return;
                }
                
                // Último intento después de más tiempo
                setTimeout(function() {
                    if (!intentarCargarDetalle()) {
                        console.error('❌ Función verDetalleProyectoDesdeAsignaciones no disponible después de esperar');
                        console.log('📋 Funciones disponibles en window:', Object.keys(window).filter(key => key.includes('Proyecto')));
                        
                        // Fallback: mostrar un modal básico con los datos disponibles
                        mostrarDetalleBasico(proyectoId);
                    }
                }, 1000);
            }, 500);
        }
        
        // Fallback: modal básico si no se puede cargar ABMProyectos.js
        function mostrarDetalleBasico(proyectoId) {
            alert('Cargando detalle del proyecto #' + proyectoId + '\n\nNota: Funcionalidad completa en desarrollo.\nPor favor recarga la página si persiste el problema.');
        }

        // Gestionar asignaciones de proyecto
        function gestionarAsignacionesProyecto(proyectoId) {
            console.log('🔧 Gestionar asignaciones del proyecto:', proyectoId);
            window.currentEntityId = proyectoId;
            window.currentEntityType = 'proyecto';
            
            // Actualizar título del modal
            $('#modal-title-text').html('<i class="fas fa-project-diagram me-2"></i>Gestionar Asignaciones - Proyecto #' + proyectoId);
            
            // Cargar datos
            cargarAsignacionesModal(proyectoId, 'proyecto');
            cargarSelectoresModal();
            
            // Reinicializar tabs - asegurar que el primer tab esté activo
            $('.modal-tab').removeClass('active').css('color', '#6b7280');
            $('.modal-tab[data-tab="current"]').addClass('active').css('color', '#8b5cf6');
            
            // Mostrar contenido del primer tab y ocultar otros
            $('.tab-pane').removeClass('active');
            $('#current-assignments').addClass('active');
            
            // Ocultar botón de guardar inicialmente
            $('#btnGuardarAsignacion').hide();
            
            // Abrir modal
            const modal = new bootstrap.Modal(document.getElementById('modalGestionAsignaciones'));
            modal.show();
            
            console.log('✅ Modal de gestión de asignaciones abierto');
        }

        // Ver detalle de usuario
        function verDetalleUsuario(usuarioId) {
            console.log('🔧 Iniciando verDetalleUsuario para ID:', usuarioId);
            console.log('🔍 Verificando elemento modal:', document.getElementById('modalDetalleUsuario'));
            
            try {
                // Mostrar spinner en el modal
                const modalElement = document.getElementById('modalDetalleUsuario');
                if (!modalElement) {
                    console.error('❌ Modal modalDetalleUsuario no encontrado');
                    alert('Error: Modal no encontrado');
                    return;
                }
                
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
                console.log('✅ Modal de detalle mostrado');
                
                // Mostrar estado de carga
                $('#detalle-usuario-titulo').text('Cargando usuario...');
                $('#modalDetalleUsuario .modal-body').html(`
                    <div class="text-center p-5">
                        <div class="spinner-border" style="color: #8b5cf6;" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-3">Cargando información del usuario...</p>
                    </div>
                `);
                console.log('✅ Spinner de carga mostrado');

                // Construir URL completa para debug
                const baseUrl = window.location.origin + window.location.pathname;
                const url = '../php/usuarios_info.php';
                console.log('🌐 Base URL:', baseUrl);
                console.log('🔗 Petición URL:', url);
                console.log('📦 Data enviada:', { id: usuarioId });

                // Usar el endpoint de usuarios que ya sabemos que funciona
                $.ajax({
                    url: '../php/usuarios.php',
                    method: 'GET',
                    dataType: 'json',
                    timeout: 10000, // 10 segundos de timeout
                    beforeSend: function(xhr) {
                        console.log('📤 Enviando petición AJAX a usuarios.php...');
                    },
                    success: function(usuarios) {
                        console.log('📥 Lista de usuarios recibida:', usuarios.length, 'usuarios');
                        
                        // Buscar el usuario específico por ID
                        const usuario = usuarios.find(u => u.id == usuarioId);
                        
                        if (usuario) {
                            console.log('✅ Usuario encontrado:', usuario);
                            console.log('📋 Campos del usuario:', Object.keys(usuario));
                            console.log('🖼️ Foto de perfil específica:', {
                                campo: 'us_foto_perfil',
                                valor: usuario.us_foto_perfil,
                                tipo: typeof usuario.us_foto_perfil
                            });
                            mostrarDetalleUsuario(usuario);
                            // Cargar estadísticas adicionales
                            cargarEstadisticasUsuario(usuarioId);
                        } else {
                            console.error('❌ Usuario no encontrado con ID:', usuarioId);
                            mostrarErrorUsuario('Usuario no encontrado en la base de datos');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('❌ Error AJAX completo:', {
                            status: status,
                            error: error,
                            responseText: xhr.responseText,
                            statusCode: xhr.status,
                            statusText: xhr.statusText
                        });
                        mostrarErrorUsuario(`Error de conexión: ${error} (${xhr.status})`);
                    }
                });
                
            } catch (e) {
                console.error('❌ Error en verDetalleUsuario:', e);
                alert('Error inesperado: ' + e.message);
            }
        }

        // Función para mostrar los datos del usuario en el modal
        function mostrarDetalleUsuario(usuario) {
            console.log('🎭 Mostrando detalle del usuario:', usuario.us_nombre, usuario.us_apellido);
            
            // Restaurar el contenido del modal si fue reemplazado por el spinner
            restaurarContenidoModalUsuario();
            
            // Actualizar título del modal
            const nombreCompleto = `${usuario.us_nombre || ''} ${usuario.us_apellido || ''}`.trim();
            $('#detalle-usuario-titulo').text(`Detalle del Usuario: ${nombreCompleto}`);
            
            // Llenar información personal
            $('#detalle-usuario-nombre').text(nombreCompleto || 'Sin nombre');
            $('#detalle-usuario-username').text(usuario.us_username || 'Sin usuario');
            $('#detalle-usuario-email').text(usuario.us_email || 'Sin email');
            $('#detalle-usuario-nacimiento').text(
                usuario.us_fecha_nacimiento ? 
                new Date(usuario.us_fecha_nacimiento).toLocaleDateString('es-AR') : 
                'Sin definir'
            );
            $('#detalle-usuario-bio').text(usuario.us_bio || 'Sin biografía disponible');
            
            // Llenar información del sistema
            const rolBadge = obtenerBadgeRolUsuario(usuario.us_rol);
            $('#detalle-usuario-rol').html(rolBadge);
            
            const estadoBadge = usuario.us_activo == 1 ? 
                '<span class="badge bg-success">Activo</span>' : 
                '<span class="badge bg-danger">Inactivo</span>';
            $('#detalle-usuario-estado').html(estadoBadge);
            
            $('#detalle-usuario-registro').text(
                usuario.us_fecha_registro ? 
                new Date(usuario.us_fecha_registro).toLocaleDateString('es-AR') : 
                'Sin fecha'
            );
            $('#detalle-usuario-actualizacion').text(
                usuario.fecha_actualizacion ? 
                new Date(usuario.fecha_actualizacion).toLocaleDateString('es-AR') : 
                'Sin actualizar'
            );
            $('#detalle-usuario-ultimo-acceso').text(
                usuario.us_fecha_ultimo_acceso ? 
                new Date(usuario.us_fecha_ultimo_acceso).toLocaleDateString('es-AR') : 
                'Sin registro'
            );
            $('#detalle-usuario-ip').text(usuario.us_ultimo_ip || 'Sin registro');
            
            // Información lateral
            $('#detalle-usuario-id').text(`#${usuario.id}`);
            
            // Generar avatar con iniciales
            const iniciales = obtenerIniciales(nombreCompleto);
            const colorAvatar = generarColorAvatar(usuario.id);
            $('#detalle-usuario-avatar').html(iniciales).css('background', colorAvatar);
            
            // Manejar foto de perfil si existe
            console.log('📸 Procesando foto de perfil:', {
                us_foto_perfil: usuario.us_foto_perfil,
                tipo: typeof usuario.us_foto_perfil,
                existe: !!usuario.us_foto_perfil,
                longitud: usuario.us_foto_perfil ? usuario.us_foto_perfil.length : 0
            });
            
            if (usuario.us_foto_perfil && usuario.us_foto_perfil.trim() !== '' && usuario.us_foto_perfil !== null) {
                let fotoUrl = usuario.us_foto_perfil;
                
                // Si la ruta no es absoluta, construir ruta relativa
                if (!fotoUrl.startsWith('http') && !fotoUrl.startsWith('/')) {
                    fotoUrl = '../uploads/usuarios/' + fotoUrl;
                }
                
                console.log('📸 Configurando imagen con URL:', fotoUrl);
                $('#detalle-usuario-foto img').attr('src', fotoUrl);
                $('#detalle-usuario-foto').show();
                $('#detalle-usuario-avatar').hide();
                
                // Verificar si la imagen carga correctamente
                $('#detalle-usuario-foto img').on('load', function() {
                    console.log('✅ Imagen cargada correctamente:', fotoUrl);
                }).on('error', function() {
                    console.error('❌ Error al cargar imagen:', fotoUrl);
                    // Si hay error, mostrar avatar con iniciales
                    $('#detalle-usuario-foto').hide();
                    $('#detalle-usuario-avatar').show();
                });
            } else {
                console.log('📸 Sin foto de perfil, mostrando avatar con iniciales');
                $('#detalle-usuario-foto').hide();
                $('#detalle-usuario-avatar').show();
            }
            
            // Manejar URL de perfil
            if (usuario.us_url_perfil) {
                $('#detalle-usuario-url-container').html(`
                    <a href="${usuario.us_url_perfil}" target="_blank" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-external-link-alt me-1"></i>Ver Perfil
                    </a>
                `);
            } else {
                $('#detalle-usuario-url-container').html('<small class="text-muted">Sin URL de perfil</small>');
            }
            
            // Almacenar ID del usuario para las funciones de botones
            window.currentUserDetailId = usuario.id;
            
            console.log('✅ Detalle del usuario mostrado correctamente');
        }

        // Función para mostrar error al cargar usuario
        function mostrarErrorUsuario(mensaje) {
            $('#detalle-usuario-titulo').text('Error al cargar usuario');
            $('#modalDetalleUsuario .modal-body').html(`
                <div class="text-center p-5">
                    <i class="fas fa-exclamation-triangle text-danger mb-3" style="font-size: 48px;"></i>
                    <h5 class="text-danger">Error al cargar usuario</h5>
                    <p class="text-muted">${mensaje}</p>
                    <button class="btn btn-secondary" onclick="$('#modalDetalleUsuario').modal('hide')">
                        <i class="fas fa-times me-1"></i>Cerrar
                    </button>
                </div>
            `);
        }

        // Función para restaurar el contenido original del modal
        function restaurarContenidoModalUsuario() {
            // Solo restaurar si el contenido fue reemplazado por el spinner
            if ($('#modalDetalleUsuario .modal-body .spinner-border').length > 0) {
                $('#modalDetalleUsuario .modal-body').html(`
                    <div class="row">
                        <!-- Información principal del usuario -->
                        <div class="col-md-8">
                            <!-- Información Personal -->
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0 text-primary">
                                        <i class="fas fa-user-circle me-2"></i>Información Personal
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold text-muted">
                                                <i class="fas fa-id-card me-1"></i>Nombre Completo
                                            </label>
                                            <p class="fs-5 fw-bold text-dark mb-0" id="detalle-usuario-nombre">-</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold text-muted">
                                                <i class="fas fa-at me-1"></i>Usuario
                                            </label>
                                            <p class="mb-0" id="detalle-usuario-username">-</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold text-muted">
                                                <i class="fas fa-envelope me-1"></i>Email
                                            </label>
                                            <p class="mb-0" id="detalle-usuario-email">-</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold text-muted">
                                                <i class="fas fa-birthday-cake me-1"></i>Fecha de Nacimiento
                                            </label>
                                            <p class="mb-0" id="detalle-usuario-nacimiento">-</p>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label fw-bold text-muted">
                                                <i class="fas fa-info-circle me-1"></i>Biografía
                                            </label>
                                            <p class="text-muted" id="detalle-usuario-bio">Sin biografía disponible</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Información del Sistema -->
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0 text-primary">
                                        <i class="fas fa-cogs me-2"></i>Información del Sistema
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold text-muted">
                                                <i class="fas fa-user-tag me-1"></i>Rol
                                            </label>
                                            <span class="badge fs-6" id="detalle-usuario-rol">-</span>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold text-muted">
                                                <i class="fas fa-toggle-on me-1"></i>Estado
                                            </label>
                                            <span class="badge fs-6" id="detalle-usuario-estado">-</span>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold text-muted">
                                                <i class="fas fa-calendar-plus me-1"></i>Fecha de Registro
                                            </label>
                                            <p class="mb-0" id="detalle-usuario-registro">-</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold text-muted">
                                                <i class="fas fa-clock me-1"></i>Última Actualización
                                            </label>
                                            <p class="mb-0" id="detalle-usuario-actualizacion">-</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold text-muted">
                                                <i class="fas fa-sign-in-alt me-1"></i>Último Acceso
                                            </label>
                                            <p class="mb-0" id="detalle-usuario-ultimo-acceso">-</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold text-muted">
                                                <i class="fas fa-network-wired me-1"></i>Última IP
                                            </label>
                                            <p class="mb-0 font-monospace" id="detalle-usuario-ip">-</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información lateral -->
                        <div class="col-md-4">
                            <!-- Avatar y Estado -->
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0 text-primary">
                                        <i class="fas fa-image me-2"></i>Perfil
                                    </h6>
                                </div>
                                <div class="card-body text-center">
                                    <div class="mb-4">
                                        <div id="detalle-usuario-avatar" class="mx-auto mb-3" style="width: 100px; height: 100px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 36px; font-weight: 700; box-shadow: 0 4px 15px rgba(0,0,0,0.2); background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                                            U
                                        </div>
                                        <div id="detalle-usuario-foto" style="display: none;">
                                            <img src="" alt="Foto de perfil" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-muted d-block">ID del Usuario</label>
                                        <p class="fs-4 fw-bold text-primary mb-0" id="detalle-usuario-id">#-</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-muted d-block">
                                            <i class="fas fa-link me-1"></i>URL de Perfil
                                        </label>
                                        <div id="detalle-usuario-url-container">
                                            <small class="text-muted">Sin URL de perfil</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Estadísticas -->
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0 text-primary">
                                        <i class="fas fa-chart-bar me-2"></i>Estadísticas
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="text-center mb-3">
                                        <div class="h3 text-success mb-1" id="detalle-usuario-proyectos-total">0</div>
                                        <small class="text-muted">Proyectos Asignados</small>
                                    </div>
                                    <div class="text-center mb-3">
                                        <div class="h3 text-warning mb-1" id="detalle-usuario-horas-total">0</div>
                                        <small class="text-muted">Horas Asignadas</small>
                                    </div>
                                    <div class="text-center">
                                        <div class="h3 text-info mb-1" id="detalle-usuario-roles-total">0</div>
                                        <small class="text-muted">Roles Diferentes</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Acciones -->
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0 text-primary">
                                        <i class="fas fa-tools me-2"></i>Acciones
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-outline-success btn-sm" onclick="gestionarAsignacionesUsuarioDesdeDetalle()" id="btn-gestionar-asignaciones-usuario">
                                            <i class="fas fa-users-cog me-2"></i>Gestionar Asignaciones
                                        </button>
                                        <button class="btn btn-outline-primary btn-sm" onclick="editarUsuarioDesdeDetalle()" id="btn-editar-usuario">
                                            <i class="fas fa-edit me-2"></i>Editar Usuario
                                        </button>
                                        <button class="btn btn-outline-info btn-sm" onclick="verHistorialUsuario()" id="btn-historial-usuario">
                                            <i class="fas fa-history me-2"></i>Ver Historial
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `);
            }
        }

        // Función para cargar estadísticas adicionales del usuario
        function cargarEstadisticasUsuario(usuarioId) {
            console.log('📊 Cargando estadísticas del usuario:', usuarioId);
            
            // Obtener estadísticas desde el endpoint de asignaciones
            $.ajax({
                url: 'php/asignaciones_proyectos.php',
                method: 'GET',
                data: { 
                    accion: 'obtener_estadisticas_usuario',
                    usuario_id: usuarioId 
                },
                dataType: 'json',
                success: function(response) {
                    if (response.exito && response.estadisticas) {
                        const stats = response.estadisticas;
                        $('#detalle-usuario-proyectos-total').text(stats.total_proyectos || 0);
                        $('#detalle-usuario-horas-total').text(stats.total_horas || 0);
                        $('#detalle-usuario-roles-total').text(stats.roles_diferentes || 0);
                        console.log('✅ Estadísticas cargadas:', stats);
                    } else {
                        console.log('⚠️ No se pudieron cargar las estadísticas del usuario');
                        // Mantener valores en 0 si no hay estadísticas
                    }
                },
                error: function(xhr, status, error) {
                    console.error('❌ Error al cargar estadísticas:', error);
                    // Mantener valores en 0 en caso de error
                }
            });
        }

        // Función para obtener badge del rol del usuario
        function obtenerBadgeRolUsuario(rol) {
            const roles = {
                'admin': '<span class="badge bg-danger">Administrador</span>',
                'manager': '<span class="badge bg-warning">Manager</span>',
                'developer': '<span class="badge bg-primary">Desarrollador</span>',
                'client': '<span class="badge bg-info">Cliente</span>',
                'user': '<span class="badge bg-secondary">Usuario</span>'
            };
            return roles[rol] || `<span class="badge bg-secondary">${rol || 'Sin rol'}</span>`;
        }

        // Funciones para los botones de acción del modal
        function gestionarAsignacionesUsuarioDesdeDetalle() {
            if (window.currentUserDetailId) {
                // Cerrar modal de detalle
                $('#modalDetalleUsuario').modal('hide');
                
                // Abrir modal de gestión de asignaciones
                setTimeout(() => {
                    gestionarAsignacionesUsuario(window.currentUserDetailId);
                }, 300);
            }
        }

        function editarUsuarioDesdeDetalle() {
            if (window.currentUserDetailId) {
                console.log('🔧 Editar usuario:', window.currentUserDetailId);
                alert('Funcionalidad de edición de usuario en desarrollo');
                // Aquí se implementaría la funcionalidad de edición
            }
        }

        function verHistorialUsuario() {
            if (window.currentUserDetailId) {
                console.log('📜 Ver historial del usuario:', window.currentUserDetailId);
                alert('Funcionalidad de historial de usuario en desarrollo');
                // Aquí se implementaría la funcionalidad de historial
            }
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
                console.log('🔄 Cambiando a tab:', tab);
                
                // Actualizar tabs activos
                $('.modal-tab').removeClass('active').css('color', '#6b7280');
                $(this).addClass('active').css('color', '#8b5cf6');
                
                // Mostrar contenido del tab (corregido para usar nombres reales)
                $('.tab-pane').removeClass('active');
                
                let targetTab;
                switch(tab) {
                    case 'current':
                        targetTab = '#current-assignments';
                        break;
                    case 'add':
                        targetTab = '#add-assignment';
                        break;
                    case 'history':
                        targetTab = '#history-assignments';
                        break;
                    default:
                        targetTab = `#${tab}-assignments`;
                }
                
                console.log('🎯 Activando tab:', targetTab);
                $(targetTab).addClass('active');
                
                // Mostrar/ocultar botón de guardar según el tab
                if (tab === 'add') {
                    $('#btnGuardarAsignacion').show();
                    console.log('👁️ Mostrando botón Guardar Asignación');
                    console.log('🔍 Estado del botón después de mostrar:', {
                        existe: $('#btnGuardarAsignacion').length,
                        visible: $('#btnGuardarAsignacion').is(':visible'),
                        display: $('#btnGuardarAsignacion').css('display')
                    });
                } else {
                    $('#btnGuardarAsignacion').hide();
                    console.log('👁️‍🗨️ Ocultando botón Guardar Asignación');
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
                url = 'php/asignaciones_proyectos.php';
                params = { accion: 'obtener_asignaciones', proyecto_id: entityId };
            } else {
                url = 'php/asignaciones_proyectos.php';
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
                url: '/octocodex/condor/php/listar_proyectos_asignaciones.php',
                method: 'GET',
                data: { accion: 'listar' },
                dataType: 'json',
                xhrFields: {
                    withCredentials: true
                },
                crossDomain: false,
                success: function(response) {
                    if (response.exito && response.proyectos) {
                        let options = '<option value="">Seleccione un proyecto...</option>';
                        response.proyectos.forEach(proyecto => {
                            options += `<option value="${proyecto.id}">${proyecto.titulo}</option>`;
                        });
                        $('#proyecto-select').html(options);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error al cargar proyectos:', error);
                    $('#proyecto-select').html('<option value="">Error al cargar proyectos</option>');
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
            
            // Obtener datos de la asignación
            $.ajax({
                url: 'php/asignaciones_proyectos.php',
                method: 'GET',
                data: { 
                    accion: 'obtener_asignacion_individual', 
                    asignacion_id: asignacionId 
                },
                dataType: 'json',
                xhrFields: {
                    withCredentials: true
                },
                success: function(response) {
                    if (response.exito && response.asignacion) {
                        mostrarModalEditarAsignacion(response.asignacion);
                    } else {
                        alert('Error: ' + (response.mensaje || 'No se pudo cargar la asignación'));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error al cargar asignación:', error);
                    alert('Error al conectar con el servidor');
                }
            });
        }
        
        // Mostrar modal para editar asignación
        function mostrarModalEditarAsignacion(asignacion) {
            const modalHtml = `
                <div class="modal fade" id="modalEditarAsignacion" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header" style="background: var(--gradiente-violeta); color: white;">
                                <h5 class="modal-title">
                                    <i class="fas fa-edit me-2"></i>Editar Asignación
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <form id="formEditarAsignacion">
                                    <input type="hidden" name="asignacion_id" value="${asignacion.asignacion_id}">
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Proyecto</label>
                                        <input type="text" class="form-control" value="${asignacion.proyecto_titulo}" readonly>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Usuario</label>
                                        <input type="text" class="form-control" value="${asignacion.nombre_completo}" readonly>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Rol en el Proyecto</label>
                                                <select class="form-select" name="rol_proyecto" required>
                                                    <option value="lider" ${asignacion.rol_proyecto === 'lider' ? 'selected' : ''}>Líder</option>
                                                    <option value="desarrollador" ${asignacion.rol_proyecto === 'desarrollador' ? 'selected' : ''}>Desarrollador</option>
                                                    <option value="consultor" ${asignacion.rol_proyecto === 'consultor' ? 'selected' : ''}>Consultor</option>
                                                    <option value="revisor" ${asignacion.rol_proyecto === 'revisor' ? 'selected' : ''}>Revisor</option>
                                                    <option value="colaborador" ${asignacion.rol_proyecto === 'colaborador' ? 'selected' : ''}>Colaborador</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Estado</label>
                                                <select class="form-select" name="estado_asignacion" required>
                                                    <option value="activo" ${asignacion.estado_asignacion === 'activo' ? 'selected' : ''}>Activo</option>
                                                    <option value="completado" ${asignacion.estado_asignacion === 'completado' ? 'selected' : ''}>Completado</option>
                                                    <option value="pausado" ${asignacion.estado_asignacion === 'pausado' ? 'selected' : ''}>Pausado</option>
                                                    <option value="cancelado" ${asignacion.estado_asignacion === 'cancelado' ? 'selected' : ''}>Cancelado</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Horas Asignadas</label>
                                                <input type="number" class="form-control" name="horas_asignadas" 
                                                       value="${asignacion.horas_asignadas}" min="0" step="0.5">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Horas Trabajadas</label>
                                                <input type="number" class="form-control" name="horas_trabajadas" 
                                                       value="${asignacion.horas_trabajadas}" min="0" step="0.5">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Fecha Inicio</label>
                                                <input type="date" class="form-control" name="fecha_inicio" 
                                                       value="${asignacion.fecha_inicio || ''}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Fecha Fin</label>
                                                <input type="date" class="form-control" name="fecha_fin" 
                                                       value="${asignacion.fecha_fin || ''}">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Notas</label>
                                        <textarea class="form-control" name="notas" rows="3" placeholder="Notas adicionales...">${asignacion.notas || ''}</textarea>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="button" class="btn btn-primary" onclick="guardarEdicionAsignacion()" 
                                        style="background: var(--gradiente-violeta); border: none;">
                                    <i class="fas fa-save me-2"></i>Guardar Cambios
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Eliminar modal anterior si existe
            $('#modalEditarAsignacion').remove();
            
            // Agregar y mostrar el modal
            $('body').append(modalHtml);
            const modal = new bootstrap.Modal(document.getElementById('modalEditarAsignacion'));
            modal.show();
        }
        
        // Guardar edición de asignación
        function guardarEdicionAsignacion() {
            const formData = new FormData(document.getElementById('formEditarAsignacion'));
            formData.append('accion', 'actualizar_asignacion');

            $.ajax({
                url: 'php/asignaciones_proyectos.php',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                xhrFields: {
                    withCredentials: true
                },
                success: function(response) {
                    if (response.exito) {
                        $('#modalEditarAsignacion').modal('hide');
                        
                        // Mostrar mensaje de éxito
                        const alertHtml = `
                            <div class="alert alert-success alert-dismissible fade show" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
                                <i class="fas fa-check-circle me-2"></i>
                                ${response.mensaje || 'Asignación actualizada exitosamente'}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        `;
                        $('body').append(alertHtml);
                        setTimeout(() => $('.alert').alert('close'), 3000);
                        
                        // Recargar datos
                        cargarEstadisticasAsignaciones();
                        
                        // Recargar tab activo
                        const activeTab = $('.assignment-tab.active').data('tab');
                        if (activeTab === 'projects') {
                            cargarAsignacionesPorProyecto();
                        } else if (activeTab === 'users') {
                            cargarAsignacionesPorUsuario();
                        }
                        
                    } else {
                        alert('Error: ' + (response.mensaje || 'No se pudo actualizar la asignación'));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error al actualizar asignación:', error);
                    alert('Error al conectar con el servidor');
                }
            });
        }

        // Eliminar asignación
        function eliminarAsignacion(asignacionId) {
            console.log('🗑️ Intentando eliminar asignación ID:', asignacionId);
            
            // Usar confirm tradicional por ahora
            if (confirm('¿Está seguro de que desea eliminar esta asignación?\n\nEsta acción no se puede deshacer.')) {
                console.log('✅ Confirmación aceptada, procediendo a eliminar...');
                
                $.ajax({
                        url: 'php/asignaciones_proyectos.php',
                        method: 'POST',
                        data: { 
                            accion: 'eliminar_asignacion',
                            asignacion_id: asignacionId
                        },
                        dataType: 'json',
                        xhrFields: {
                            withCredentials: true
                        },
                        crossDomain: false,
                        success: function(response) {
                            console.log('📥 Respuesta del servidor para eliminar:', response);
                            
                            if (response.exito) {
                                console.log('✅ Eliminación exitosa');
                                showMessage('¡Asignación eliminada exitosamente!', 'success', 3000);
                                
                                // Recargar las asignaciones
                                if (window.currentEntityId && window.currentEntityType) {
                                    cargarAsignacionesModal(window.currentEntityId, window.currentEntityType);
                                }
                                
                                // Actualizar estadísticas
                                if (typeof cargarEstadisticasAsignaciones === 'function') {
                                    cargarEstadisticasAsignaciones();
                                }
                                
                                // Recargar el listado principal de proyectos
                                if (typeof cargarAsignacionesPorProyecto === 'function') {
                                    cargarAsignacionesPorProyecto();
                                }
                            } else {
                                showMessage('Error al eliminar: ' + response.mensaje, 'error', 4000);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error al eliminar asignación:', error);
                            showMessage('Error al conectar con el servidor', 'error', 4000);
                        }
                    });
            }
        }

        // Cargar proyectos disponibles en el select
        function cargarProyectosSelect() {
            console.log('🔍 Cargando proyectos para select...');
            
            $.ajax({
                url: 'php/asignaciones_proyectos.php',
                method: 'GET',
                data: { accion: 'listar_proyectos' },
                dataType: 'json',
                success: function(response) {
                    if (response.exito && response.proyectos) {
                        const select = $('#proyecto-select');
                        select.html('<option value="">Seleccione un proyecto...</option>');
                        
                        response.proyectos.forEach(proyecto => {
                            const option = `<option value="${proyecto.id}" 
                                                  data-estado="${proyecto.pr_estado}" 
                                                  data-prioridad="${proyecto.pr_prioridad}">
                                                ${proyecto.pr_titulo} (${proyecto.pr_estado})
                                            </option>`;
                            select.append(option);
                        });
                        
                        console.log('✅ Proyectos cargados en select:', response.proyectos.length);
                    } else {
                        console.error('❌ Error al cargar proyectos:', response.mensaje);
                        $('#proyecto-select').html('<option value="">Error al cargar proyectos</option>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('❌ Error AJAX al cargar proyectos:', error);
                    $('#proyecto-select').html('<option value="">Error de conexión</option>');
                }
            });
        }

        // Cargar usuarios disponibles en el select
        function cargarUsuariosSelect(proyectoId = null) {
            console.log('🔍 Cargando usuarios para select...');
            
            // Si no se proporciona proyecto_id, usar el global
            if (!proyectoId && window.currentEntityId && window.currentEntityType === 'proyecto') {
                proyectoId = window.currentEntityId;
            }
            
            // Si aún no hay proyecto_id, cargar todos los usuarios activos
            if (!proyectoId) {
                console.log('⚠️ No hay proyecto_id, cargando todos los usuarios activos');
                cargarTodosLosUsuarios();
                return;
            }
            
            console.log('📋 Parámetros enviados:', { 
                accion: 'listar_usuarios_disponibles', 
                proyecto_id: proyectoId 
            });
            
            $.ajax({
                url: 'php/asignaciones_proyectos.php',
                method: 'GET',
                data: { 
                    accion: 'listar_usuarios_disponibles',
                    proyecto_id: proyectoId // Agregar proyecto_id como parámetro
                },
                dataType: 'json',
                success: function(response) {
                    console.log('📥 Respuesta de usuarios:', response);
                    
                    if (response.exito && response.usuarios) {
                        const select = $('#usuario-select');
                        select.html('<option value="">Seleccione un usuario...</option>');
                        
                        let usuariosDisponibles = 0;
                        let usuariosAsignados = 0;
                        
                        response.usuarios.forEach(usuario => {
                            const esDisponible = usuario.estado_asignacion === 'disponible';
                            let optionClass = '';
                            let optionText = usuario.nombre_completo;
                            
                            if (esDisponible) {
                                usuariosDisponibles++;
                                optionText += ` (${usuario.us_email})`;
                            } else {
                                usuariosAsignados++;
                                optionText += ` (${usuario.us_email}) - YA ASIGNADO`;
                                optionClass = 'style="color: #6c757d; font-style: italic;"';
                            }
                            
                            const option = `<option value="${usuario.id}" 
                                                  data-email="${usuario.us_email}"
                                                  data-rol="${usuario.us_rol}"
                                                  data-estado="${usuario.estado_asignacion}"
                                                  ${!esDisponible ? 'disabled' : ''}
                                                  ${optionClass}>
                                                ${optionText}
                                            </option>`;
                            select.append(option);
                        });
                        
                        console.log(`✅ Usuarios cargados - Disponibles: ${usuariosDisponibles}, Ya asignados: ${usuariosAsignados}, Total: ${response.usuarios.length}`);
                        
                        // Mostrar información adicional
                        mostrarInfoUsuarios(usuariosDisponibles, usuariosAsignados);
                        
                    } else {
                        console.error('❌ Error al cargar usuarios:', response.mensaje);
                        $('#usuario-select').html('<option value="">Error al cargar usuarios</option>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('❌ Error AJAX al cargar usuarios:', error);
                    console.error('❌ Respuesta del servidor:', xhr.responseText);
                    $('#usuario-select').html('<option value="">Error de conexión</option>');
                }
            });
        }

        // Función alternativa para cargar todos los usuarios (cuando no hay proyecto seleccionado)
        function cargarTodosLosUsuarios() {
            console.log('🔄 Cargando todos los usuarios activos...');
            
            // Usar la API de asignaciones para obtener usuarios
            $.ajax({
                url: 'php/asignaciones_proyectos.php',
                method: 'GET',
                data: { accion: 'listar_usuarios' },
                dataType: 'json',
                success: function(response) {
                    if (response.exito && response.usuarios) {
                        const select = $('#usuario-select');
                        select.html('<option value="">Seleccione un usuario...</option>');
                        
                        response.usuarios.forEach(usuario => {
                            if (usuario.us_activo == 1) {
                                const option = `<option value="${usuario.id}" 
                                                      data-email="${usuario.us_email}"
                                                      data-rol="${usuario.us_rol}">
                                                    ${usuario.nombre_completo} (${usuario.us_email})
                                                </option>`;
                                select.append(option);
                            }
                        });
                        
                        console.log('✅ Usuarios cargados desde fuente alternativa:', response.usuarios.length);
                    }
                },
                error: function() {
                    // Si falla, crear usuarios con query SQL directa
                    console.log('⚠️ Fuente alternativa no disponible, usando endpoint con proyecto_id = null');
                    cargarUsuariosSinProyecto();
                }
            });
        }

        // Última alternativa: cargar usuarios sin filtrar por proyecto
        function cargarUsuariosSinProyecto() {
            $.ajax({
                url: 'php/asignaciones_proyectos.php',
                method: 'GET',
                data: { 
                    accion: 'listar_usuarios_disponibles',
                    proyecto_id: '' // Enviar vacío para obtener todos
                },
                dataType: 'json',
                success: function(response) {
                    if (response.exito && response.usuarios) {
                        const select = $('#usuario-select');
                        select.html('<option value="">Seleccione un usuario...</option>');
                        
                        response.usuarios.forEach(usuario => {
                            const option = `<option value="${usuario.id}" 
                                                  data-email="${usuario.us_email}"
                                                  data-rol="${usuario.us_rol}">
                                                ${usuario.nombre_completo} (${usuario.us_email})
                                            </option>`;
                            select.append(option);
                        });
                        
                        console.log('✅ Usuarios cargados sin filtrar:', response.usuarios.length);
                        
                        // Mostrar info general
                        const infoHtml = `
                            <div id="info-usuarios-disponibles" class="mt-2">
                                <small class="text-info">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Seleccione un proyecto primero para ver disponibilidad específica
                                </small>
                            </div>
                        `;
                        $('#usuario-select').closest('.mb-3').append(infoHtml);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('❌ Error al cargar usuarios:', error);
                    $('#usuario-select').html('<option value="">Error al cargar usuarios</option>');
                }
            });
        }

        // Mostrar información sobre usuarios disponibles
        function mostrarInfoUsuarios(disponibles, asignados) {
            // Remover info anterior
            $('#info-usuarios-disponibles').remove();
            
            const infoHtml = `
                <div id="info-usuarios-disponibles" class="mt-2">
                    <small class="text-muted">
                        <i class="fas fa-users me-1"></i>
                        ${disponibles} usuarios disponibles, ${asignados} ya asignados a este proyecto
                    </small>
                </div>
            `;
            
            $('#usuario-select').closest('.mb-3').append(infoHtml);
        }

        // Validar formulario antes de enviar
        function validarFormularioAsignacion() {
            const form = document.getElementById('formNuevaAsignacion');
            const formData = new FormData(form);
            
            // Validaciones básicas
            if (!formData.get('proyecto_id')) {
                mostrarAlertaFormulario('Debe seleccionar un proyecto', 'error');
                return false;
            }
            
            if (!formData.get('usuario_id')) {
                mostrarAlertaFormulario('Debe seleccionar un usuario', 'error');
                return false;
            }
            
            if (!formData.get('rol_proyecto')) {
                mostrarAlertaFormulario('Debe seleccionar un rol para el proyecto', 'error');
                return false;
            }
            
            // Validación de horas (opcional pero si se ingresa debe ser válida)
            const horas = formData.get('horas_asignadas');
            if (horas && (isNaN(horas) || parseFloat(horas) < 0)) {
                mostrarAlertaFormulario('Las horas asignadas deben ser un número válido mayor o igual a 0', 'error');
                return false;
            }
            
            // Validación de fecha (opcional pero si se ingresa debe ser válida)
            const fechaInicio = formData.get('fecha_inicio');
            if (fechaInicio) {
                const fecha = new Date(fechaInicio);
                const hoy = new Date();
                hoy.setHours(0, 0, 0, 0);
                
                if (fecha < hoy) {
                    if (!confirm('La fecha de inicio es anterior a hoy. ¿Desea continuar?')) {
                        return false;
                    }
                }
            }
            
            return true;
        }

        // Mostrar alertas en el formulario
        function mostrarAlertaFormulario(mensaje, tipo) {
            // Remover alertas existentes
            $('#add-assignment .alert').remove();
            
            const tipoClass = tipo === 'success' ? 'alert-success' : 'alert-danger';
            const icono = tipo === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-triangle';
            
            const alerta = `
                <div class="alert ${tipoClass} alert-dismissible fade show mb-3" role="alert">
                    <i class="${icono} me-2"></i>
                    ${mensaje}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            
            $('#add-assignment form').prepend(alerta);
        }

        // Guardar nueva asignación (mejorada)
        $(document).on('click', '#btnGuardarAsignacion', function() {
            console.log('🔥 ¡CLIC EN BOTÓN DETECTADO!');
            console.log('💾 Intentando guardar nueva asignación...');
            console.log('📊 Estado del botón:', {
                existe: $('#btnGuardarAsignacion').length > 0,
                visible: $('#btnGuardarAsignacion').is(':visible'),
                deshabilitado: $('#btnGuardarAsignacion').prop('disabled')
            });
            
            // Validar formulario
            if (!validarFormularioAsignacion()) {
                return;
            }
            
            // Deshabilitar botón para evitar doble envío
            const btn = $(this);
            const textoOriginal = btn.html();
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Guardando...');
            
            const formData = new FormData(document.getElementById('formNuevaAsignacion'));
            formData.append('accion', 'asignar_usuario');
            
            // Log de datos a enviar
            console.log('📋 Datos a enviar:');
            for (let [key, value] of formData.entries()) {
                console.log(`  ${key}: ${value}`);
            }

            $.ajax({
                url: 'php/asignaciones_proyectos.php',
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
                    console.log('📥 Respuesta del servidor:', response);
                    
                    if (response.exito) {
                        mostrarAlertaFormulario('¡Asignación creada exitosamente!', 'success');
                        
                        // Mostrar mensaje de éxito global
                        showMessage('¡Usuario asignado exitosamente al proyecto!', 'success', 3000);
                        
                        // Limpiar formulario después de 2 segundos
                        setTimeout(() => {
                            document.getElementById('formNuevaAsignacion').reset();
                            $('#add-assignment .alert').remove();
                            
                            // Cambiar al tab de asignaciones actuales
                            $('.modal-tab[data-tab="current"]').click();
                            
                            // Recargar las asignaciones
                            if (window.currentEntityId && window.currentEntityType) {
                                cargarAsignacionesModal(window.currentEntityId, window.currentEntityType);
                            }
                            
                            // Actualizar estadísticas si existe la función
                            if (typeof cargarEstadisticasAsignaciones === 'function') {
                                cargarEstadisticasAsignaciones();
                            }
                            
                            // Recargar el listado principal de proyectos con asignaciones
                            if (typeof cargarAsignacionesPorProyecto === 'function') {
                                console.log('🔄 Recargando listado de proyectos con asignaciones...');
                                cargarAsignacionesPorProyecto();
                            }
                        }, 2000);
                        
                    } else {
                        mostrarAlertaFormulario('Error: ' + response.mensaje, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('❌ Error AJAX completo:', {xhr: xhr, status: status, error: error});
                    console.error('❌ Texto de respuesta:', xhr.responseText);
                    
                    let mensajeError = 'Error al conectar con el servidor';
                    if (xhr.status === 404) {
                        mensajeError = 'Archivo PHP no encontrado. Verifica la ruta del servidor.';
                    } else if (xhr.status === 500) {
                        mensajeError = 'Error interno del servidor. Revisa los logs del servidor.';
                    } else if (xhr.responseText) {
                        try {
                            const errorResponse = JSON.parse(xhr.responseText);
                            mensajeError = errorResponse.mensaje || mensajeError;
                        } catch (e) {
                            mensajeError = 'Error del servidor: ' + xhr.responseText.substring(0, 100);
                        }
                    }
                    
                    mostrarAlertaFormulario(mensajeError, 'error');
                },
                complete: function() {
                    // Rehabilitar botón
                    btn.prop('disabled', false).html(textoOriginal);
                }
            });
        });

        // Mostrar información adicional del proyecto seleccionado
        $(document).on('change', '#proyecto-select', function() {
            const proyectoId = $(this).val();
            const selectedOption = $(this).find('option:selected');
            
            // Remover info anterior
            $('#proyecto-info, #info-usuarios-disponibles').remove();
            
            if (proyectoId) {
                const estado = selectedOption.data('estado');
                const cliente = selectedOption.data('cliente');
                
                const infoHtml = `
                    <div id="proyecto-info" class="alert alert-info mt-2 mb-0">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle me-2"></i>
                            <div>
                                <strong>Cliente:</strong> ${cliente} <br>
                                <strong>Estado:</strong> ${estado.replace('_', ' ')}
                            </div>
                        </div>
                    </div>
                `;
                
                $('#proyecto-select').closest('.mb-3').append(infoHtml);
                
                // Recargar usuarios cuando cambia el proyecto
                console.log('🔄 Recargando usuarios para proyecto:', proyectoId);
                cargarUsuariosSelect(proyectoId);
            }
        });

        // Mostrar información adicional del usuario seleccionado
        $(document).on('change', '#usuario-select', function() {
            const usuarioId = $(this).val();
            const selectedOption = $(this).find('option:selected');
            
            // Remover info anterior
            $('#usuario-info').remove();
            
            if (usuarioId) {
                const email = selectedOption.data('email');
                const rol = selectedOption.data('rol');
                
                const infoHtml = `
                    <div id="usuario-info" class="alert alert-info mt-2 mb-0">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-user-circle me-2"></i>
                            <div>
                                <strong>Email:</strong> ${email} <br>
                                <strong>Rol:</strong> ${rol}
                            </div>
                        </div>
                    </div>
                `;
                
                $('#usuario-select').closest('.mb-3').append(infoHtml);
                
                // Verificar si el usuario ya está asignado a este proyecto
                verificarAsignacionExistente(usuarioId, $('#proyecto-select').val());
            }
        });

        // Verificar si ya existe una asignación entre usuario y proyecto
        function verificarAsignacionExistente(usuarioId, proyectoId) {
            if (!usuarioId || !proyectoId) return;
            
            // Remover alerta anterior
            $('#alerta-asignacion-existente').remove();
            
            $.ajax({
                url: 'php/asignaciones_proyectos.php',
                method: 'GET',
                data: { 
                    accion: 'verificar_asignacion_existente',
                    usuario_id: usuarioId,
                    proyecto_id: proyectoId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.exito && response.datos && response.datos.existe) {
                        const alertaHtml = `
                            <div id="alerta-asignacion-existente" class="alert alert-warning mt-3">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>¡Atención!</strong> Este usuario ya está asignado a este proyecto con el rol: <strong>${response.datos.asignacion.rol_proyecto}</strong>
                                ${response.datos.asignacion.estado_asignacion !== 'activo' ? `(Estado: ${response.datos.asignacion.estado_asignacion})` : ''}
                            </div>
                        `;
                        
                        $('#add-assignment form').append(alertaHtml);
                    }
                },
                error: function(xhr, status, error) {
                    console.log('No se pudo verificar asignación existente:', error);
                }
            });
        }

        // Sugerencias de rol basado en el proyecto seleccionado
        $(document).on('change', '#proyecto-select', function() {
            const proyectoId = $(this).val();
            const selectedOption = $(this).find('option:selected');
            
            if (proyectoId) {
                // Obtener información sobre roles existentes en el proyecto
                obtenerRolesSugeridos(proyectoId);
            }
        });

        function obtenerRolesSugeridos(proyectoId) {
            $.ajax({
                url: 'php/asignaciones_proyectos.php',
                method: 'GET',
                data: { 
                    accion: 'obtener_roles_proyecto',
                    proyecto_id: proyectoId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.exito && response.datos && response.datos.roles_asignados) {
                        const rolesActuales = response.datos.roles_asignados.map(rol => rol.rol_proyecto);
                        mostrarSugerenciasRol(rolesActuales);
                    }
                },
                error: function(xhr, status, error) {
                    console.log('No se pudieron obtener sugerencias de rol:', error);
                }
            });
        }

        function mostrarSugerenciasRol(rolesExistentes) {
            // Remover sugerencias anteriores
            $('#sugerencias-rol').remove();
            
            const rolSelect = $('select[name="rol_proyecto"]');
            let sugerenciasHtml = '';
            
            if (rolesExistentes.length > 0) {
                sugerenciasHtml = `
                    <div id="sugerencias-rol" class="mt-2">
                        <small class="text-muted">
                            <i class="fas fa-lightbulb me-1"></i>
                            Roles ya asignados en este proyecto: ${rolesExistentes.join(', ')}
                        </small>
                    </div>
                `;
            } else {
                sugerenciasHtml = `
                    <div id="sugerencias-rol" class="mt-2">
                        <small class="text-success">
                            <i class="fas fa-star me-1"></i>
                            Este proyecto no tiene asignaciones aún. ¡Perfecto momento para asignar un líder!
                        </small>
                    </div>
                `;
            }
            
            rolSelect.closest('.mb-3').append(sugerenciasHtml);
        }

        // Limpiar formulario completamente cuando se cambia de tab
        $(document).on('click', '.modal-tab[data-tab="add"]', function() {
            console.log('🆕 Cambiando a tab "Nueva Asignación"');
            
            // Limpiar formulario
            if (document.getElementById('formNuevaAsignacion')) {
                document.getElementById('formNuevaAsignacion').reset();
                console.log('✅ Formulario reseteado');
            }
            
            // Remover todas las alertas e información adicional
            $('#proyecto-info, #usuario-info, #alerta-asignacion-existente, #sugerencias-rol').remove();
            $('#add-assignment .alert').remove();
            
            // Cargar datos frescos
            console.log('🔄 Cargando datos frescos para selects...');
            cargarProyectosSelect();
            cargarUsuariosSelect(window.currentEntityId);
            
            console.log('✅ Tab "Nueva Asignación" preparado');
        });

        // Cargar datos cuando se abre el modal de asignaciones
        $(document).on('shown.bs.modal', '#modalGestionAsignaciones', function () {
            console.log('🎭 Modal de asignaciones abierto, inicializando...');
            
            // Asegurar que los tabs estén configurados correctamente
            setupModalNavigation();
            
            // Verificar que el tab actual esté activo
            if (!$('.modal-tab.active').length) {
                console.log('⚠️ No hay tab activo, activando tab "current"');
                $('.modal-tab[data-tab="current"]').click();
            }
            
            // Cargar datos para el formulario de nueva asignación
            cargarProyectosSelect();
            cargarUsuariosSelect(window.currentEntityId);
            
            console.log('✅ Modal inicializado correctamente');
        });

        // Refrescar asignaciones cuando se cierra el modal
        $(document).on('hidden.bs.modal', '#modalGestionAsignaciones', function () {
            console.log('🔄 Modal de asignaciones cerrado, refrescando datos...');
            
            // Recargar la tabla de asignaciones por proyecto si existe
            if (typeof cargarAsignacionesPorProyecto === 'function') {
                cargarAsignacionesPorProyecto();
            }
            
            // Recargar estadísticas si existe la función
            if (typeof cargarEstadisticasAsignaciones === 'function') {
                cargarEstadisticasAsignaciones();
            }
            
            // Si estamos en la vista de proyectos, recargar la tabla
            if (window.location.hash === '#proyectos' || $('.assignments-container').length > 0) {
                console.log('🔄 Refrescando vista de proyectos...');
                setTimeout(() => {
                    if (typeof cargarAsignacionesPorProyecto === 'function') {
                        cargarAsignacionesPorProyecto();
                    }
                }, 500);
            }
            
            console.log('✅ Refresco completado');
        });

        // Inicializar eventos del modal
        $(document).ready(function() {
            console.log('🔧 Configurando navegación del modal...');
            setupModalNavigation();
        });

        // Event listener para el menú de usuarios
        $('#usuarios-btn').on('click', function(e) {
            e.preventDefault();
            console.log('👥 Cargando sección de usuarios...');
            console.log('📋 Estado actual del contenedor .construccion:', $('.construccion').length > 0 ? 'Existe' : 'No existe');
            
            $('.nav-link').removeClass('active');
            $(this).addClass('active');
            
            // Asegurar que el contenedor existe
            if ($('.construccion').length === 0) {
                console.log('⚠️ Contenedor .construccion no existe, recreándolo...');
                $('.main-content').html(`
                    <div class="container-fluid">
                        <div class="row construccion">
                        </div>
                    </div>
                `);
            }
            
            // Mostrar mensaje de construcción para usuarios
            $('.construccion').html(`
                <div class="container-fluid p-0">
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-0 shadow-lg" style="background: var(--gradiente-violeta); border-radius: 20px;">
                                <div class="card-body text-white p-4 text-center">
                                    <h1 class="display-6 fw-bold mb-2">
                                        <i class="fas fa-users fa-2x me-3"></i>Gestión de Usuarios
                                    </h1>
                                    <p class="lead mb-0 opacity-90">
                                        Administra y gestiona todos los usuarios del sistema
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                                <div class="card-body text-center p-5">
                                    <i class="fas fa-tools fa-4x text-muted mb-3"></i>
                                    <h3 class="text-muted">Sección en Construcción</h3>
                                    <p class="text-muted">La gestión de usuarios estará disponible próximamente.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `);
        });

        // Event listener para el menú de asignaciones
        $('#asignaciones-btn').on('click', function(e) {
            e.preventDefault();
            mostrarAsignaciones();
        });

        // Event listener para el botón Nueva Asignación de Proyecto
        $(document).on('click', '#btnNuevaAsignacionProyecto', function(e) {
            e.preventDefault();
            console.log('🎯 Abriendo modal de asignaciones desde botón independiente...');
            
            // Configurar variables globales para modo "nueva asignación"
            window.currentEntityId = null;
            window.currentEntityType = 'proyecto';
            
            // Configurar título del modal
            $('#modal-title-text').html('<i class="fas fa-plus me-2"></i>Nueva Asignación de Proyecto');
            
            // Configurar navegación de tabs - ir directo al tab "Nueva Asignación"
            $('.tab-pane').removeClass('active');
            $('#add-assignment').addClass('active');
            $('.modal-tab').removeClass('active');
            $('.modal-tab[data-tab="add"]').addClass('active');
            
            // Mostrar botón de guardar
            $('#btnGuardarAsignacion').show();
            
            // Limpiar formulario
            document.getElementById('formNuevaAsignacion').reset();
            $('#add-assignment .alert').remove();
            $('#proyecto-info, #usuario-info, #alerta-asignacion-existente, #sugerencias-rol').remove();
            
            // Cargar datos para los selects
            console.log('🔄 Cargando datos para nueva asignación...');
            cargarProyectosSelect();
            cargarUsuariosSelect(null); // Sin proyecto específico inicialmente
            
            // Abrir modal
            const modal = new bootstrap.Modal(document.getElementById('modalGestionAsignaciones'));
            modal.show();
            
            console.log('✅ Modal abierto en modo Nueva Asignación');
        });

        // Script para el dashboard
        console.log('🎯 === DASHBOARD INICIANDO ===');
        console.log('✅ Dashboard cargado correctamente');
        
        // Test de funciones principales
        console.log('🔍 Verificando funciones principales:', {
            cargarAsignacionesPorProyecto: typeof cargarAsignacionesPorProyecto,
            eliminarAsignacion: typeof eliminarAsignacion,
            setupModalNavigation: typeof setupModalNavigation
        });
        
        // Intentar cargar asignaciones manualmente después de 1 segundo
        setTimeout(function() {
            console.log('⏰ Trigger manual de carga de asignaciones...');
            if (typeof cargarAsignacionesPorProyecto === 'function') {
                cargarAsignacionesPorProyecto();
            } else {
                console.error('❌ cargarAsignacionesPorProyecto no está definida');
            }
        }, 1000);
        // Cargar el contenido del dashboard principal
        $(document).ready(function() {
            $.ajax({
                url: 'dashboard_home.php',
                method: 'GET',
                success: function(response) {
                    $('.construccion').html(response);
                    console.log('✅ Dashboard home cargado correctamente');
                },
                error: function(xhr, status, error) {
                    console.error('Error al cargar dashboard_home.php:', error);
                    $('.construccion').html(`
                        <div class="alert alert-danger m-4">
                            <h5><i class="fas fa-exclamation-triangle me-2"></i>Error al cargar el dashboard</h5>
                            <p>No se pudo cargar el contenido principal. Por favor, recarga la página.</p>
                            <button class="btn btn-outline-danger btn-sm" onclick="location.reload()">
                                <i class="fas fa-redo me-1"></i>Recargar página
                            </button>
                        </div>
                    `);
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
                                            <label for="proyecto-select" class="form-label">Proyecto</label>
                                            <select class="form-select" id="proyecto-select" name="proyecto_id" required>
                                                <option value="">Seleccione un proyecto...</option>
                                            </select>
                                            <div class="form-text">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Solo se muestran proyectos activos
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="usuario-select" class="form-label">Usuario</label>
                                            <select class="form-select" id="usuario-select" name="usuario_id" required>
                                                <option value="">Seleccione un usuario...</option>
                                            </select>
                                            <div class="form-text">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Solo se muestran usuarios activos
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="rol-proyecto-select" class="form-label">Rol en el Proyecto</label>
                                            <select class="form-select" id="rol-proyecto-select" name="rol_proyecto" required>
                                                <option value="">Seleccione un rol...</option>
                                                <option value="lider">🔶 Líder</option>
                                                <option value="desarrollador">💻 Desarrollador</option>
                                                <option value="consultor">🎯 Consultor</option>
                                                <option value="revisor">🔍 Revisor</option>
                                                <option value="colaborador">🤝 Colaborador</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="horas-asignadas" class="form-label">Horas Asignadas</label>
                                            <input type="number" class="form-control" id="horas-asignadas" name="horas_asignadas" min="0" step="0.5" placeholder="Ej: 40">
                                            <div class="form-text">
                                                <i class="fas fa-clock me-1"></i>
                                                Campo opcional. Ej: 40 (horas por semana)
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="fecha-inicio" class="form-label">Fecha de Inicio</label>
                                            <input type="date" class="form-control" id="fecha-inicio" name="fecha_inicio">
                                            <div class="form-text">
                                                <i class="fas fa-calendar me-1"></i>
                                                Campo opcional. Por defecto: fecha actual
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="notas" class="form-label">Notas</label>
                                            <textarea class="form-control" id="notas" name="notas" rows="3" placeholder="Notas adicionales sobre la asignación"></textarea>
                                            <div class="form-text">
                                                <i class="fas fa-edit me-1"></i>
                                                Campo opcional para comentarios adicionales
                                            </div>
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

        /* ===== ESTILOS PARA FORMULARIO DE NUEVA ASIGNACIÓN ===== */
        #formNuevaAsignacion .form-select:focus,
        #formNuevaAsignacion .form-control:focus {
            border-color: #8b5cf6;
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
            outline: none;
        }

        #formNuevaAsignacion .form-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }

        #formNuevaAsignacion .form-select,
        #formNuevaAsignacion .form-control {
            border-radius: 8px;
            border: 1px solid #d1d5db;
            padding: 0.75rem;
            transition: all 0.3s ease;
        }

        #formNuevaAsignacion .form-select:hover,
        #formNuevaAsignacion .form-control:hover {
            border-color: #8b5cf6;
        }

        #formNuevaAsignacion .alert {
            border: none;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        #formNuevaAsignacion .alert-info {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #1e40af;
            border-left: 4px solid #3b82f6;
        }

        #formNuevaAsignacion .alert-success {
            background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
            color: #166534;
            border-left: 4px solid #22c55e;
        }

        #formNuevaAsignacion .alert-warning {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #d97706;
            border-left: 4px solid #f59e0b;
        }

        #formNuevaAsignacion .alert-danger {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #dc2626;
            border-left: 4px solid #ef4444;
        }

        #btnGuardarAsignacion {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #8b5cf6 100%);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
        }

        #btnGuardarAsignacion:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(139, 92, 246, 0.4);
        }

        #btnGuardarAsignacion:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        #formNuevaAsignacion input[type="number"] {
            -moz-appearance: textfield;
        }

        #formNuevaAsignacion input[type="number"]::-webkit-outer-spin-button,
        #formNuevaAsignacion input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Animaciones para las alertas informativas */
        #proyecto-info, #usuario-info, #sugerencias-rol, #alerta-asignacion-existente {
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Mejorar apariencia de los selects */
        #formNuevaAsignacion .form-select {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.75rem center;
            background-repeat: no-repeat;
            background-size: 16px 12px;
        }

        /* Indicador de campo requerido */
        #formNuevaAsignacion .form-label[for]:after {
            content: " *";
            color: #ef4444;
            font-weight: bold;
        }

        #formNuevaAsignacion .form-label:not([for]):after {
            content: "";
        }

        /* ===== ESTILOS PARA NAVEGACIÓN DE TABS ===== */
        .tab-pane {
            display: none;
            padding: 20px;
        }

        .tab-pane.active {
            display: block;
        }

        .modal-tab {
            background: none;
            border: none;
            padding: 12px 20px;
            margin: 0;
            border-radius: 0;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .modal-tab:hover {
            background-color: rgba(139, 92, 246, 0.1);
            color: #8b5cf6 !important;
        }

        .modal-tab.active {
            background-color: white;
            color: #8b5cf6 !important;
            border-bottom: 3px solid #8b5cf6;
        }

        /* Mejorar la apariencia del botón Guardar Asignación */
        #btnGuardarAsignacion {
            /* Controlado por JavaScript, no por CSS */
        }
        
        /* Estilo para el botón Nueva Asignación de Proyecto */
        #btnNuevaAsignacionProyecto {
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(139, 92, 246, 0.2);
        }
        
        #btnNuevaAsignacionProyecto:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(139, 92, 246, 0.3);
        }

        /* Animación para el cambio de tabs */
        .tab-pane {
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }

        .tab-pane.active {
            opacity: 1;
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

    <!-- Modal para Ver Detalle del Usuario -->
    <div class="modal fade" id="modalDetalleUsuario" tabindex="-1" aria-labelledby="modalDetalleUsuarioLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white;">
                    <h4 class="modal-title" id="modalDetalleUsuarioLabel">
                        <i class="fas fa-user me-2"></i>
                        <span id="detalle-usuario-titulo">Detalle del Usuario</span>
                    </h4>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    <div class="row">
                        <!-- Información principal del usuario -->
                        <div class="col-md-8">
                            <!-- Información Personal -->
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0 text-primary">
                                        <i class="fas fa-user-circle me-2"></i>Información Personal
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold text-muted">
                                                <i class="fas fa-id-card me-1"></i>Nombre Completo
                                            </label>
                                            <p class="fs-5 fw-bold text-dark mb-0" id="detalle-usuario-nombre">-</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold text-muted">
                                                <i class="fas fa-at me-1"></i>Usuario
                                            </label>
                                            <p class="mb-0" id="detalle-usuario-username">-</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold text-muted">
                                                <i class="fas fa-envelope me-1"></i>Email
                                            </label>
                                            <p class="mb-0" id="detalle-usuario-email">-</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold text-muted">
                                                <i class="fas fa-birthday-cake me-1"></i>Fecha de Nacimiento
                                            </label>
                                            <p class="mb-0" id="detalle-usuario-nacimiento">-</p>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label fw-bold text-muted">
                                                <i class="fas fa-info-circle me-1"></i>Biografía
                                            </label>
                                            <p class="text-muted" id="detalle-usuario-bio">Sin biografía disponible</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Información del Sistema -->
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0 text-primary">
                                        <i class="fas fa-cogs me-2"></i>Información del Sistema
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold text-muted">
                                                <i class="fas fa-user-tag me-1"></i>Rol
                                            </label>
                                            <span class="badge fs-6" id="detalle-usuario-rol">-</span>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold text-muted">
                                                <i class="fas fa-toggle-on me-1"></i>Estado
                                            </label>
                                            <span class="badge fs-6" id="detalle-usuario-estado">-</span>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold text-muted">
                                                <i class="fas fa-calendar-plus me-1"></i>Fecha de Registro
                                            </label>
                                            <p class="mb-0" id="detalle-usuario-registro">-</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold text-muted">
                                                <i class="fas fa-clock me-1"></i>Última Actualización
                                            </label>
                                            <p class="mb-0" id="detalle-usuario-actualizacion">-</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold text-muted">
                                                <i class="fas fa-sign-in-alt me-1"></i>Último Acceso
                                            </label>
                                            <p class="mb-0" id="detalle-usuario-ultimo-acceso">-</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold text-muted">
                                                <i class="fas fa-network-wired me-1"></i>Última IP
                                            </label>
                                            <p class="mb-0 font-monospace" id="detalle-usuario-ip">-</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información lateral -->
                        <div class="col-md-4">
                            <!-- Avatar y Estado -->
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0 text-primary">
                                        <i class="fas fa-image me-2"></i>Perfil
                                    </h6>
                                </div>
                                <div class="card-body text-center">
                                    <div class="mb-4">
                                        <div id="detalle-usuario-avatar" class="mx-auto mb-3" style="width: 100px; height: 100px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 36px; font-weight: 700; box-shadow: 0 4px 15px rgba(0,0,0,0.2); background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                                            U
                                        </div>
                                        <div id="detalle-usuario-foto" style="display: none;">
                                            <img src="" alt="Foto de perfil" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-muted d-block">ID del Usuario</label>
                                        <p class="fs-4 fw-bold text-primary mb-0" id="detalle-usuario-id">#-</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-muted d-block">
                                            <i class="fas fa-link me-1"></i>URL de Perfil
                                        </label>
                                        <div id="detalle-usuario-url-container">
                                            <small class="text-muted">Sin URL de perfil</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Estadísticas -->
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0 text-primary">
                                        <i class="fas fa-chart-bar me-2"></i>Estadísticas
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="text-center mb-3">
                                        <div class="h3 text-success mb-1" id="detalle-usuario-proyectos-total">0</div>
                                        <small class="text-muted">Proyectos Asignados</small>
                                    </div>
                                    <div class="text-center mb-3">
                                        <div class="h3 text-warning mb-1" id="detalle-usuario-horas-total">0</div>
                                        <small class="text-muted">Horas Asignadas</small>
                                    </div>
                                    <div class="text-center">
                                        <div class="h3 text-info mb-1" id="detalle-usuario-roles-total">0</div>
                                        <small class="text-muted">Roles Diferentes</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Acciones -->
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0 text-primary">
                                        <i class="fas fa-tools me-2"></i>Acciones
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-outline-success btn-sm" onclick="gestionarAsignacionesUsuarioDesdeDetalle()" id="btn-gestionar-asignaciones-usuario">
                                            <i class="fas fa-users-cog me-2"></i>Gestionar Asignaciones
                                        </button>
                                        <button class="btn btn-outline-primary btn-sm" onclick="editarUsuarioDesdeDetalle()" id="btn-editar-usuario">
                                            <i class="fas fa-edit me-2"></i>Editar Usuario
                                        </button>
                                        <button class="btn btn-outline-info btn-sm" onclick="verHistorialUsuario()" id="btn-historial-usuario">
                                            <i class="fas fa-history me-2"></i>Ver Historial
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Estilos específicos para el modal de detalle del usuario */
        #modalDetalleUsuario .modal-content {
            border-radius: 15px;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.2);
        }

        #modalDetalleUsuario .modal-header {
            border-radius: 15px 15px 0 0;
            border-bottom: none;
            padding: 1.5rem 2rem;
        }

        #modalDetalleUsuario .card {
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        #modalDetalleUsuario .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        #modalDetalleUsuario .badge {
            padding: 8px 15px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 12px;
        }

        #modalDetalleUsuario .badge.bg-success {
            background: linear-gradient(135deg, #10b981, #059669) !important;
        }

        #modalDetalleUsuario .badge.bg-danger {
            background: linear-gradient(135deg, #ef4444, #dc2626) !important;
        }

        #modalDetalleUsuario .badge.bg-primary {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed) !important;
        }

        #modalDetalleUsuario .badge.bg-warning {
            background: linear-gradient(135deg, #f59e0b, #d97706) !important;
            color: white !important;
        }

        #modalDetalleUsuario .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        /* Responsive */
        @media (max-width: 768px) {
            #modalDetalleUsuario .modal-dialog {
                margin: 0.5rem;
            }
            
            #modalDetalleUsuario .modal-header,
            #modalDetalleUsuario .modal-footer {
                padding: 1rem;
            }

            #modalDetalleUsuario .card-body {
                padding: 1rem;
            }
        }
    </style>
</body>
</html>
