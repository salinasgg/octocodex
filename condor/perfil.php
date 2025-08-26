<?php
// ===== PÁGINA DE PERFIL DE USUARIO =====
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: index.php');
    exit;
}

// Incluir configuración de base de datos
require_once 'php/config_bd.php';

// Obtener datos del usuario
$userId = $_SESSION['user_id'];
$userData = null;

try {
    $database = Database::getInstance();
    $pdo = $database->getConnection();
    
    $stmt = $pdo->prepare("
        SELECT id, us_username, us_email, us_rol, us_nombre, us_apellido, 
               us_bio, us_foto_perfil, us_url_perfil, us_fecha_ultimo_acceso,
               us_ultimo_ip, us_activo, created_at, updated_at
        FROM usuarios 
        WHERE id = :id
    ");
    
    $stmt->execute(['id' => $userId]);
    $userData = $stmt->fetch();
    
    // Verificar si se obtuvieron datos
    if (!$userData) {
        error_log("No se encontraron datos para el usuario ID: " . $userId);
        // Redirigir a login si no hay datos
        session_destroy();
        header('Location: index.php?error=no_data');
        exit;
    }
    
} catch (Exception $e) {
    error_log("Error obteniendo datos del usuario: " . $e->getMessage());
    // Redirigir a login en caso de error
    session_destroy();
    header('Location: index.php?error=db_error');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-color: #8b5cf6;
            --primary-dark: #7c3aed;
            --secondary-color: #f8fafc;
            --text-dark: #1e293b;
            --text-light: #64748b;
            --border-color: #e2e8f0;
            --text-white: #ffffff;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, var(--secondary-color) 0%, #f1f5f9 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .profile-container {
            background: var(--text-white);
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 800px;
            width: 100%;
            position: relative;
            backdrop-filter: blur(10px);
            border: 1px solid var(--border-color);
        }

        .profile-header {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            height: 120px;
            position: relative;
        }

        .profile-content {
            display: flex;
            padding: 40px;
            gap: 40px;
            position: relative;
            margin-top: -60px;
        }

        .profile-image-section {
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            z-index: 2;
        }

        .profile-image {
            width: 160px;
            height: 160px;
            border-radius: 50%;
            border: 6px solid var(--text-white);
            box-shadow: 0 12px 30px rgba(139, 92, 246, 0.3);
            object-fit: cover;
            background: var(--secondary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            color: var(--primary-color);
            font-weight: 600;
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .profile-image:hover {
            transform: scale(1.05);
        }

        .status-indicator {
            position: absolute;
            bottom: 10px;
            right: 10px;
            width: 24px;
            height: 24px;
            background: #22c55e;
            border: 4px solid var(--text-white);
            border-radius: 50%;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .edit-btn {
            margin-top: 20px;
            padding: 10px 20px;
            background: var(--primary-color);
            color: var(--text-white);
            border: none;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
        }

        .edit-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(139, 92, 246, 0.4);
        }

        .profile-info {
            flex: 1;
            padding-top: 60px;
        }

        .profile-name {
            font-size: 32px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 8px;
            line-height: 1.2;
        }

        .profile-title {
            font-size: 18px;
            color: var(--primary-color);
            font-weight: 500;
            margin-bottom: 16px;
        }

        .profile-bio {
            font-size: 16px;
            color: var(--text-light);
            line-height: 1.6;
            margin-bottom: 32px;
        }

        .profile-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .stat-item {
            text-align: center;
            padding: 20px;
            background: var(--secondary-color);
            border-radius: 16px;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .stat-item:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .stat-number {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 4px;
        }

        .stat-label {
            font-size: 14px;
            color: var(--text-light);
            font-weight: 500;
        }

        .profile-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 24px;
        }

        .detail-group {
            background: var(--secondary-color);
            padding: 24px;
            border-radius: 16px;
            border: 1px solid var(--border-color);
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid var(--border-color);
        }

        .detail-item:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-size: 14px;
            color: var(--text-light);
            font-weight: 500;
        }

        .detail-value {
            font-size: 14px;
            color: var(--text-dark);
            font-weight: 600;
        }

        .badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .badge.active {
            background: #22c55e;
            color: var(--text-white);
        }

        .badge.inactive {
            background: #ef4444;
            color: var(--text-white);
        }

        .status-indicator.active {
            background: #22c55e;
        }

        .status-indicator.inactive {
            background: #ef4444;
        }

        .icon {
            width: 20px;
            height: 20px;
            margin-right: 8px;
            vertical-align: middle;
        }

        @media (max-width: 768px) {
            .profile-content {
                flex-direction: column;
                align-items: center;
                text-align: center;
                padding: 20px;
            }

            .profile-info {
                padding-top: 20px;
            }

            .profile-name {
                font-size: 24px;
            }

            .profile-details {
                grid-template-columns: 1fr;
            }
        }

        .floating-shapes {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            pointer-events: none;
            overflow: hidden;
        }

        .shape {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(45deg, var(--primary-color), var(--primary-dark));
            opacity: 0.1;
            animation: float 6s ease-in-out infinite;
        }

        .shape:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }

        .shape:nth-child(2) {
            width: 60px;
            height: 60px;
            top: 60%;
            right: 10%;
            animation-delay: 2s;
        }

        .shape:nth-child(3) {
            width: 40px;
            height: 40px;
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <div class="floating-shapes">
            <div class="shape"></div>
            <div class="shape"></div>
            <div class="shape"></div>
        </div>
        
        <div class="profile-header"></div>
        
        <div class="profile-content">
                         <div class="profile-image-section">
                 <div class="profile-image">
                     <?php if ($userData && $userData['us_foto_perfil']): ?>
                         <img src="<?php echo htmlspecialchars($userData['us_foto_perfil']); ?>" 
                              alt="Foto de perfil" 
                              onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                         <div style="display: none;">
                             <?php echo strtoupper(substr($userData['us_nombre'] ?? '', 0, 1) . substr($userData['us_apellido'] ?? '', 0, 1)); ?>
                         </div>
                     <?php else: ?>
                         <?php echo strtoupper(substr($userData['us_nombre'] ?? '', 0, 1) . substr($userData['us_apellido'] ?? '', 0, 1)); ?>
                     <?php endif; ?>
                     <div class="status-indicator <?php echo ($userData['us_activo'] ?? false) ? 'active' : 'inactive'; ?>"></div>
                 </div>
                 <button class="edit-btn" onclick="editarPerfil()">Editar Perfil</button>
             </div>
            
                         <div class="profile-info">
                 <h1 class="profile-name"><?php echo htmlspecialchars(($userData['us_nombre'] ?? '') . ' ' . ($userData['us_apellido'] ?? '')); ?></h1>
                 <p class="profile-title"><?php echo ucfirst(htmlspecialchars($userData['us_rol'] ?? '')); ?></p>
                 <p class="profile-bio">
                     <?php echo ($userData['us_bio'] ?? '') ? htmlspecialchars($userData['us_bio']) : 'No hay biografía disponible.'; ?>
                 </p>
                 
                 <div class="profile-stats">
                     <div class="stat-item">
                         <div class="stat-number">#<?php echo $userData['id'] ?? 'N/A'; ?></div>
                         <div class="stat-label">ID de Usuario</div>
                     </div>
                     <div class="stat-item">
                         <div class="stat-number">@<?php echo htmlspecialchars($userData['us_username'] ?? ''); ?></div>
                         <div class="stat-label">Usuario</div>
                     </div>
                     <div class="stat-item">
                         <div class="stat-number"><?php echo ($userData['us_activo'] ?? false) ? 'Activo' : 'Inactivo'; ?></div>
                         <div class="stat-label">Estado</div>
                     </div>
                 </div>
                
                <div class="profile-details">
                                         <div class="detail-group">
                         <div class="detail-item">
                             <span class="detail-label">Email</span>
                             <span class="detail-value"><?php echo htmlspecialchars($userData['us_email'] ?? ''); ?></span>
                         </div>
                         <div class="detail-item">
                             <span class="detail-label">Nombre de usuario</span>
                             <span class="detail-value">@<?php echo htmlspecialchars($userData['us_username'] ?? ''); ?></span>
                         </div>
                         <div class="detail-item">
                             <span class="detail-label">Rol</span>
                             <span class="detail-value"><?php echo ucfirst(htmlspecialchars($userData['us_rol'] ?? '')); ?></span>
                         </div>
                         <div class="detail-item">
                             <span class="detail-label">Estado</span>
                             <span class="badge <?php echo ($userData['us_activo'] ?? false) ? 'active' : 'inactive'; ?>">
                                 <?php echo ($userData['us_activo'] ?? false) ? 'Activo' : 'Inactivo'; ?>
                             </span>
                         </div>
                     </div>
                    
                                         <div class="detail-group">
                         <div class="detail-item">
                             <span class="detail-label">Último acceso</span>
                             <span class="detail-value">
                                 <?php echo ($userData['us_fecha_ultimo_acceso'] ?? '') ? date('d/m/Y H:i', strtotime($userData['us_fecha_ultimo_acceso'])) : 'Nunca'; ?>
                             </span>
                         </div>
                         <div class="detail-item">
                             <span class="detail-label">IP último acceso</span>
                             <span class="detail-value"><?php echo $userData['us_ultimo_ip'] ?: 'No registrado'; ?></span>
                         </div>
                         <div class="detail-item">
                             <span class="detail-label">Miembro desde</span>
                             <span class="detail-value"><?php echo ($userData['created_at'] ?? '') ? date('d/m/Y', strtotime($userData['created_at'])) : 'N/A'; ?></span>
                         </div>
                         <?php if ($userData['us_url_perfil'] ?? ''): ?>
                         <div class="detail-item">
                             <span class="detail-label">URL de perfil</span>
                             <span class="detail-value">
                                 <a href="<?php echo htmlspecialchars($userData['us_url_perfil']); ?>" 
                                    target="_blank" style="color: var(--primary-color); text-decoration: none;">
                                     Ver perfil
                                 </a>
                             </span>
                         </div>
                         <?php endif; ?>
                     </div>
                </div>
            </div>
        </div>
    </div>

    <script>
                 function editarPerfil() {
             // Redirigir a la página de edición según el rol
             const rol = '<?php echo strtolower($userData['us_rol'] ?? ''); ?>';
             let editUrl = '';
            
            if (rol === 'administrador') {
                editUrl = 'admin/editar-perfil.php';
            } else if (rol === 'usuario') {
                editUrl = 'usuario/editar-perfil.php';
            } else {
                editUrl = 'editar-perfil.php';
            }
            
            window.location.href = editUrl;
        }

        // Animación de entrada
        document.addEventListener('DOMContentLoaded', function() {
            const profileContainer = document.querySelector('.profile-container');
            profileContainer.style.opacity = '0';
            profileContainer.style.transform = 'translateY(30px)';
            
            setTimeout(() => {
                profileContainer.style.transition = 'all 0.6s ease';
                profileContainer.style.opacity = '1';
                profileContainer.style.transform = 'translateY(0)';
            }, 100);
        });
    </script>
</body>
</html>