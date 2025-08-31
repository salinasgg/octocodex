<?php
// Iniciar sesión para acceder a los datos del usuario
session_start();

// Incluir el archivo de configuración de la base de datos
require_once 'config_bd.php';

// Establecer el tipo de contenido como JSON
header('Content-Type: application/json');

// Verificar que el usuario esté logueado
if (!isset($_SESSION['user_id'])) {
    $response = array(
        'success' => false,
        'message' => 'Debe iniciar sesión para cambiar su contraseña'
    );
    echo json_encode($response);
    exit;
}

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response = array(
        'success' => false,
        'message' => 'Método de petición no válido'
    );
    echo json_encode($response);
    exit;
}

try {
    // Crear una instancia de la clase Database para obtener la conexión
    $database = Database::getInstance();
    $pdo = $database->getConnection();
    
    // Obtener el ID del usuario desde la sesión
    $user_id = $_SESSION['user_id'];
    
    // Validar y obtener datos del formulario
    $passwordActual = isset($_POST['passwordActual']) ? trim($_POST['passwordActual']) : '';
    $passwordNueva = isset($_POST['passwordNueva']) ? trim($_POST['passwordNueva']) : '';
    $passwordConfirmar = isset($_POST['passwordConfirmar']) ? trim($_POST['passwordConfirmar']) : '';
    
    // ===== VALIDACIONES =====
    
    // Validar que todos los campos estén presentes
    if (empty($passwordActual)) {
        throw new Exception('La contraseña actual es requerida');
    }
    
    if (empty($passwordNueva)) {
        throw new Exception('La nueva contraseña es requerida');
    }
    
    if (empty($passwordConfirmar)) {
        throw new Exception('Debe confirmar la nueva contraseña');
    }
    
    // Validar longitud de la nueva contraseña
    if (strlen($passwordNueva) < 6) {
        throw new Exception('La nueva contraseña debe tener al menos 6 caracteres');
    }
    
    // Validar que las contraseñas nuevas coincidan
    if ($passwordNueva !== $passwordConfirmar) {
        throw new Exception('Las contraseñas nuevas no coinciden');
    }
    
    // Validar que la nueva contraseña sea diferente a la actual
    if ($passwordActual === $passwordNueva) {
        throw new Exception('La nueva contraseña debe ser diferente a la actual');
    }
    
    // ===== VERIFICAR CONTRASEÑA ACTUAL =====
    
    // Obtener los datos del usuario actual
    $sql = "SELECT us_password, us_username, us_nombre FROM usuarios WHERE id = ? AND us_activo = 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$usuario) {
        throw new Exception('Usuario no encontrado o inactivo');
    }
    
    // Verificar la contraseña actual
    if (!password_verify($passwordActual, $usuario['us_password'])) {
        throw new Exception('La contraseña actual es incorrecta');
    }
    
    // ===== ACTUALIZAR CONTRASEÑA =====
    
    // Generar hash de la nueva contraseña
    $passwordNuevaHash = password_hash($passwordNueva, PASSWORD_DEFAULT);
    
    // Iniciar transacción
    $pdo->beginTransaction();
    
    // Actualizar la contraseña en la base de datos
    $sql_update = "UPDATE usuarios SET 
                   us_password = ?,
                   fecha_actualizacion = NOW()
                   WHERE id = ?";
    
    $stmt_update = $pdo->prepare($sql_update);
    $resultado = $stmt_update->execute([$passwordNuevaHash, $user_id]);
    
    if (!$resultado) {
        throw new Exception('Error al actualizar la contraseña en la base de datos');
    }
    
    // Verificar que se actualizó exactamente un registro
    if ($stmt_update->rowCount() !== 1) {
        throw new Exception('No se pudo actualizar la contraseña');
    }
    
    // Confirmar la transacción
    $pdo->commit();
    
    // ===== LOG DE SEGURIDAD =====
    
    // Registrar el cambio de contraseña en los logs del servidor
    error_log("SECURITY: Password changed for user ID: $user_id, Username: " . $usuario['us_username'] . ", Name: " . $usuario['us_nombre'] . " at " . date('Y-m-d H:i:s'));
    
    // ===== RESPUESTA EXITOSA =====
    
    $response = array(
        'success' => true,
        'message' => 'Contraseña cambiada exitosamente',
        'logout' => false // Cambiar a true si quieres cerrar sesión automáticamente
    );
    
    echo json_encode($response);
    
} catch (Exception $e) {
    // Revertir transacción en caso de error
    if ($pdo && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    // Log del error para depuración
    error_log("ERROR cambio_clave.php: " . $e->getMessage() . " - User ID: " . ($_SESSION['user_id'] ?? 'unknown'));
    
    // Respuesta de error
    $response = array(
        'success' => false,
        'message' => $e->getMessage()
    );
    
    echo json_encode($response);
    
} catch (PDOException $e) {
    // Error específico de base de datos
    if ($pdo && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    error_log("PDO ERROR cambio_clave.php: " . $e->getMessage() . " - User ID: " . ($_SESSION['user_id'] ?? 'unknown'));
    
    $response = array(
        'success' => false,
        'message' => 'Error de base de datos. Inténtelo de nuevo más tarde.'
    );
    
    echo json_encode($response);
}

// ===== LIMPIAR VARIABLES SENSIBLES =====

// Limpiar variables que contienen contraseñas de la memoria
if (isset($passwordActual)) {
    $passwordActual = str_repeat('0', strlen($passwordActual));
    unset($passwordActual);
}

if (isset($passwordNueva)) {
    $passwordNueva = str_repeat('0', strlen($passwordNueva));
    unset($passwordNueva);
}

if (isset($passwordConfirmar)) {
    $passwordConfirmar = str_repeat('0', strlen($passwordConfirmar));
    unset($passwordConfirmar);
}

if (isset($passwordNuevaHash)) {
    $passwordNuevaHash = str_repeat('0', strlen($passwordNuevaHash));
    unset($passwordNuevaHash);
}
?>