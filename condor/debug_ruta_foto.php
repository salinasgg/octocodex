<?php
session_start();

echo "<h2>🔍 Diagnóstico de Ruta de Foto</h2>";
echo "<hr>";

// Verificar si el usuario está logueado
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    echo "❌ Usuario no logueado";
    exit;
}

echo "<h3>📋 Información de la Foto:</h3>";
echo "<ul>";
echo "<li><strong>us_foto_perfil:</strong> " . ($_SESSION['us_foto_perfil'] ?? 'No definida') . "</li>";
echo "</ul>";

echo "<hr>";

// Verificar diferentes rutas posibles
echo "<h3>🔍 Verificación de Rutas:</h3>";

$foto_nombre = $_SESSION['us_foto_perfil'] ?? '';
if ($foto_nombre) {
    $rutas_posibles = [
        "uploads/perfiles/$foto_nombre",
        "../uploads/perfiles/$foto_nombre",
        "../../uploads/perfiles/$foto_nombre",
        "./uploads/perfiles/$foto_nombre",
        "condor/uploads/perfiles/$foto_nombre"
    ];
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Ruta</th><th>Existe</th><th>Ruta Completa</th><th>Acción</th></tr>";
    
    foreach ($rutas_posibles as $ruta) {
        $existe = file_exists($ruta);
        $ruta_completa = realpath($ruta);
        $status = $existe ? "✅ Sí" : "❌ No";
        
        echo "<tr>";
        echo "<td>$ruta</td>";
        echo "<td>$status</td>";
        echo "<td>" . ($ruta_completa ?: 'No encontrada') . "</td>";
        echo "<td>";
        if ($existe) {
            echo "<a href='$ruta' target='_blank'>Ver imagen</a>";
        } else {
            echo "-";
        }
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>❌ No hay nombre de foto en la sesión</p>";
}

echo "<hr>";

// Verificar directorios
echo "<h3>📁 Verificación de Directorios:</h3>";
$directorios = [
    "uploads/perfiles/",
    "../uploads/perfiles/",
    "../../uploads/perfiles/",
    "./uploads/perfiles/"
];

echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Directorio</th><th>Existe</th><th>Contenido</th></tr>";

foreach ($directorios as $dir) {
    $existe = is_dir($dir);
    $status = $existe ? "✅ Sí" : "❌ No";
    
    echo "<tr>";
    echo "<td>$dir</td>";
    echo "<td>$status</td>";
    echo "<td>";
    if ($existe) {
        $files = scandir($dir);
        $archivos = array_filter($files, function($file) {
            return $file != '.' && $file != '..';
        });
        if (!empty($archivos)) {
            echo "<ul>";
            foreach ($archivos as $file) {
                echo "<li>$file</li>";
            }
            echo "</ul>";
        } else {
            echo "Vacío";
        }
    } else {
        echo "-";
    }
    echo "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<hr>";

// Verificar directorio actual
echo "<h3>📍 Información del Sistema:</h3>";
echo "<ul>";
echo "<li><strong>Directorio actual:</strong> " . getcwd() . "</li>";
echo "<li><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] ?? 'No disponible' . "</li>";
echo "<li><strong>Script Path:</strong> " . __FILE__ . "</li>";
echo "<li><strong>Script Dir:</strong> " . __DIR__ . "</li>";
echo "</ul>";

echo "<hr>";

// Probar mostrar la imagen con diferentes rutas
echo "<h3>🖼️ Prueba de Visualización:</h3>";
if ($foto_nombre) {
    $rutas_para_probar = [
        "uploads/perfiles/$foto_nombre",
        "../uploads/perfiles/$foto_nombre"
    ];
    
    foreach ($rutas_para_probar as $ruta) {
        if (file_exists($ruta)) {
            echo "<div style='margin: 20px 0; padding: 10px; border: 1px solid #ccc;'>";
            echo "<h4>Ruta: $ruta</h4>";
            echo "<img src='$ruta' style='width: 100px; height: 100px; object-fit: cover; border-radius: 50%; border: 2px solid #007bff;'>";
            echo "<p>✅ Imagen cargada correctamente</p>";
            echo "</div>";
        }
    }
}

echo "<hr>";
echo "<p><a href='test_foto_perfil.php'>🔙 Volver al Test de Foto</a></p>";
echo "<p><a href='admin/dashboard.php'>📊 Ir al Dashboard</a></p>";
?>
