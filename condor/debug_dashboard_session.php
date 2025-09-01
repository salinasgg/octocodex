<?php
/**
 * Debug de Sesión para Dashboard
 */

// Verificar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

echo "<h2>🔍 Debug de Sesión - Dashboard</h2>";
echo "<hr>";

echo "<h3>📋 Estado de la Sesión:</h3>";
echo "<ul>";
echo "<li><strong>Session Status:</strong> " . session_status() . "</li>";
echo "<li><strong>Session ID:</strong> " . session_id() . "</li>";
echo "<li><strong>Session Name:</strong> " . session_name() . "</li>";
echo "<li><strong>Logged In:</strong> " . ($_SESSION['logged_in'] ?? 'NO') . "</li>";
echo "</ul>";

echo "<hr>";

echo "<h3>📋 Variables de Sesión:</h3>";
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
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
    
    echo "<h3>🖼️ Prueba de Foto de Perfil:</h3>";
    if (isset($_SESSION['us_foto_perfil']) && $_SESSION['us_foto_perfil']) {
        $foto_path = "../uploads/perfiles/" . htmlspecialchars($_SESSION['us_foto_perfil']);
        echo "<p><strong>Ruta de la foto:</strong> $foto_path</p>";
        
        if (file_exists($foto_path)) {
            echo "<img src='$foto_path' style='width: 100px; height: 100px; object-fit: cover; border-radius: 50%; border: 2px solid #007bff;'>";
            echo "<p>✅ La foto existe y se puede mostrar</p>";
        } else {
            echo "<p>❌ La foto no existe en la ruta especificada</p>";
            echo "<p><strong>Ruta completa:</strong> " . realpath($foto_path) . "</p>";
        }
    } else {
        echo "<p>ℹ️ No hay foto de perfil definida en la sesión</p>";
    }
    
} else {
    echo "<p style='color: #dc3545;'>❌ Usuario no logueado</p>";
    echo "<p><a href='../index.php'>🔐 Ir al Login</a></p>";
}

echo "<hr>";

echo "<h3>🔗 Enlaces de Prueba:</h3>";
echo "<ul>";
echo "<li><a href='dashboard_home.php'>🏠 Dashboard Home</a></li>";
echo "<li><a href='dashboard.php'>📊 Dashboard Principal</a></li>";
echo "<li><a href='../index.php'>🔐 Login</a></li>";
echo "<li><a href='../test_login_simple.php'>🔍 Test Login Simple</a></li>";
echo "</ul>";

echo "<hr>";

echo "<h3>📝 Información del Sistema:</h3>";
echo "<ul>";
echo "<li><strong>Directorio actual:</strong> " . getcwd() . "</li>";
echo "<li><strong>Script Path:</strong> " . __FILE__ . "</li>";
echo "<li><strong>Document Root:</strong> " . ($_SERVER['DOCUMENT_ROOT'] ?? 'No disponible') . "</li>";
echo "</ul>";
?>
