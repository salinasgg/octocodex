<?php
/**
 * Script para verificar el estado de la base de datos
 * y las tablas necesarias para el sistema de asignaciones
 */

session_start();
// Simular sesión para poder usar config_bd
$_SESSION['logged_in'] = true;
$_SESSION['user_id'] = 1;

require_once 'config_bd.php';

echo "<h2>🔍 Verificación del Estado de la Base de Datos</h2>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .success { color: #28a745; }
    .error { color: #dc3545; }
    .warning { color: #ffc107; }
    .info { color: #17a2b8; }
    table { border-collapse: collapse; width: 100%; margin: 10px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
</style>";

try {
    $database = Database::getInstance();
    $pdo = $database->getConnection();
    
    echo "<p class='success'>✅ Conexión a la base de datos establecida correctamente</p>";
    
    // Verificar base de datos actual
    $stmt = $pdo->query("SELECT DATABASE() as db_name");
    $db_info = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p class='info'>📊 Base de datos actual: <strong>" . $db_info['db_name'] . "</strong></p>";
    
    // 1. Verificar tabla usuarios
    echo "<h3>👥 Verificación de Tabla Usuarios</h3>";
    try {
        $stmt = $pdo->query("DESCRIBE usuarios");
        echo "<p class='success'>✅ Tabla 'usuarios' existe</p>";
        
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios WHERE us_activo = 1");
        $count = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<p class='info'>📈 Usuarios activos: <strong>" . $count['total'] . "</strong></p>";
        
        if ($count['total'] > 0) {
            $stmt = $pdo->query("SELECT id, us_nombre, us_apellido, us_email, us_rol FROM usuarios WHERE us_activo = 1 LIMIT 5");
            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "<table>";
            echo "<tr><th>ID</th><th>Nombre</th><th>Email</th><th>Rol</th></tr>";
            foreach ($usuarios as $usuario) {
                echo "<tr>";
                echo "<td>" . $usuario['id'] . "</td>";
                echo "<td>" . $usuario['us_nombre'] . " " . $usuario['us_apellido'] . "</td>";
                echo "<td>" . $usuario['us_email'] . "</td>";
                echo "<td>" . $usuario['us_rol'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>❌ Error con tabla usuarios: " . $e->getMessage() . "</p>";
    }
    
    // 2. Verificar tabla proyectos
    echo "<h3>📋 Verificación de Tabla Proyectos</h3>";
    try {
        $stmt = $pdo->query("DESCRIBE proyectos");
        echo "<p class='success'>✅ Tabla 'proyectos' existe</p>";
        
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM proyectos WHERE pr_activo = 1");
        $count = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<p class='info'>📈 Proyectos activos: <strong>" . $count['total'] . "</strong></p>";
        
        if ($count['total'] > 0) {
            $stmt = $pdo->query("SELECT id, pr_titulo, pr_estado, pr_prioridad FROM proyectos WHERE pr_activo = 1 LIMIT 5");
            $proyectos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "<table>";
            echo "<tr><th>ID</th><th>Título</th><th>Estado</th><th>Prioridad</th></tr>";
            foreach ($proyectos as $proyecto) {
                echo "<tr>";
                echo "<td>" . $proyecto['id'] . "</td>";
                echo "<td>" . $proyecto['pr_titulo'] . "</td>";
                echo "<td>" . $proyecto['pr_estado'] . "</td>";
                echo "<td>" . $proyecto['pr_prioridad'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>❌ Error con tabla proyectos: " . $e->getMessage() . "</p>";
    }
    
    // 3. Verificar tabla asignaciones_proyectos
    echo "<h3>🔗 Verificación de Tabla Asignaciones Proyectos</h3>";
    try {
        $stmt = $pdo->query("DESCRIBE asignaciones_proyectos");
        echo "<p class='success'>✅ Tabla 'asignaciones_proyectos' existe</p>";
        
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM asignaciones_proyectos");
        $count = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<p class='info'>📈 Total asignaciones: <strong>" . $count['total'] . "</strong></p>";
        
        if ($count['total'] > 0) {
            $stmt = $pdo->query("
                SELECT ap.id, p.pr_titulo, u.us_nombre, u.us_apellido, ap.rol_proyecto, ap.estado_asignacion 
                FROM asignaciones_proyectos ap
                INNER JOIN proyectos p ON ap.proyecto_id = p.id
                INNER JOIN usuarios u ON ap.usuario_id = u.id
                LIMIT 5
            ");
            $asignaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "<table>";
            echo "<tr><th>ID</th><th>Proyecto</th><th>Usuario</th><th>Rol</th><th>Estado</th></tr>";
            foreach ($asignaciones as $asig) {
                echo "<tr>";
                echo "<td>" . $asig['id'] . "</td>";
                echo "<td>" . $asig['pr_titulo'] . "</td>";
                echo "<td>" . $asig['us_nombre'] . " " . $asig['us_apellido'] . "</td>";
                echo "<td>" . $asig['rol_proyecto'] . "</td>";
                echo "<td>" . $asig['estado_asignacion'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p class='warning'>⚠️ No hay asignaciones creadas aún</p>";
        }
        
    } catch (Exception $e) {
        echo "<p class='error'>❌ Tabla 'asignaciones_proyectos' NO existe: " . $e->getMessage() . "</p>";
        echo "<p class='info'>💡 Necesita ejecutar el script de creación de asignaciones</p>";
    }
    
    // 4. Verificar vista asignaciones_detalle
    echo "<h3>👁️ Verificación de Vista Asignaciones Detalle</h3>";
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM vista_asignaciones_detalle LIMIT 1");
        echo "<p class='success'>✅ Vista 'vista_asignaciones_detalle' existe</p>";
    } catch (Exception $e) {
        echo "<p class='error'>❌ Vista 'vista_asignaciones_detalle' NO existe: " . $e->getMessage() . "</p>";
    }
    
    // 5. Verificar procedimientos almacenados
    echo "<h3>⚙️ Verificación de Procedimientos Almacenados</h3>";
    try {
        $stmt = $pdo->query("SHOW PROCEDURE STATUS WHERE Name = 'obtener_estadisticas_asignaciones'");
        $proc = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($proc) {
            echo "<p class='success'>✅ Procedimiento 'obtener_estadisticas_asignaciones' existe</p>";
        } else {
            echo "<p class='error'>❌ Procedimiento 'obtener_estadisticas_asignaciones' NO existe</p>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>❌ Error verificando procedimientos: " . $e->getMessage() . "</p>";
    }
    
    echo "<hr>";
    echo "<h3>🎯 Resumen de Estado</h3>";
    
    // Generar recomendaciones
    $usuarios_count = 0;
    $proyectos_count = 0;
    $asignaciones_exist = false;
    
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios WHERE us_activo = 1");
        $usuarios_count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM proyectos WHERE pr_activo = 1");
        $proyectos_count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM asignaciones_proyectos");
        $asignaciones_exist = true;
    } catch (Exception $e) {
        $asignaciones_exist = false;
    }
    
    echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px;'>";
    
    if (!$asignaciones_exist) {
        echo "<p class='error'><strong>❌ ACCIÓN REQUERIDA:</strong></p>";
        echo "<ol>";
        echo "<li>Ejecutar el script SQL: <code>/condor/sql/crear_asignaciones_proyectos.sql</code></li>";
        echo "<li>Verificar que se crearon las tablas y vistas</li>";
        echo "<li>Insertar datos de ejemplo si es necesario</li>";
        echo "</ol>";
    } else if ($usuarios_count == 0 || $proyectos_count == 0) {
        echo "<p class='warning'><strong>⚠️ DATOS FALTANTES:</strong></p>";
        echo "<ul>";
        if ($usuarios_count == 0) echo "<li>No hay usuarios en la base de datos</li>";
        if ($proyectos_count == 0) echo "<li>No hay proyectos en la base de datos</li>";
        echo "</ul>";
    } else {
        echo "<p class='success'><strong>✅ SISTEMA LISTO:</strong></p>";
        echo "<ul>";
        echo "<li>Usuarios disponibles: $usuarios_count</li>";
        echo "<li>Proyectos disponibles: $proyectos_count</li>";
        echo "<li>Sistema de asignaciones: Configurado</li>";
        echo "</ul>";
    }
    
    echo "</div>";
    
    echo "<hr>";
    echo "<h3>🔧 Enlaces Útiles</h3>";
    echo "<ul>";
    echo "<li><a href='test_asignaciones.php'>🧪 Probar API de Asignaciones</a></li>";
    echo "<li><a href='../admin/dashboard.php'>📊 Dashboard Administrativo</a></li>";
    echo "<li><a href='../dashboard.php'>🏠 Dashboard Principal</a></li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Error de conexión: " . $e->getMessage() . "</p>";
}
?>

<script>
// Auto refresh cada 30 segundos si hay errores críticos
if (document.querySelector('.error')) {
    console.log('Errores detectados - considerando auto-refresh');
}
</script>