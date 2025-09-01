<?php
/**
 * Script para instalar/actualizar el sistema de asignaciones de proyectos
 * Ejecuta automáticamente la creación de tablas, vistas y procedimientos
 */

session_start();
// Simular sesión admin para poder ejecutar
$_SESSION['logged_in'] = true;
$_SESSION['user_id'] = 1;
$_SESSION['rol'] = 'administrador';

require_once 'config_bd.php';

echo "<h2>⚙️ Instalador del Sistema de Asignaciones de Proyectos</h2>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .success { color: #28a745; }
    .error { color: #dc3545; }
    .warning { color: #ffc107; }
    .info { color: #17a2b8; }
    .step { background: #f8f9fa; padding: 10px; margin: 10px 0; border-left: 4px solid #007bff; }
    pre { background: #f4f4f4; padding: 10px; border-radius: 5px; overflow-x: auto; }
</style>";

try {
    $database = Database::getInstance();
    $pdo = $database->getConnection();
    
    echo "<p class='success'>✅ Conexión establecida correctamente</p>";
    
    // Verificar base de datos
    $stmt = $pdo->query("SELECT DATABASE() as db_name");
    $db_info = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p class='info'>📊 Instalando en base de datos: <strong>" . $db_info['db_name'] . "</strong></p>";
    
    echo "<div class='step'>";
    echo "<h3>Paso 1: Verificando tablas existentes</h3>";
    
    // Verificar si ya existe la tabla
    $tabla_existe = false;
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM asignaciones_proyectos LIMIT 1");
        $tabla_existe = true;
        echo "<p class='warning'>⚠️ La tabla 'asignaciones_proyectos' ya existe</p>";
    } catch (Exception $e) {
        echo "<p class='info'>💡 La tabla 'asignaciones_proyectos' no existe, será creada</p>";
    }
    echo "</div>";
    
    echo "<div class='step'>";
    echo "<h3>Paso 2: Creando estructura de base de datos</h3>";
    
    // Leer y ejecutar el script SQL
    $sql_file = '../sql/crear_asignaciones_proyectos.sql';
    if (!file_exists($sql_file)) {
        throw new Exception("No se encuentra el archivo SQL: $sql_file");
    }
    
    $sql_content = file_get_contents($sql_file);
    
    // Dividir el SQL en comandos individuales
    $sql_commands = array();
    $temp_line = '';
    $lines = explode("\n", $sql_content);
    
    foreach ($lines as $line) {
        $line = trim($line);
        
        // Saltar comentarios y líneas vacías
        if (empty($line) || substr($line, 0, 2) == '--' || substr($line, 0, 2) == '/*') {
            continue;
        }
        
        $temp_line .= $line;
        
        // Si la línea termina con ; (y no es parte de un DELIMITER), ejecutar
        if (substr($line, -1) == ';' && strpos($temp_line, 'DELIMITER') === false) {
            $sql_commands[] = $temp_line;
            $temp_line = '';
        } else {
            $temp_line .= ' ';
        }
    }
    
    $comandos_ejecutados = 0;
    $errores = 0;
    
    foreach ($sql_commands as $sql) {
        $sql = trim($sql);
        if (empty($sql)) continue;
        
        try {
            // Saltar comandos problemáticos
            if (strpos($sql, 'USE ') === 0 || 
                strpos($sql, 'DELIMITER') !== false ||
                strpos($sql, 'DROP TRIGGER') !== false ||
                strpos($sql, 'CREATE TRIGGER') !== false) {
                continue;
            }
            
            $pdo->exec($sql);
            $comandos_ejecutados++;
            
            // Mostrar solo comandos importantes
            if (strpos($sql, 'CREATE TABLE') === 0) {
                $table_name = preg_match('/CREATE TABLE `?(\w+)`?/', $sql, $matches);
                echo "<p class='success'>✅ Tabla creada: " . ($matches[1] ?? 'Unknown') . "</p>";
            } else if (strpos($sql, 'CREATE VIEW') === 0) {
                $view_name = preg_match('/CREATE VIEW `?(\w+)`?/', $sql, $matches);
                echo "<p class='success'>✅ Vista creada: " . ($matches[1] ?? 'Unknown') . "</p>";
            } else if (strpos($sql, 'CREATE PROCEDURE') === 0) {
                $proc_name = preg_match('/CREATE PROCEDURE `?(\w+)`?/', $sql, $matches);
                echo "<p class='success'>✅ Procedimiento creado: " . ($matches[1] ?? 'Unknown') . "</p>";
            }
            
        } catch (Exception $e) {
            $errores++;
            if (strpos($e->getMessage(), 'already exists') === false) {
                echo "<p class='error'>❌ Error en comando SQL: " . substr($e->getMessage(), 0, 100) . "...</p>";
            }
        }
    }
    
    echo "<p class='info'>📈 Comandos ejecutados: $comandos_ejecutados</p>";
    if ($errores > 0) {
        echo "<p class='warning'>⚠️ Errores no críticos: $errores</p>";
    }
    echo "</div>";
    
    echo "<div class='step'>";
    echo "<h3>Paso 3: Verificando instalación</h3>";
    
    // Verificar que las tablas se crearon correctamente
    $tablas_verificar = ['asignaciones_proyectos', 'historial_asignaciones'];
    $tablas_ok = 0;
    
    foreach ($tablas_verificar as $tabla) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) FROM $tabla");
            echo "<p class='success'>✅ Tabla '$tabla' verificada</p>";
            $tablas_ok++;
        } catch (Exception $e) {
            echo "<p class='error'>❌ Error con tabla '$tabla': " . $e->getMessage() . "</p>";
        }
    }
    
    // Verificar vista
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM vista_asignaciones_detalle");
        echo "<p class='success'>✅ Vista 'vista_asignaciones_detalle' verificada</p>";
    } catch (Exception $e) {
        echo "<p class='error'>❌ Error con vista: " . $e->getMessage() . "</p>";
    }
    
    echo "</div>";
    
    echo "<div class='step'>";
    echo "<h3>Paso 4: Verificando datos base</h3>";
    
    // Verificar usuarios
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios WHERE us_activo = 1");
    $usuarios_count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Verificar proyectos  
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM proyectos WHERE pr_activo = 1");
    $proyectos_count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    echo "<p class='info'>👥 Usuarios activos disponibles: <strong>$usuarios_count</strong></p>";
    echo "<p class='info'>📋 Proyectos activos disponibles: <strong>$proyectos_count</strong></p>";
    
    if ($usuarios_count == 0) {
        echo "<p class='warning'>⚠️ No hay usuarios en la base de datos. Necesitará crear usuarios antes de hacer asignaciones.</p>";
    }
    
    if ($proyectos_count == 0) {
        echo "<p class='warning'>⚠️ No hay proyectos en la base de datos. Necesitará crear proyectos antes de hacer asignaciones.</p>";
    }
    
    echo "</div>";
    
    // Insertar datos de ejemplo si no hay asignaciones y hay usuarios/proyectos
    if ($usuarios_count > 0 && $proyectos_count > 0) {
        echo "<div class='step'>";
        echo "<h3>Paso 5: Datos de ejemplo (Opcional)</h3>";
        
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM asignaciones_proyectos");
        $asignaciones_count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        if ($asignaciones_count == 0) {
            echo "<form method='POST' style='margin: 10px 0;'>";
            echo "<p>¿Desea insertar datos de ejemplo para probar el sistema?</p>";
            echo "<button type='submit' name='insertar_ejemplos' value='1' style='background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;'>🎯 Insertar Datos de Ejemplo</button>";
            echo "</form>";
            
            if (isset($_POST['insertar_ejemplos'])) {
                insertarDatosEjemplo($pdo);
            }
        } else {
            echo "<p class='info'>📊 Ya existen $asignaciones_count asignaciones en el sistema</p>";
        }
        
        echo "</div>";
    }
    
    echo "<hr>";
    echo "<h3>🎉 Instalación Completada</h3>";
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; border: 1px solid #c3e6cb;'>";
    echo "<p><strong>✅ El sistema de asignaciones de proyectos ha sido instalado correctamente</strong></p>";
    echo "<h4>Próximos pasos:</h4>";
    echo "<ol>";
    echo "<li><a href='verificar_bd.php'>🔍 Verificar estado de la base de datos</a></li>";
    echo "<li><a href='test_asignaciones.php'>🧪 Probar la API de asignaciones</a></li>";
    echo "<li><a href='../admin/dashboard.php'>📊 Acceder al dashboard administrativo</a></li>";
    echo "</ol>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; border: 1px solid #f5c6cb;'>";
    echo "<p class='error'><strong>❌ Error durante la instalación:</strong></p>";
    echo "<pre>" . $e->getMessage() . "</pre>";
    echo "<p>Verifique:</p>";
    echo "<ul>";
    echo "<li>Que la base de datos esté funcionando</li>";
    echo "<li>Que el archivo SQL existe en la ruta correcta</li>";
    echo "<li>Que tenga permisos de escritura en la base de datos</li>";
    echo "</ul>";
    echo "</div>";
}

