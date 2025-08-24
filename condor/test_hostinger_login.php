<?php
// ===== TEST DE LOGIN PARA HOSTINGER =====
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== TEST DE LOGIN HOSTINGER ===\n\n";

// 1. Verificar entorno
echo "1. Detección de entorno:\n";
echo "- HTTP_HOST: " . ($_SERVER['HTTP_HOST'] ?? 'No definido') . "\n";
echo "- SERVER_NAME: " . ($_SERVER['SERVER_NAME'] ?? 'No definido') . "\n";
echo "- REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'No definido') . "\n";

$isProduction = isset($_SERVER['HTTP_HOST']) && 
                (strpos($_SERVER['HTTP_HOST'], 'octocodex.com') !== false || 
                 strpos($_SERVER['HTTP_HOST'], 'hostinger') !== false);

echo "- Es producción: " . ($isProduction ? 'Sí' : 'No') . "\n\n";

// 2. Incluir configuración
echo "2. Cargando configuración:\n";
require_once 'config_bd.php';

echo "- DB_HOST: " . DB_HOST . "\n";
echo "- DB_NAME: " . DB_NAME . "\n";
echo "- DB_USER: " . DB_USER . "\n";
echo "- DB_PASS: " . (strlen(DB_PASS) > 0 ? 'Definido' : 'Vacío') . "\n\n";

// 3. Probar conexión
echo "3. Probando conexión a la base de datos:\n";
try {
    $database = Database::getInstance();
    $pdo = $database->getConnection();
    echo "✓ Conexión exitosa\n\n";
} catch (Exception $e) {
    echo "✗ Error de conexión: " . $e->getMessage() . "\n\n";
    exit;
}

// 4. Probar autenticación
echo "4. Probando autenticación:\n";

// Simular datos de login
$testUsername = 'salinasgg';
$testPassword = 'caca2025';

echo "- Usuario de prueba: " . $testUsername . "\n";
echo "- Contraseña de prueba: " . $testPassword . "\n\n";

try {
    // Buscar usuario
    $stmt = $pdo->prepare("
        SELECT id, us_username, us_password, us_rol, us_email, us_activo, 
               us_nombre, us_apellido
        FROM usuarios 
        WHERE us_username = :username AND us_activo = 1
    ");
    
    $stmt->execute(['username' => $testUsername]);
    $user = $stmt->fetch();
    
    if ($user) {
        echo "✓ Usuario encontrado:\n";
        echo "  - ID: " . $user['id'] . "\n";
        echo "  - Username: " . $user['us_username'] . "\n";
        echo "  - Rol: " . $user['us_rol'] . "\n";
        echo "  - Email: " . $user['us_email'] . "\n";
        echo "  - Activo: " . $user['us_activo'] . "\n";
        echo "  - Nombre: " . $user['us_nombre'] . " " . $user['us_apellido'] . "\n\n";
        
        // Verificar contraseña
        if (password_verify($testPassword, $user['us_password'])) {
            echo "✓ Contraseña correcta\n";
            
            // Simular inicio de sesión
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['us_username'];
            $_SESSION['rol'] = $user['us_rol'];
            $_SESSION['email'] = $user['us_email'];
            $_SESSION['nombre_completo'] = $user['us_nombre'] . ' ' . $user['us_apellido'];
            $_SESSION['logged_in'] = true;
            $_SESSION['login_time'] = time();
            
            echo "✓ Sesión iniciada correctamente\n";
            echo "- Session ID: " . session_id() . "\n";
            echo "- User ID en sesión: " . $_SESSION['user_id'] . "\n";
            echo "- Username en sesión: " . $_SESSION['username'] . "\n";
            echo "- Rol en sesión: " . $_SESSION['rol'] . "\n\n";
            
            // Determinar redirección
            $redirectUrl = 'dashboard.php';
            switch (strtolower($user['us_rol'])) {
                case 'administrador':
                    $redirectUrl = 'admin/dashboard.php';
                    break;
                case 'usuario':
                    $redirectUrl = 'usuario/dashboard.php';
                    break;
            }
            
            echo "✓ Redirección configurada: " . $redirectUrl . "\n\n";
            
        } else {
            echo "✗ Contraseña incorrecta\n\n";
        }
        
    } else {
        echo "✗ Usuario no encontrado o inactivo\n\n";
    }
    
} catch (Exception $e) {
    echo "✗ Error en autenticación: " . $e->getMessage() . "\n\n";
}

// 5. Verificar configuración de sesiones
echo "5. Configuración de sesiones:\n";
echo "- session.save_handler: " . ini_get('session.save_handler') . "\n";
echo "- session.save_path: " . ini_get('session.save_path') . "\n";
echo "- session.gc_maxlifetime: " . ini_get('session.gc_maxlifetime') . "\n";
echo "- session.cookie_lifetime: " . ini_get('session.cookie_lifetime') . "\n";
echo "- session.cookie_httponly: " . ini_get('session.cookie_httponly') . "\n";
echo "- session.cookie_secure: " . ini_get('session.cookie_secure') . "\n\n";

// 6. Verificar permisos de archivos
echo "6. Permisos de archivos:\n";
$files = ['config_bd.php', 'loginprocess.php', 'logout.php'];
foreach ($files as $file) {
    if (file_exists($file)) {
        $perms = fileperms($file);
        $perms = substr(sprintf('%o', $perms), -4);
        echo "- " . $file . ": " . $perms . "\n";
    } else {
        echo "- " . $file . ": No existe\n";
    }
}

echo "\n=== FIN DEL TEST ===\n";
?>
