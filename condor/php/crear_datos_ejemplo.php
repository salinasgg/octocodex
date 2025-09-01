<?php
/**
 * Script para crear usuarios y proyectos de ejemplo
 * para poder probar el sistema de asignaciones
 */

session_start();
$_SESSION['logged_in'] = true;
$_SESSION['user_id'] = 1;
$_SESSION['rol'] = 'administrador';

require_once 'config_bd.php';

echo "<h2>📝 Creador de Datos de Ejemplo</h2>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .success { color: #28a745; }
    .error { color: #dc3545; }
    .warning { color: #ffc107; }
    .info { color: #17a2b8; }
    .section { background: #f8f9fa; padding: 15px; margin: 15px 0; border-radius: 8px; }
    table { border-collapse: collapse; width: 100%; margin: 10px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #e9ecef; }
    button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin: 5px; }
    button:hover { background: #0056b3; }
    .success-btn { background: #28a745; }
    .success-btn:hover { background: #1e7e34; }
</style>";

try {
    $database = Database::getInstance();
    $pdo = $database->getConnection();
    
    echo "<p class='success'>✅ Conexión establecida correctamente</p>";
    
    // Verificar estado actual
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios WHERE us_activo = 1");
    $usuarios_count = $stmt->fetch()['total'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM proyectos WHERE pr_activo = 1");
    $proyectos_count = $stmt->fetch()['total'];
    
    echo "<div class='section'>";
    echo "<h3>📊 Estado Actual</h3>";
    echo "<p class='info'>👥 Usuarios activos: <strong>$usuarios_count</strong></p>";
    echo "<p class='info'>📋 Proyectos activos: <strong>$proyectos_count</strong></p>";
    echo "</div>";
    
    // Crear usuarios si no existen
    if (isset($_POST['crear_usuarios']) || $usuarios_count < 3) {
        echo "<div class='section'>";
        echo "<h3>👥 Creando Usuarios de Ejemplo</h3>";
        
        $usuarios_ejemplo = [
            [
                'nombre' => 'Juan Carlos',
                'apellido' => 'Pérez López',
                'username' => 'juan.perez',
                'email' => 'juan.perez@octocodex.com',
                'rol' => 'administrador'
            ],
            [
                'nombre' => 'María Elena',
                'apellido' => 'García Martínez',
                'username' => 'maria.garcia',
                'email' => 'maria.garcia@octocodex.com',
                'rol' => 'usuario'
            ],
            [
                'nombre' => 'Carlos Alberto',
                'apellido' => 'Rodríguez Silva',
                'username' => 'carlos.rodriguez',
                'email' => 'carlos.rodriguez@octocodex.com',
                'rol' => 'usuario'
            ],
            [
                'nombre' => 'Ana Sofía',
                'apellido' => 'López Fernández',
                'username' => 'ana.lopez',
                'email' => 'ana.lopez@octocodex.com',
                'rol' => 'usuario'
            ],
            [
                'nombre' => 'Roberto Miguel',
                'apellido' => 'Hernández Torres',
                'username' => 'roberto.hernandez',
                'email' => 'roberto.hernandez@octocodex.com',
                'rol' => 'usuario'
            ],
            [
                'nombre' => 'Laura Patricia',
                'apellido' => 'Morales Castro',
                'username' => 'laura.morales',
                'email' => 'laura.morales@octocodex.com',
                'rol' => 'usuario'
            ]
        ];
        
        $sql = "INSERT INTO usuarios (us_nombre, us_apellido, us_username, us_password, us_email, us_rol, us_activo) 
                VALUES (?, ?, ?, ?, ?, ?, 1)";
        $stmt = $pdo->prepare($sql);
        
        $usuarios_creados = 0;
        foreach ($usuarios_ejemplo as $usuario) {
            try {
                $password_hash = password_hash('123456', PASSWORD_DEFAULT);
                $stmt->execute([
                    $usuario['nombre'],
                    $usuario['apellido'], 
                    $usuario['username'],
                    $password_hash,
                    $usuario['email'],
                    $usuario['rol']
                ]);
                $usuarios_creados++;
                echo "<p class='success'>✅ Usuario creado: {$usuario['nombre']} {$usuario['apellido']} ({$usuario['username']})</p>";
            } catch (Exception $e) {
                if (strpos($e->getMessage(), 'Duplicate') !== false) {
                    echo "<p class='warning'>⚠️ Usuario ya existe: {$usuario['username']}</p>";
                } else {
                    echo "<p class='error'>❌ Error creando usuario {$usuario['username']}: " . $e->getMessage() . "</p>";
                }
            }
        }
        
        echo "<p class='info'>📈 Usuarios nuevos creados: <strong>$usuarios_creados</strong></p>";
        echo "</div>";
    }
    
    // Crear proyectos si no existen
    if (isset($_POST['crear_proyectos']) || $proyectos_count < 3) {
        echo "<div class='section'>";
        echo "<h3>📋 Creando Proyectos de Ejemplo</h3>";
        
        $proyectos_ejemplo = [
            [
                'titulo' => 'Sistema de Gestión Empresarial',
                'descripcion' => 'Desarrollo de un sistema integral para la gestión de recursos empresariales, incluyendo inventario, ventas y contabilidad.',
                'estado' => 'en_desarrollo',
                'prioridad' => 'alta',
                'fecha_inicio' => '2025-08-01',
                'fecha_estimada' => '2025-12-31',
                'presupuesto' => 75000.00,
                'progreso' => 35
            ],
            [
                'titulo' => 'Aplicación Móvil E-commerce',
                'descripcion' => 'Aplicación móvil multiplataforma para comercio electrónico con funcionalidades de catálogo, carrito de compras y pagos.',
                'estado' => 'en_revision',
                'prioridad' => 'alta',
                'fecha_inicio' => '2025-07-15',
                'fecha_estimada' => '2025-11-30',
                'presupuesto' => 45000.00,
                'progreso' => 68
            ],
            [
                'titulo' => 'Portal Web Corporativo',
                'descripcion' => 'Rediseño completo del sitio web corporativo con CMS personalizado y sistema de noticias.',
                'estado' => 'propuesta',
                'prioridad' => 'media',
                'fecha_inicio' => '2025-09-01',
                'fecha_estimada' => '2025-10-31',
                'presupuesto' => 25000.00,
                'progreso' => 0
            ],
            [
                'titulo' => 'Sistema de Control de Inventario',
                'descripcion' => 'Sistema especializado para el control y seguimiento de inventario con códigos de barras y reportes avanzados.',
                'estado' => 'en_desarrollo',
                'prioridad' => 'media',
                'fecha_inicio' => '2025-08-15',
                'fecha_estimada' => '2025-12-01',
                'presupuesto' => 35000.00,
                'progreso' => 22
            ],
            [
                'titulo' => 'API de Integración de Servicios',
                'descripcion' => 'Desarrollo de APIs REST para integración con servicios externos y microservicios internos.',
                'estado' => 'finalizado',
                'prioridad' => 'critica',
                'fecha_inicio' => '2025-06-01',
                'fecha_estimada' => '2025-08-31',
                'presupuesto' => 55000.00,
                'progreso' => 100
            ],
            [
                'titulo' => 'Dashboard Analítico Avanzado',
                'descripcion' => 'Creación de dashboard interactivo con visualizaciones de datos y reportes en tiempo real.',
                'estado' => 'pausado',
                'prioridad' => 'baja',
                'fecha_inicio' => '2025-07-01',
                'fecha_estimada' => '2025-09-30',
                'presupuesto' => 20000.00,
                'progreso' => 15
            ]
        ];
        
        $sql = "INSERT INTO proyectos (pr_titulo, pr_descripcion, pr_estado, pr_prioridad, pr_fecha_inicio, pr_fecha_estimada, pr_presupuesto, pr_progreso, pr_activo) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)";
        $stmt = $pdo->prepare($sql);
        
        $proyectos_creados = 0;
        foreach ($proyectos_ejemplo as $proyecto) {
            try {
                $stmt->execute([
                    $proyecto['titulo'],
                    $proyecto['descripcion'],
                    $proyecto['estado'],
                    $proyecto['prioridad'],
                    $proyecto['fecha_inicio'],
                    $proyecto['fecha_estimada'],
                    $proyecto['presupuesto'],
                    $proyecto['progreso']
                ]);
                $proyectos_creados++;
                echo "<p class='success'>✅ Proyecto creado: {$proyecto['titulo']} ({$proyecto['estado']})</p>";
            } catch (Exception $e) {
                echo "<p class='error'>❌ Error creando proyecto: " . $e->getMessage() . "</p>";
            }
        }
        
        echo "<p class='info'>📈 Proyectos nuevos creados: <strong>$proyectos_creados</strong></p>";
        echo "</div>";
    }
    
    // Mostrar formularios si no se han creado datos
    if (!isset($_POST['crear_usuarios']) && !isset($_POST['crear_proyectos'])) {
        echo "<div class='section'>";
        echo "<h3>🎯 Acciones Disponibles</h3>";
        echo "<form method='POST'>";
        
        if ($usuarios_count < 6) {
            echo "<button type='submit' name='crear_usuarios' value='1'>👥 Crear Usuarios de Ejemplo</button>";
        } else {
            echo "<p class='success'>✅ Usuarios suficientes disponibles</p>";
        }
        
        if ($proyectos_count < 6) {
            echo "<button type='submit' name='crear_proyectos' value='1'>📋 Crear Proyectos de Ejemplo</button>";
        } else {
            echo "<p class='success'>✅ Proyectos suficientes disponibles</p>";
        }
        
        echo "<br><br>";
        echo "<button type='submit' name='crear_usuarios' value='1' class='success-btn'>🚀 Crear Todo (Usuarios + Proyectos)</button>";
        echo "</form>";
        echo "</div>";
    }
    
    // Mostrar resumen final
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios WHERE us_activo = 1");
    $usuarios_final = $stmt->fetch()['total'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM proyectos WHERE pr_activo = 1");  
    $proyectos_final = $stmt->fetch()['total'];
    
    echo "<div class='section'>";
    echo "<h3>📊 Estado Final</h3>";
    echo "<p class='info'>👥 Total usuarios activos: <strong>$usuarios_final</strong></p>";
    echo "<p class='info'>📋 Total proyectos activos: <strong>$proyectos_final</strong></p>";
    
    if ($usuarios_final >= 3 && $proyectos_final >= 3) {
        echo "<p class='success'><strong>✅ ¡Perfecto! Ya puede probar el sistema de asignaciones</strong></p>";
        echo "<h4>Próximos pasos:</h4>";
        echo "<ul>";
        echo "<li><a href='instalar_asignaciones.php'>⚙️ Instalar sistema de asignaciones</a></li>";
        echo "<li><a href='verificar_bd.php'>🔍 Verificar estado de la base de datos</a></li>";
        echo "<li><a href='../admin/dashboard.php'>📊 Ir al dashboard administrativo</a></li>";
        echo "</ul>";
    } else {
        echo "<p class='warning'>⚠️ Se necesitan al menos 3 usuarios y 3 proyectos para probar adecuadamente el sistema</p>";
    }
    echo "</div>";
    
    // Mostrar algunos datos creados
    if ($usuarios_final > 0) {
        echo "<div class='section'>";
        echo "<h4>👥 Usuarios Disponibles</h4>";
        $stmt = $pdo->query("SELECT id, us_nombre, us_apellido, us_username, us_email, us_rol FROM usuarios WHERE us_activo = 1 ORDER BY id DESC LIMIT 6");
        $usuarios = $stmt->fetchAll();
        
        echo "<table>";
        echo "<tr><th>ID</th><th>Nombre</th><th>Username</th><th>Email</th><th>Rol</th></tr>";
        foreach ($usuarios as $user) {
            echo "<tr>";
            echo "<td>{$user['id']}</td>";
            echo "<td>{$user['us_nombre']} {$user['us_apellido']}</td>";
            echo "<td>{$user['us_username']}</td>";
            echo "<td>{$user['us_email']}</td>";
            echo "<td>{$user['us_rol']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<p><small>💡 Contraseña para todos los usuarios de ejemplo: <strong>123456</strong></small></p>";
        echo "</div>";
    }
    
    if ($proyectos_final > 0) {
        echo "<div class='section'>";
        echo "<h4>📋 Proyectos Disponibles</h4>";
        $stmt = $pdo->query("SELECT id, pr_titulo, pr_estado, pr_prioridad, pr_progreso FROM proyectos WHERE pr_activo = 1 ORDER BY id DESC LIMIT 6");
        $proyectos = $stmt->fetchAll();
        
        echo "<table>";
        echo "<tr><th>ID</th><th>Título</th><th>Estado</th><th>Prioridad</th><th>Progreso</th></tr>";
        foreach ($proyectos as $proyecto) {
            echo "<tr>";
            echo "<td>{$proyecto['id']}</td>";
            echo "<td>{$proyecto['pr_titulo']}</td>";
            echo "<td>{$proyecto['pr_estado']}</td>";
            echo "<td>{$proyecto['pr_prioridad']}</td>";
            echo "<td>{$proyecto['pr_progreso']}%</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; border: 1px solid #f5c6cb;'>";
    echo "<p class='error'><strong>❌ Error:</strong></p>";
    echo "<pre>" . $e->getMessage() . "</pre>";
    echo "</div>";
}
?>

<script>
// Auto-submit form para crear todo si no hay datos
window.addEventListener('load', function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('auto') === '1') {
        const form = document.querySelector('form');
        if (form) {
            const createAllBtn = form.querySelector('button[name="crear_usuarios"][value="1"].success-btn');
            if (createAllBtn) {
                createAllBtn.click();
            }
        }
    }
});
</script>