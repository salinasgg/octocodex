<?php
// ===== PÁGINA DE EDICIÓN DE PERFIL =====

// Solo iniciar sesión si no se ha iniciado ya y no hay headers enviados
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}

// Obtener user_id de parámetros GET o sesión
$userId = $_GET['user_id'] ?? $_SESSION['user_id'] ?? null;

if (!$userId) {
    echo '<div class="alert alert-danger">Error: No se proporcionó un ID de usuario válido.</div>';
    exit;
}

// Incluir configuración de base de datos
require_once 'php/config_bd.php';

// Obtener datos del usuario
$userData = null;
$message = '';
$messageType = '';

try {
    $database = Database::getInstance();
    $pdo = $database->getConnection();
    
    $stmt = $pdo->prepare("
        SELECT id, us_username, us_email, us_rol, us_nombre, us_apellido, 
               us_bio, us_foto_perfil, us_url_perfil, us_activo
        FROM usuarios 
        WHERE id = :id
    ");
    
    $stmt->execute(['id' => $userId]);
    $userData = $stmt->fetch();
    
} catch (Exception $e) {
    error_log("Error obteniendo datos del usuario: " . $e->getMessage());
    $message = "Error al cargar los datos del usuario";
    $messageType = "error";
}

// Procesar formulario de actualización
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $nombre = trim($_POST['nombre']);
        $apellido = trim($_POST['apellido']);
        $email = trim($_POST['email']);
        $bio = trim($_POST['bio']);
        $url_perfil = trim($_POST['url_perfil']);
        
        // Validaciones básicas
        if (empty($nombre) || empty($apellido) || empty($email)) {
            throw new Exception("Los campos nombre, apellido y email son obligatorios");
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("El formato del email no es válido");
        }
        
        // Verificar si el email ya existe para otro usuario
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE us_email = :email AND id != :id");
        $stmt->execute(['email' => $email, 'id' => $userId]);
        if ($stmt->fetch()) {
            throw new Exception("El email ya está en uso por otro usuario");
        }
        
        // Procesar imagen de perfil si se subió
        $foto_perfil = $userData['us_foto_perfil']; // Mantener la actual si no se sube nueva
        
        if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
            // Código exacto que funcionó en debug_editar_perfil_final.php
            $uploadDir = __DIR__ . '/uploads/perfiles/';
            $newFileName = 'perfil_' . $userId . '_' . time() . '_' . $_FILES['foto_perfil']['name'];
            $uploadPath = $uploadDir . $newFileName;
            
            // Crear directorio si no existe
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // Intentar subir el archivo
            $result = move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $uploadPath);
            
            if ($result) {
                $foto_perfil = 'uploads/perfiles/' . $newFileName;
                
                // Eliminar imagen anterior
                if ($userData['us_foto_perfil'] && file_exists(__DIR__ . '/' . $userData['us_foto_perfil'])) {
                    unlink(__DIR__ . '/' . $userData['us_foto_perfil']);
                }
            } else {
                throw new Exception("Error al subir la imagen");
            }
        }
        
        // Actualizar datos en la base de datos
        $stmt = $pdo->prepare("
            UPDATE usuarios 
            SET us_nombre = :nombre, us_apellido = :apellido, us_email = :email,
                us_bio = :bio, us_foto_perfil = :foto_perfil, us_url_perfil = :url_perfil,
                fecha_actualizacion = NOW()
            WHERE id = :id
        ");
        
        $stmt->execute([
            'nombre' => $nombre,
            'apellido' => $apellido,
            'email' => $email,
            'bio' => $bio,
            'foto_perfil' => $foto_perfil,
            'url_perfil' => $url_perfil,
            'id' => $userId
        ]);
        
        // Actualizar datos de sesión
        $_SESSION['email'] = $email;
        $_SESSION['nombre_completo'] = $nombre . ' ' . $apellido;
        
        $message = "Perfil actualizado exitosamente";
        $messageType = "success";
        
        // Recargar datos del usuario
        $stmt = $pdo->prepare("
            SELECT id, us_username, us_email, us_rol, us_nombre, us_apellido, 
                   us_bio, us_foto_perfil, us_url_perfil, us_activo
            FROM usuarios 
            WHERE id = :id
        ");
        $stmt->execute(['id' => $userId]);
        $userData = $stmt->fetch();
        
    } catch (Exception $e) {
        $message = $e->getMessage();
        $messageType = "error";
    }
}
?>

<link rel="stylesheet" href="../css/variables.css">
<!-- <link rel="stylesheet" href="../css/style_editar.css"> -->



<script>
// Función para inicializar los eventos cuando el contenido se carga dinámicamente
function initializeEditForm() {
    // Mostrar nombre del archivo seleccionado
    const fotoInput = document.getElementById('foto_perfil');
    if (fotoInput) {
        fotoInput.addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name || 'Seleccionar imagen (JPG, PNG, GIF - Máx. 5MB)';
            const fileNameElement = document.getElementById('file-name');
            if (fileNameElement) {
                fileNameElement.textContent = fileName;
            }
        });
    }

    // Manejar envío del formulario via AJAX
    const form = document.getElementById('formEditarPerfil');
    console.log('🔍 Buscando formulario:', form);
    if (form) {
        console.log('✅ Formulario encontrado, agregando evento submit');
        form.addEventListener('submit', function(e) {
            console.log('🚀 Formulario enviado!');
            e.preventDefault();
            e.stopPropagation();
            
            const formData = new FormData(this);
            console.log('📋 FormData creado');
            
            // Verificar si hay archivo seleccionado
            const fileInput = document.getElementById('foto_perfil');
            if (fileInput && fileInput.files.length > 0) {
                console.log('📁 Archivo seleccionado:', fileInput.files[0].name);
                formData.append('foto_perfil', fileInput.files[0]);
            } else {
                console.log('⚠️ No hay archivo seleccionado');
            }
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Cambiar texto del botón
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Guardando...';
            submitBtn.disabled = true;
            
            console.log('🌐 Enviando AJAX a:', '../test_editar_perfil_completo.php');
            $.ajax({
                url: '../test_editar_perfil_completo.php',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    console.log('✅ Respuesta del servidor:', response);
                    
                    if (response.includes('Perfil actualizado exitosamente')) {
                        showMessage('Perfil actualizado exitosamente', 'success', 3000);
                        // Cerrar modal después de un breve delay
                        setTimeout(() => {
                            $('#myModalEditarPerfil').modal('hide');
                            // Recargar el perfil
                            if (typeof cargarPerfil === 'function') {
                                cargarPerfil();
                            } else {
                                // Recargar la página del perfil
                                location.reload();
                            }
                        }, 1500);
                    } else {
                        console.error('❌ Respuesta no contiene éxito:', response);
                        showMessage('Error al actualizar el perfil', 'error', 5000);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('❌ Error AJAX:', error);
                    console.error('Status:', status);
                    console.error('Response:', xhr.responseText);
                    showMessage('Error al actualizar el perfil', 'error', 5000);
                },
                complete: function() {
                    // Restaurar botón
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            });
        });
    }

    // Animación de entrada
    const editContainer = document.querySelector('.edit-container');
    if (editContainer) {
        editContainer.style.opacity = '0';
        editContainer.style.transform = 'translateY(30px)';
        
        setTimeout(() => {
            editContainer.style.transition = 'all 0.6s ease';
            editContainer.style.opacity = '1';
            editContainer.style.transform = 'translateY(0)';
        }, 100);
    }
}

// Ejecutar cuando el DOM esté listo
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeEditForm);
} else {
    initializeEditForm();
}
</script>
