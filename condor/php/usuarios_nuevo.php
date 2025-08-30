<?php

// Establecer el tipo de contenido de la respuesta como JSON
header('Content-Type: application/json');
// Permitir acceso desde cualquier origen (CORS)
header('Access-Control-Allow-Origin: *');
// Permitir métodos POST para las peticiones
header('Access-Control-Allow-Methods: POST');
// Permitir el encabezado Content-Type en las peticiones
header('Access-Control-Allow-Headers: Content-Type');

// Incluir archivo de configuración de base de datos
require_once 'config_bd.php';

try {
    // Conexión a la base de datos usando la clase Database
    $database = Database::getInstance();
    $pdo = $database->getConnection();
    
    // Verificar que la petición sea de tipo POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        // Obtener y validar los datos del formulario
        $username = trim($_POST['us_username'] ?? '');
        $nombre = trim($_POST['us_nombre'] ?? '');
        $email = trim($_POST['us_email'] ?? '');
        $password = $_POST['us_password'] ?? '';
        $fecha_nacimiento = $_POST['us_fecha_nacimiento'] ?? '';
        $rol = $_POST['us_rol'] ?? '';
        $activo = $_POST['us_activo'] ?? '1';
        $puesto = $_POST['us_puesto'] ?? '';
        // Validaciones básicass
        $errors = [];
        
        if (empty($username)) {
            $errors[] = 'El nombre de usuario es requerido';
        } elseif (strlen($username) < 3) {
            $errors[] = 'El nombre de usuario debe tener al menos 3 caracteres';
        }
        
        if (empty($nombre)) {
            $errors[] = 'El nombre completo es requerido';
        }
        
        if (empty($email)) {
            $errors[] = 'El correo electrónico es requerido';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'El formato del correo electrónico no es válido';
        }
        
        if (empty($password)) {
            $errors[] = 'La contraseña es requerida';
        } elseif (strlen($password) < 6) {
            $errors[] = 'La contraseña debe tener al menos 6 caracteres';
        }
        
        if (empty($fecha_nacimiento)) {
            $errors[] = 'La fecha de nacimiento es requerida';
        }
        
        if (empty($rol)) {
            $errors[] = 'El rol es requerido';
        } elseif (!in_array($rol, ['administrador', 'usuario'])) {
            $errors[] = 'El rol seleccionado no es válido';
        }
        
        // Si hay errores de validación, devolverlos
        if (!empty($errors)) {
            echo json_encode([
                'success' => false,
                'message' => implode(', ', $errors)
            ]);
            exit;
        }
        
        // Verificar si el username ya existe
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE us_username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            echo json_encode([
                'success' => false,
                'message' => 'El nombre de usuario ya existe'
            ]);
            exit;
        }
        
        // Verificar si el email ya existe
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE us_email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            echo json_encode([
                'success' => false,
                'message' => 'El correo electrónico ya está registrado'
            ]);
            exit;
        }
        
        // Hash de la contraseña
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        // Preparar la consulta SQL para insertar el nuevo usuario
        $sql = "INSERT INTO usuarios (us_username, us_nombre, us_email, us_password, 
                                    us_fecha_nacimiento, us_rol, us_activo, us_fecha_registro, us_puesto) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?)";
        
        $stmt = $pdo->prepare($sql);
        
        // Ejecutar la consulta con los datos del usuario
        $result = $stmt->execute([
            $username,
            $nombre,
            $email, 
            $password_hash,
            $fecha_nacimiento,
            $rol,
            $activo,
            $puesto
        ]);
        
        if ($result) {
            // Obtener el ID del usuario recién creado
            $user_id = $pdo->lastInsertId();
            
            echo json_encode([
                'success' => true,
                'message' => 'Usuario creado exitosamente',
                'user_id' => $user_id
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al crear el usuario en la base de datos'
            ]);
        }
        
    } else {
        // Si el método no es POST, devolver error
        echo json_encode([
            'success' => false,
            'message' => 'Método no permitido'
        ]);
    }
    
} catch (PDOException $e) {
    // Capturar errores específicos de la base de datos
    echo json_encode([
        'success' => false,
        'message' => 'Error de base de datos: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    // Capturar cualquier otro error del servidor
    echo json_encode([
        'success' => false,
        'message' => 'Error del servidor: ' . $e->getMessage()
    ]);
}

?>
