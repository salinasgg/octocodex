<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    echo "❌ Usuario no logueado";
    exit;
}

echo "<h2>🔍 Prueba de Foto de Perfil</h2>";
echo "<hr>";

// Mostrar información de la sesión
echo "<h3>📋 Información de Sesión:</h3>";
echo "<ul>";
echo "<li><strong>User ID:</strong> " . ($_SESSION['user_id'] ?? 'No definido') . "</li>";
echo "<li><strong>Username:</strong> " . ($_SESSION['username'] ?? 'No definido') . "</li>";
echo "<li><strong>Nombre Completo:</strong> " . ($_SESSION['nombre_completo'] ?? 'No definido') . "</li>";
echo "<li><strong>Rol:</strong> " . ($_SESSION['rol'] ?? 'No definido') . "</li>";
echo "<li><strong>Email:</strong> " . ($_SESSION['email'] ?? 'No definido') . "</li>";
echo "<li><strong>Foto de Perfil:</strong> " . ($_SESSION['us_foto_perfil'] ?? 'No definida') . "</li>";
echo "<li><strong>URL de Perfil:</strong> " . ($_SESSION['us_url_perfil'] ?? 'No definida') . "</li>";
echo "</ul>";

echo "<hr>";

// Mostrar la foto de perfil
echo "<h3>🖼️ Foto de Perfil:</h3>";
if (isset($_SESSION['us_foto_perfil']) && $_SESSION['us_foto_perfil']) {
    $foto_path = "uploads/perfiles/" . htmlspecialchars($_SESSION['us_foto_perfil']);
    echo "<p><strong>Ruta de la foto:</strong> $foto_path</p>";
    
    if (file_exists($foto_path)) {
        echo "<img src='$foto_path' style='width: 100px; height: 100px; object-fit: cover; border-radius: 50%; border: 2px solid #007bff;'>";
        echo "<p>✅ La foto existe y se puede mostrar</p>";
    } else {
        echo "<p>❌ La foto no existe en la ruta especificada</p>";
        echo "<p><strong>Ruta completa:</strong> " . realpath($foto_path) . "</p>";
    }
} else {
    echo "<div style='width: 100px; height: 100px; background-color: #f8f9fa; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2px solid #dee2e6;'>";
    echo "<i style='font-size: 2em; color: #6c757d;'>👤</i>";
    echo "</div>";
    echo "<p>ℹ️ No hay foto de perfil definida</p>";
}

echo "<hr>";

// Verificar directorio de uploads
echo "<h3>📁 Verificación de Directorio:</h3>";
$uploads_dir = "uploads/perfiles/";
if (is_dir($uploads_dir)) {
    echo "<p>✅ Directorio de uploads existe: $uploads_dir</p>";
    
    $files = scandir($uploads_dir);
    if (count($files) > 2) { // . y .. cuentan como 2
        echo "<p>📂 Archivos en el directorio:</p>";
        echo "<ul>";
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                echo "<li>$file</li>";
            }
        }
        echo "</ul>";
    } else {
        echo "<p>📂 El directorio está vacío</p>";
    }
} else {
    echo "<p>❌ El directorio de uploads no existe: $uploads_dir</p>";
}

echo "<hr>";
echo "<p><a href='admin/dashboard_home.php'>🔙 Volver al Dashboard</a></p>";
?>
