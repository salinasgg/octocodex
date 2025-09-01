<?php
session_start();

echo "<h2>🔍 Debug de Sesión para Dashboard</h2>";

echo "<h3>Variables de Sesión:</h3>";
if (empty($_SESSION)) {
    echo "<p>❌ No hay variables de sesión</p>";
} else {
    echo "<ul>";
    foreach ($_SESSION as $key => $value) {
        echo "<li><strong>$key:</strong> " . (is_array($value) ? json_encode($value) : htmlspecialchars($value)) . "</li>";
    }
    echo "</ul>";
}

echo "<h3>Verificaciones específicas:</h3>";
echo "<ul>";
echo "<li><strong>logged_in:</strong> " . (isset($_SESSION['logged_in']) ? ($_SESSION['logged_in'] ? 'true' : 'false') : 'NO SET') . "</li>";
echo "<li><strong>us_nombre:</strong> " . htmlspecialchars($_SESSION['us_nombre'] ?? 'NO SET') . "</li>";
echo "<li><strong>us_apellido:</strong> " . htmlspecialchars($_SESSION['us_apellido'] ?? 'NO SET') . "</li>";
echo "<li><strong>us_foto_perfil:</strong> " . htmlspecialchars($_SESSION['us_foto_perfil'] ?? 'NO SET') . "</li>";
echo "</ul>";

if (isset($_SESSION['us_foto_perfil']) && $_SESSION['us_foto_perfil']) {
    $foto_path = "../" . $_SESSION['us_foto_perfil'];
    echo "<h3>🖼️ Verificación de Imagen:</h3>";
    echo "<p><strong>Ruta esperada:</strong> $foto_path</p>";
    echo "<p><strong>Existe archivo:</strong> " . (file_exists($foto_path) ? '✅ SÍ' : '❌ NO') . "</p>";
    
    if (file_exists($foto_path)) {
        echo "<p><strong>Vista previa:</strong></p>";
        echo "<img src='$foto_path' width='100' height='100' style='border: 2px solid #ccc; border-radius: 50%; object-fit: cover;'>";
    } else {
        echo "<p>🔍 Buscando archivos en directorio uploads/perfiles/:</p>";
        $upload_dir = "../uploads/perfiles/";
        if (is_dir($upload_dir)) {
            $files = scandir($upload_dir);
            echo "<ul>";
            foreach ($files as $file) {
                if ($file != '.' && $file != '..') {
                    echo "<li>$file</li>";
                }
            }
            echo "</ul>";
        } else {
            echo "<p>❌ El directorio $upload_dir no existe</p>";
        }
    }
}

echo "<h3>🧪 Test de Nombre Completo:</h3>";
echo "<p><strong>Método 1 (us_nombre + us_apellido):</strong> ¡Bienvenido, " . htmlspecialchars(($_SESSION['us_nombre'] ?? 'Usuario') . ' ' . ($_SESSION['us_apellido'] ?? '')) . "!</p>";
echo "<p><strong>Método 2 (nombre_completo):</strong> ¡Bienvenido, " . htmlspecialchars($_SESSION['nombre_completo'] ?? 'Usuario') . "!</p>";
?>