<?php
// ===== CONTENIDO DE PERFIL PARA DASHBOARD =====

// Obtener user_id de parámetros GET o sesión
$userId = $_GET['user_id'] ?? $_SESSION['user_id'] ?? null;

if (!$userId) {
    echo '<div class="alert alert-danger">Error: No se proporcionó un ID de usuario válido.</div>';
    exit;
}

// Incluir configuración de base de datos
require_once 'php/config_bd.php';

try {
    $database = Database::getInstance();
    $pdo = $database->getConnection();
    
    $stmt = $pdo->prepare("
        SELECT id, us_username, us_email, us_rol, us_nombre, us_apellido, 
               us_bio, us_foto_perfil, us_url_perfil, us_fecha_ultimo_acceso,
               us_ultimo_ip, us_activo, us_fecha_registro, fecha_actualizacion
        FROM usuarios 
        WHERE id = :id
    ");
    
    $stmt->execute(['id' => $userId]);
    $userData = $stmt->fetch();
    
    if (!$userData) {
        echo '<div class="alert alert-danger">No se pudieron cargar los datos del usuario.</div>';
        exit;
    }
    
} catch (Exception $e) {
    echo '<div class="alert alert-danger">Error al cargar los datos del usuario.</div>';
    exit;
}
?>

<link rel="stylesheet" href="../css/estilos_perfil.css">
<link rel="stylesheet" href="../css/variables.css">


<div class="profile-container">
    <div class="profile-header"></div>
    
    <div class="profile-content">
        <div class="profile-image-section">
            <div class="profile-image">
                <?php if ($userData && $userData['us_foto_perfil']): ?>
                    <img src="<?php echo '../' . htmlspecialchars($userData['us_foto_perfil']); ?>" 
                         alt="Foto de perfil"
                         style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div style="display: none;">
                        <?php echo strtoupper(substr($userData['us_nombre'] ?? '', 0, 1) . substr($userData['us_apellido'] ?? '', 0, 1)); ?>
                    </div>
                <?php else: ?>
                    <?php echo strtoupper(substr($userData['us_nombre'] ?? '', 0, 1) . substr($userData['us_apellido'] ?? '', 0, 1)); ?>
                <?php endif; ?>
                <div class="status-indicator <?php echo ($userData['us_activo'] ?? false) ? 'active' : 'inactive'; ?>"></div>
            </div>
            <button class="edit-btn" id="btnEditarPerfil">Editar Perfil</button>
            <!-- onclick="editarPerfil()" -->
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
                        <span class="detail-value"><?php echo ($userData['us_fecha_registro'] ?? '') ? date('d/m/Y', strtotime($userData['us_fecha_registro'])) : 'N/A'; ?></span>
                    </div>
                    <?php if ($userData['us_url_perfil'] ?? ''): ?>
                    <div class="detail-item">
                        <span class="detail-label">URL de perfil</span>
                        <span class="detail-value">
                            <a href="<?php echo htmlspecialchars($userData['us_url_perfil']); ?>" 
                               target="_blank" style="color: #8b5cf6; text-decoration: none;">
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
<script src="../js/showMessage.js"></script>
<script src="../js/modales.js"></script>
<script>
// function editarPerfil() {
//     console.log('=== FUNCIÓN editarPerfil EJECUTADA ===');
    
//     // Obtener el user_id
//     let idperfil = window.userId;    
//     console.log('User ID obtenido:', idperfil);
    
//     if (!idperfil) {
//         console.error('No se pudo obtener el User ID');
//         showMessage('Error: No se pudo identificar al usuario', 'error', 5000);
//         return;
//     }
    
//     console.log('Mostrando modal...');
//     // Mostrar el modal
//     $('#myModalEditarPerfil').modal('show');
    
//     console.log('Iniciando AJAX request...');
//     // Cargar el contenido de la página de edición en el modal
//     $.ajax({
//         url: '../editar-perfil.php',
//         method: 'GET',
//         data: { user_id: idperfil },
//         beforeSend: function() {
//             console.log('AJAX request iniciado');
//         },
//         success: function(response) {
//             console.log('✅ AJAX SUCCESS - Contenido cargado exitosamente');
//             console.log('Longitud de respuesta:', response.length);
//             console.log('Primeros 100 caracteres:', response.substring(0, 100));
//             $('#myModalEditarPerfil .modal-content').html(response);
//             console.log('Contenido insertado en modal');
            
//             // Inicializar los eventos del formulario después de cargar el contenido
//             if (typeof initializeEditForm === 'function') {
//                 initializeEditForm();
//             }
//         },
//         error: function(xhr, status, error) {
//             console.error('❌ AJAX ERROR');
//             console.error('Error:', error);
//             console.error('Status:', status);
//             console.error('Status Code:', xhr.status);
//             console.error('Response Text:', xhr.responseText);
//             $('#myModalEditarPerfil .modal-body').html('<div class="alert alert-danger">Error al cargar el formulario de edición. Por favor intente nuevamente.</div>');
//         },
//         complete: function() {
//             console.log('AJAX request completado');
//         }
//     });
// }

// Animación de entrada
document.addEventListener('DOMContentLoaded', function() {
    const profileContainer = document.querySelector('.profile-container');
    if (profileContainer) {
        profileContainer.style.opacity = '0';
        profileContainer.style.transform = 'translateY(30px)';
        
        setTimeout(() => {
            profileContainer.style.transition = 'all 0.6s ease';
            profileContainer.style.opacity = '1';
            profileContainer.style.transform = 'translateY(0)';
        }, 100);
    }
});
</script>
