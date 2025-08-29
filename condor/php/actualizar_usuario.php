<?php
header('Content-Type: application/json');
// Permitir acceso desde cualquier origen (CORS)
header('Access-Control-Allow-Origin: *');
// Permitir solo el método POST para las peticiones
header('Access-Control-Allow-Methods: POST');
// Permitir el encabezado Content-Type en las peticiones
header('Access-Control-Allow-Headers: Content-Type');

// Incluir archivo de configuración de base de datos
require_once 'config_bd.php';

try {
    // Verificar que la petición sea de tipo POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['error' => 'Método no permitido. Solo se permite POST.']);
        exit;
    }
    
    // Obtener y validar los datos del formulario
    $user_id = $_POST['user_id'] ?? null;
    $us_username = $_POST['us_username'] ?? '';
    $us_email = $_POST['us_email'] ?? '';
    $us_nombre = $_POST['us_nombre'] ?? '';
    $us_fecha_nacimiento = $_POST['us_fecha_nacimiento'] ?? '';
    $us_rol = $_POST['us_rol'] ?? '';
    $us_activo = $_POST['us_activo'] ?? '0';
    
    // Validar campos requeridos
    if (!$user_id || !is_numeric($user_id)) {
        echo json_encode(['error' => 'ID de usuario inválido']);
        exit;
    }
    
    if (empty($us_username) || empty($us_email) || empty($us_nombre) || empty($us_rol)) {
        echo json_encode(['error' => 'Todos los campos son requeridos']);
        exit;
    }
    
    // Validar formato de email
    if (!filter_var($us_email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['error' => 'Formato de email inválido']);
        exit;
    }
    
    // Conexión a la base de datos usando la clase Database
    $database = Database::getInstance();
    $pdo = $database->getConnection();
    
    // Verificar que el usuario existe
    $checkSql = "SELECT id FROM usuarios WHERE id = :id";
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->execute(['id' => $user_id]);
    
    if ($checkStmt->rowCount() === 0) {
        echo json_encode(['error' => 'Usuario no encontrado']);
        exit;
    }
    
    // Verificar si el email ya existe en otro usuario
    $emailCheckSql = "SELECT id FROM usuarios WHERE us_email = :email AND id != :id";
    $emailCheckStmt = $pdo->prepare($emailCheckSql);
    $emailCheckStmt->execute(['email' => $us_email, 'id' => $user_id]);
    
    if ($emailCheckStmt->rowCount() > 0) {
        echo json_encode(['error' => 'El email ya está registrado por otro usuario']);
        exit;
    }
    
    // Verificar si el username ya existe en otro usuario
    $usernameCheckSql = "SELECT id FROM usuarios WHERE us_username = :username AND id != :id";
    $usernameCheckStmt = $pdo->prepare($usernameCheckSql);
    $usernameCheckStmt->execute(['username' => $us_username, 'id' => $user_id]);
    
    if ($usernameCheckStmt->rowCount() > 0) {
        echo json_encode(['error' => 'El nombre de usuario ya está registrado por otro usuario']);
        exit;
    }
    
    // Consulta SQL para actualizar el usuario
    $sql = "UPDATE usuarios SET 
            us_username = :username,
            us_email = :email,
            us_nombre = :nombre,
            us_fecha_nacimiento = :fecha_nacimiento,
            us_rol = :rol,
            us_activo = :activo,
            fecha_actualizacion = NOW()
            WHERE id = :id";
    
    // Preparar la consulta SQL para evitar inyección de código
    $stmt = $pdo->prepare($sql);
    
    // Ejecutar la consulta preparada
    $result = $stmt->execute([
        'username' => $us_username,
        'email' => $us_email,
        'nombre' => $us_nombre,
        'fecha_nacimiento' => $us_fecha_nacimiento,
        'rol' => $us_rol,
        'activo' => $us_activo,
        'id' => $user_id
    ]);
    
    if ($result && $stmt->rowCount() > 0) {
        echo json_encode(['success' => 'Usuario actualizado exitosamente']);
    } else {
        echo json_encode(['error' => 'No se pudo actualizar el usuario']);
    }
    
} catch (PDOException $e) {
    // Capturar errores específicos de la base de datos
    echo json_encode(['error' => 'Error de base de datos: ' . $e->getMessage()]);
} catch (Exception $e) {
    // Capturar cualquier otro error del servidor
    echo json_encode(['error' => 'Error del servidor: ' . $e->getMessage()]);
}
?>
