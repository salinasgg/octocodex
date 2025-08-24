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
                        <li><a class="dropdown-item" href="#" id="logout-btn"><i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">



            <!-- Main Content -->
            <div class="col-md-12 col-lg-12">
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
                                <h2 class="mb-1">¡BienvenidA, USUARIO <?php echo htmlspecialchars($nombre_completo); ?>!</h2>
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
    <script src="../js/logout.js"></script>
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
    </script>
</body>
</html>
