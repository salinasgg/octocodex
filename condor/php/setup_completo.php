<?php
/**
 * SCRIPT MAESTRO DE INSTALACIÓN COMPLETA
 * Configura todo el sistema de asignaciones de proyectos desde cero
 */

session_start();
$_SESSION['logged_in'] = true;
$_SESSION['user_id'] = 1;
$_SESSION['rol'] = 'administrador';

require_once 'config_bd.php';

echo "<!DOCTYPE html>";
echo "<html lang='es'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>Setup Completo - Sistema de Asignaciones</title>";
echo "<style>
    body { 
        font-family: 'Arial', sans-serif; 
        margin: 0; 
        padding: 20px; 
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #333;
        min-height: 100vh;
    }
    .container {
        max-width: 1000px;
        margin: 0 auto;
        background: white;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    }
    .header {
        text-align: center;
        margin-bottom: 30px;
        padding: 20px;
        background: linear-gradient(135deg, #8b5cf6, #667eea);
        border-radius: 15px;
        color: white;
    }
    .step {
        background: #f8f9fa;
        margin: 20px 0;
        padding: 20px;
        border-radius: 10px;
        border-left: 5px solid #8b5cf6;
        position: relative;
    }
    .step.completed {
        border-left-color: #28a745;
        background: #d4edda;
    }
    .step.error {
        border-left-color: #dc3545;
        background: #f8d7da;
    }
    .step.warning {
        border-left-color: #ffc107;
        background: #fff3cd;
    }
    .success { color: #28a745; font-weight: bold; }
    .error { color: #dc3545; font-weight: bold; }
    .warning { color: #ffc107; font-weight: bold; }
    .info { color: #17a2b8; font-weight: bold; }
    .progress-bar {
        width: 100%;
        height: 10px;
        background: #e9ecef;
        border-radius: 5px;
        overflow: hidden;
        margin: 20px 0;
    }
    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #28a745, #20c997);
        transition: width 0.5s ease;
    }
    .final-actions {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
        padding: 25px;
        border-radius: 15px;
        margin-top: 30px;
        text-align: center;
    }
    .action-buttons {
        margin-top: 20px;
    }
    .action-buttons a {
        display: inline-block;
        background: rgba(255,255,255,0.2);
        color: white;
        padding: 12px 25px;
        text-decoration: none;
        border-radius: 8px;
        margin: 5px;
        transition: all 0.3s ease;
        border: 2px solid rgba(255,255,255,0.3);
    }
    .action-buttons a:hover {
        background: rgba(255,255,255,0.3);
        transform: translateY(-2px);
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin: 15px 0;
    }
    th, td {
        border: 1px solid #dee2e6;
        padding: 12px;
        text-align: left;
    }
    th {
        background: #f8f9fa;
        font-weight: 600;
    }
    .spinner {
        border: 4px solid #f3f3f3;
        border-top: 4px solid #8b5cf6;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        animation: spin 1s linear infinite;
        display: inline-block;
        margin-right: 10px;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .step-number {
        position: absolute;
        left: -15px;
        top: 15px;
        background: #8b5cf6;
        color: white;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }
    .step.completed .step-number {
        background: #28a745;
    }
    .step.error .step-number {
        background: #dc3545;
    }
</style>";
echo "</head>";
echo "<body>";

echo "<div class='container'>";
echo "<div class='header'>";
echo "<h1>🚀 Setup Completo del Sistema de Asignaciones</h1>";
echo "<p>Configuración automática de tablas, datos y funcionalidades</p>";
echo "</div>";

$total_pasos = 6;
$paso_actual = 0;
$errores_criticos = 0;

try {
    $database = Database::getInstance();
    $pdo = $database->getConnection();
    
    echo "<div class='progress-bar'>";
    echo "<div class='progress-fill' id='progress' style='width: 0%'></div>";
    echo "</div>";
    
    // PASO 1: Verificar conexión
    $paso_actual++;
    echo "<div class='step completed'>";
    echo "<div class='step-number'>1</div>";
    echo "<h3>🔌 Verificando Conexión a Base de Datos</h3>";
    echo "<p class='success'>✅ Conexión establecida correctamente</p>";
    
    $stmt = $pdo->query("SELECT DATABASE() as db_name, VERSION() as version");
    $db_info = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p class='info'>📊 Base de datos: {$db_info['db_name']}</p>";
    echo "<p class='info'>🔧 MySQL/MariaDB: {$db_info['version']}</p>";
    echo "</div>";
    updateProgress($paso_actual, $total_pasos);
    
    // PASO 2: Verificar/Crear usuarios
    $paso_actual++;
    echo "<div class='step'>";
    echo "<div class='step-number'>2</div>";
    echo "<h3>👥 Gestionando Usuarios del Sistema</h3>";
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios WHERE us_activo = 1");
    $usuarios_count = $stmt->fetch()['total'];
    
    if ($usuarios_count < 3) {
        echo "<p class='warning'>⚠️ Pocos usuarios disponibles ($usuarios_count). Creando usuarios de ejemplo...</p>";
        
        $usuarios_ejemplo = [
            ['Juan Carlos', 'Pérez López', 'admin', 'admin@octocodex.com', 'administrador'],
            ['María Elena', 'García Martínez', 'maria.garcia', 'maria.garcia@octocodex.com', 'usuario'],
            ['Carlos Alberto', 'Rodríguez Silva', 'carlos.rodriguez', 'carlos.rodriguez@octocodex.com', 'usuario'],
            ['Ana Sofía', 'López Fernández', 'ana.lopez', 'ana.lopez@octocodex.com', 'usuario'],
            ['Roberto Miguel', 'Hernández Torres', 'roberto.hernandez', 'roberto.hernandez@octocodex.com', 'usuario']
        ];
        
        $sql = "INSERT INTO usuarios (us_nombre, us_apellido, us_username, us_password, us_email, us_rol, us_activo) 
                VALUES (?, ?, ?, ?, ?, ?, 1)";
        $stmt = $pdo->prepare($sql);
        
        $usuarios_creados = 0;
        foreach ($usuarios_ejemplo as $usuario) {
            try {
                $password_hash = password_hash('123456', PASSWORD_DEFAULT);
                $stmt->execute(array_merge($usuario, [$password_hash]));
                $usuarios_creados++;
            } catch (Exception $e) {
                // Usuario ya existe, continuar
            }
        }
        
        echo "<p class='success'>✅ Usuarios creados/verificados: $usuarios_creados</p>";
    } else {
        echo "<p class='success'>✅ Usuarios suficientes disponibles: $usuarios_count</p>";
    }
    
    echo "</div>";
    updateProgress($paso_actual, $total_pasos);
    
    // PASO 3: Verificar/Crear proyectos
    $paso_actual++;
    echo "<div class='step'>";
    echo "<div class='step-number'>3</div>";
    echo "<h3>📋 Gestionando Proyectos del Sistema</h3>";
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM proyectos WHERE pr_activo = 1");
    $proyectos_count = $stmt->fetch()['total'];
    
    if ($proyectos_count < 3) {
        echo "<p class='warning'>⚠️ Pocos proyectos disponibles ($proyectos_count). Creando proyectos de ejemplo...</p>";
        
        $proyectos_ejemplo = [
            ['Sistema de Gestión Empresarial', 'Desarrollo de un sistema integral para la gestión de recursos empresariales', 'en_desarrollo', 'alta', '2025-08-01', '2025-12-31', 75000.00, 35],
            ['Aplicación Móvil E-commerce', 'Aplicación móvil multiplataforma para comercio electrónico', 'en_revision', 'alta', '2025-07-15', '2025-11-30', 45000.00, 68],
            ['Portal Web Corporativo', 'Rediseño completo del sitio web corporativo con CMS personalizado', 'propuesta', 'media', '2025-09-01', '2025-10-31', 25000.00, 0],
            ['Sistema de Control de Inventario', 'Sistema especializado para el control y seguimiento de inventario', 'en_desarrollo', 'media', '2025-08-15', '2025-12-01', 35000.00, 22],
            ['API de Integración de Servicios', 'Desarrollo de APIs REST para integración con servicios externos', 'finalizado', 'critica', '2025-06-01', '2025-08-31', 55000.00, 100]
        ];
        
        $sql = "INSERT INTO proyectos (pr_titulo, pr_descripcion, pr_estado, pr_prioridad, pr_fecha_inicio, pr_fecha_estimada, pr_presupuesto, pr_progreso, pr_activo) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)";
        $stmt = $pdo->prepare($sql);
        
        $proyectos_creados = 0;
        foreach ($proyectos_ejemplo as $proyecto) {
            try {
                $stmt->execute($proyecto);
                $proyectos_creados++;
            } catch (Exception $e) {
                // Proyecto puede ya existir
            }
        }
        
        echo "<p class='success'>✅ Proyectos creados/verificados: $proyectos_creados</p>";
    } else {
        echo "<p class='success'>✅ Proyectos suficientes disponibles: $proyectos_count</p>";
    }
    
    echo "</div>";
    updateProgress($paso_actual, $total_pasos);
    
    // PASO 4: Crear tabla de asignaciones
    $paso_actual++;
    echo "<div class='step'>";
    echo "<div class='step-number'>4</div>";
    echo "<h3>🔗 Creando Sistema de Asignaciones</h3>";
    
    // Verificar si existe
    $tabla_existe = false;
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM asignaciones_proyectos LIMIT 1");
        $tabla_existe = true;
        echo "<p class='info'>ℹ️ Tabla de asignaciones ya existe</p>";
    } catch (Exception $e) {
        echo "<p class='warning'>⚠️ Creando tabla de asignaciones...</p>";
    }
    
    if (!$tabla_existe) {
        // Crear tabla
        $sql = "CREATE TABLE `asignaciones_proyectos` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `proyecto_id` int(11) NOT NULL,
          `usuario_id` int(11) NOT NULL,
          `rol_proyecto` enum('lider','desarrollador','consultor','revisor','colaborador') NOT NULL DEFAULT 'colaborador',
          `fecha_asignacion` timestamp DEFAULT CURRENT_TIMESTAMP,
          `fecha_inicio` date DEFAULT NULL,
          `fecha_fin` date DEFAULT NULL,
          `estado_asignacion` enum('activo','completado','pausado','cancelado') DEFAULT 'activo',
          `notas` text DEFAULT NULL,
          `horas_asignadas` decimal(5,2) DEFAULT NULL,
          `horas_trabajadas` decimal(5,2) DEFAULT 0.00,
          `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
          `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          UNIQUE KEY `unique_assignment` (`proyecto_id`, `usuario_id`),
          KEY `idx_proyecto` (`proyecto_id`),
          KEY `idx_usuario` (`usuario_id`),
          KEY `idx_estado` (`estado_asignacion`),
          CONSTRAINT `fk_asignacion_proyecto` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos` (`id`) ON DELETE CASCADE,
          CONSTRAINT `fk_asignacion_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $pdo->exec($sql);
        echo "<p class='success'>✅ Tabla de asignaciones creada</p>";
    }
    
    // Crear vista
    try {
        $pdo->exec("DROP VIEW IF EXISTS vista_asignaciones_detalle");
        $sql_vista = "CREATE VIEW `vista_asignaciones_detalle` AS
        SELECT 
            ap.id as asignacion_id, ap.proyecto_id, ap.usuario_id, ap.rol_proyecto, ap.estado_asignacion,
            ap.fecha_asignacion, ap.fecha_inicio, ap.fecha_fin, ap.horas_asignadas, ap.horas_trabajadas, ap.notas,
            p.pr_titulo as proyecto_titulo, p.pr_estado as proyecto_estado, p.pr_prioridad as proyecto_prioridad,
            u.us_nombre as usuario_nombre, u.us_apellido as usuario_apellido, u.us_email as usuario_email,
            u.us_rol as usuario_rol, CONCAT(u.us_nombre, ' ', u.us_apellido) as nombre_completo
        FROM asignaciones_proyectos ap
        INNER JOIN proyectos p ON ap.proyecto_id = p.id
        INNER JOIN usuarios u ON ap.usuario_id = u.id";
        
        $pdo->exec($sql_vista);
        echo "<p class='success'>✅ Vista de asignaciones creada</p>";
    } catch (Exception $e) {
        echo "<p class='error'>❌ Error creando vista: " . $e->getMessage() . "</p>";
    }
    
    echo "</div>";
    updateProgress($paso_actual, $total_pasos);
    
    // PASO 5: Crear asignaciones de ejemplo
    $paso_actual++;
    echo "<div class='step'>";
    echo "<div class='step-number'>5</div>";
    echo "<h3>🎯 Creando Asignaciones de Ejemplo</h3>";
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM asignaciones_proyectos");
    $asignaciones_count = $stmt->fetch()['total'];
    
    if ($asignaciones_count == 0) {
        echo "<p class='info'>💡 Creando asignaciones de ejemplo...</p>";
        
        // Obtener IDs reales
        $stmt = $pdo->query("SELECT id FROM usuarios WHERE us_activo = 1 LIMIT 6");
        $usuarios = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $stmt = $pdo->query("SELECT id FROM proyectos WHERE pr_activo = 1 LIMIT 5");
        $proyectos = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (count($usuarios) >= 3 && count($proyectos) >= 3) {
            $ejemplos = [
                [$proyectos[0], $usuarios[0], 'lider', 40.00, '2025-09-01', 'Líder del proyecto'],
                [$proyectos[0], $usuarios[1], 'desarrollador', 35.00, '2025-09-02', 'Desarrollador frontend'],
                [$proyectos[1], $usuarios[2], 'consultor', 20.00, '2025-09-05', 'Consultoría técnica'],
                [$proyectos[1], $usuarios[0], 'revisor', 15.00, '2025-09-06', 'Revisión de entregables'],
                [$proyectos[2], $usuarios[3], 'desarrollador', 30.00, '2025-09-10', 'Desarrollo de módulos'],
            ];
            
            $sql = "INSERT INTO asignaciones_proyectos (proyecto_id, usuario_id, rol_proyecto, horas_asignadas, fecha_inicio, notas) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            
            $creadas = 0;
            foreach ($ejemplos as $ejemplo) {
                try {
                    $stmt->execute($ejemplo);
                    $creadas++;
                } catch (Exception $e) {
                    // Puede fallar por constraint único
                }
            }
            
            echo "<p class='success'>✅ Asignaciones de ejemplo creadas: $creadas</p>";
        } else {
            echo "<p class='warning'>⚠️ No hay suficientes usuarios o proyectos para crear ejemplos</p>";
        }
    } else {
        echo "<p class='success'>✅ Ya existen $asignaciones_count asignaciones en el sistema</p>";
    }
    
    echo "</div>";
    updateProgress($paso_actual, $total_pasos);
    
    // PASO 6: Verificación final
    $paso_actual++;
    echo "<div class='step completed'>";
    echo "<div class='step-number'>6</div>";
    echo "<h3>🏁 Verificación Final del Sistema</h3>";
    
    // Contar totales finales
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios WHERE us_activo = 1");
    $usuarios_final = $stmt->fetch()['total'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM proyectos WHERE pr_activo = 1");
    $proyectos_final = $stmt->fetch()['total'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM asignaciones_proyectos");
    $asignaciones_final = $stmt->fetch()['total'];
    
    echo "<table>";
    echo "<tr><th>Componente</th><th>Cantidad</th><th>Estado</th></tr>";
    echo "<tr><td>👥 Usuarios Activos</td><td>$usuarios_final</td><td>" . ($usuarios_final >= 3 ? "<span class='success'>✅ OK</span>" : "<span class='warning'>⚠️ Pocos</span>") . "</td></tr>";
    echo "<tr><td>📋 Proyectos Activos</td><td>$proyectos_final</td><td>" . ($proyectos_final >= 3 ? "<span class='success'>✅ OK</span>" : "<span class='warning'>⚠️ Pocos</span>") . "</td></tr>";
    echo "<tr><td>🔗 Asignaciones</td><td>$asignaciones_final</td><td>" . ($asignaciones_final > 0 ? "<span class='success'>✅ OK</span>" : "<span class='warning'>⚠️ Ninguna</span>") . "</td></tr>";
    echo "</table>";
    
    // Verificar API
    try {
        $test_url = 'asignaciones_proyectos.php?accion=obtener_estadisticas';
        $context = stream_context_create([
            'http' => ['timeout' => 5, 'header' => 'Cookie: ' . $_SERVER['HTTP_COOKIE'] ?? '']
        ]);
        $response = @file_get_contents($test_url, false, $context);
        
        if ($response && json_decode($response)) {
            echo "<p class='success'>✅ API de asignaciones funciona correctamente</p>";
        } else {
            echo "<p class='warning'>⚠️ La API podría tener problemas, verificar manualmente</p>";
        }
    } catch (Exception $e) {
        echo "<p class='warning'>⚠️ No se pudo verificar la API automáticamente</p>";
    }
    
    echo "</div>";
    updateProgress($paso_actual, $total_pasos);
    
    echo "<div class='final-actions'>";
    echo "<h2>🎉 ¡Instalación Completada Exitosamente!</h2>";
    echo "<p>El sistema de asignaciones de proyectos está listo para usar</p>";
    
    echo "<div class='action-buttons'>";
    echo "<a href='verificar_bd.php'>🔍 Verificar Base de Datos</a>";
    echo "<a href='test_asignaciones.php'>🧪 Probar API</a>";
    echo "<a href='../admin/dashboard.php'>📊 Dashboard Admin</a>";
    echo "<a href='../dashboard.php'>🏠 Dashboard Principal</a>";
    echo "</div>";
    
    echo "<div style='margin-top: 20px; padding: 15px; background: rgba(255,255,255,0.2); border-radius: 10px;'>";
    echo "<h4>📋 Credenciales de Acceso:</h4>";
    echo "<p><strong>Usuario Administrador:</strong> admin / 123456</p>";
    echo "<p><strong>Usuarios de ejemplo:</strong> maria.garcia, carlos.rodriguez, ana.lopez / 123456</p>";
    echo "</div>";
    echo "</div>";
    
} catch (Exception $e) {
    $errores_criticos++;
    echo "<div class='step error'>";
    echo "<div class='step-number'>❌</div>";
    echo "<h3>Error Crítico</h3>";
    echo "<p class='error'>❌ " . $e->getMessage() . "</p>";
    echo "<p>Por favor, verifique:</p>";
    echo "<ul>";
    echo "<li>Conexión a la base de datos</li>";
    echo "<li>Permisos de escritura</li>";
    echo "<li>Estructura de tablas existentes</li>";
    echo "</ul>";
    echo "</div>";
}

echo "</div>"; // container

echo "<script>
function updateProgress(current, total) {
    const percentage = Math.round((current / total) * 100);
    document.getElementById('progress').style.width = percentage + '%';
}

// Auto-refresh si hay errores críticos después de 10 segundos
if ($errores_criticos > 0) {
    setTimeout(() => {
        if (confirm('¿Desea reintentar la instalación?')) {
            location.reload();
        }
    }, 10000);
}
</script>";

echo "</body></html>";

function updateProgress($current, $total) {
    $percentage = round(($current / $total) * 100);
    echo "<script>updateProgress($current, $total);</script>";
    flush();
    usleep(500000); // Pausa de 0.5 segundos para efecto visual
}
?>