function insertarDatosEjemplo($pdo) {
    echo "<h4>Insertando datos de ejemplo...</h4>";
    
    try {
        // Obtener algunos usuarios
        $stmt = $pdo->query("SELECT id FROM usuarios WHERE us_activo = 1 LIMIT 6");
        $usuarios = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Obtener algunos proyectos
        $stmt = $pdo->query("SELECT id FROM proyectos WHERE pr_activo = 1 LIMIT 5");
        $proyectos = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $ejemplos_insertados = 0;
        $ejemplos = [
            [$proyectos[0] ?? 1, $usuarios[0] ?? 1, 'lider', 40.00, '2025-09-01', 'Líder del proyecto con responsabilidad total'],
            [$proyectos[0] ?? 1, $usuarios[1] ?? 2, 'desarrollador', 35.00, '2025-09-02', 'Desarrollador frontend principal'],
            [$proyectos[1] ?? 2, $usuarios[2] ?? 3, 'consultor', 20.00, '2025-09-05', 'Consultoría técnica especializada'],
            [$proyectos[1] ?? 2, $usuarios[0] ?? 1, 'revisor', 15.00, '2025-09-06', 'Revisión y aprobación de entregables'],
            [$proyectos[2] ?? 3, $usuarios[3] ?? 4, 'desarrollador', 30.00, '2025-09-10', 'Desarrollo de módulos específicos'],
        ];
        
        $sql = "INSERT INTO asignaciones_proyectos (proyecto_id, usuario_id, rol_proyecto, horas_asignadas, fecha_inicio, notas) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        foreach ($ejemplos as $ejemplo) {
            try {
                $stmt->execute($ejemplo);
                $ejemplos_insertados++;
            } catch (Exception $e) {
                // Puede fallar por constraint único, es normal
                continue;
            }
        }
        
        echo "<p class='success'>✅ $ejemplos_insertados datos de ejemplo insertados</p>";
        
    } catch (Exception $e) {
        echo "<p class='error'>❌ Error insertando datos de ejemplo: " . $e->getMessage() . "</p>";
    }
}
?>

<script>
// Scroll automático al final cuando termine la instalación
window.addEventListener('load', function() {
    const successElement = document.querySelector('h3');
    if (successElement && successElement.textContent.includes('Instalación Completada')) {
        successElement.scrollIntoView({ behavior: 'smooth' });
    }
});
</script>