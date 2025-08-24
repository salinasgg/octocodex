<?php
// ===== TEST DE CONEXIÓN PARA HOSTINGER =====
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== TEST DE CONEXIÓN HOSTINGER ===\n\n";

// 1. Verificar configuración de PHP
echo "1. Configuración de PHP:\n";
echo "- PHP Version: " . phpversion() . "\n";
echo "- PDO disponible: " . (extension_loaded('pdo') ? 'Sí' : 'No') . "\n";
echo "- PDO MySQL disponible: " . (extension_loaded('pdo_mysql') ? 'Sí' : 'No') . "\n";
echo "- Session disponible: " . (extension_loaded('session') ? 'Sí' : 'No') . "\n\n";

// 2. Verificar archivo de configuración
echo "2. Verificando archivo config_bd.php:\n";
if (file_exists('config_bd.php')) {
    echo "✓ Archivo config_bd.php existe\n";
    
    // Incluir configuración
    require_once 'config_bd.php';
    
    echo "- DB_HOST: " . (defined('DB_HOST') ? DB_HOST : 'No definido') . "\n";
    echo "- DB_NAME: " . (defined('DB_NAME') ? DB_NAME : 'No definido') . "\n";
    echo "- DB_USER: " . (defined('DB_USER') ? DB_USER : 'No definido') . "\n";
    echo "- DB_PASS: " . (defined('DB_PASS') ? (strlen(DB_PASS) > 0 ? 'Definido' : 'Vacío') : 'No definido') . "\n";
    echo "- DB_PORT: " . (defined('DB_PORT') ? DB_PORT : 'No definido') . "\n\n";
    
} else {
    echo "✗ Archivo config_bd.php NO existe\n\n";
    exit;
}

// 3. Probar conexión a la base de datos
echo "3. Probando conexión a la base de datos:\n";
try {
    $database = Database::getInstance();
    $pdo = $database->getConnection();
    
    echo "✓ Conexión exitosa a la base de datos\n";
    
    // 4. Verificar tabla usuarios
    echo "\n4. Verificando tabla usuarios:\n";
    $stmt = $pdo->query("SHOW TABLES LIKE 'usuarios'");
    if ($stmt->rowCount() > 0) {
        echo "✓ Tabla usuarios existe\n";
        
        // 5. Verificar estructura de la tabla
        echo "\n5. Estructura de la tabla usuarios:\n";
        $stmt = $pdo->query("DESCRIBE usuarios");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($columns as $column) {
            echo "- " . $column['Field'] . " (" . $column['Type'] . ")\n";
        }
        
        // 6. Verificar usuarios existentes
        echo "\n6. Usuarios existentes:\n";
        $stmt = $pdo->query("SELECT id, us_username, us_rol, us_activo FROM usuarios LIMIT 5");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($users) > 0) {
            foreach ($users as $user) {
                echo "- ID: " . $user['id'] . ", Usuario: " . $user['us_username'] . 
                     ", Rol: " . $user['us_rol'] . ", Activo: " . $user['us_activo'] . "\n";
            }
        } else {
            echo "No hay usuarios en la tabla\n";
        }
        
    } else {
        echo "✗ Tabla usuarios NO existe\n";
    }
    
} catch (Exception $e) {
    echo "✗ Error de conexión: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

// 7. Verificar configuración de sesiones
echo "\n7. Configuración de sesiones:\n";
echo "- session.save_handler: " . ini_get('session.save_handler') . "\n";
echo "- session.save_path: " . ini_get('session.save_path') . "\n";
echo "- session.gc_maxlifetime: " . ini_get('session.gc_maxlifetime') . "\n";

// 8. Verificar permisos de archivos
echo "\n8. Permisos de archivos:\n";
$files = ['config_bd.php', 'loginprocess.php', 'logout.php'];
foreach ($files as $file) {
    if (file_exists($file)) {
        echo "- " . $file . ": " . substr(sprintf('%o', fileperms($file)), -4) . "\n";
    } else {
        echo "- " . $file . ": No existe\n";
    }
}

echo "\n=== FIN DEL TEST ===\n";
?>
