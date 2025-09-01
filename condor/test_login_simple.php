<?php
// Configuración de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Iniciar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

echo "<h2>🔑 Test de Login Simple</h2>";
echo "<hr>";

// Verificar si ya estás logueado
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    echo "<h3>✅ Ya estás logueado!</h3>";
    echo "<ul>";
    echo "<li><strong>User ID:</strong> " . ($_SESSION['user_id'] ?? 'No definido') . "</li>";
    echo "<li><strong>Username:</strong> " . ($_SESSION['username'] ?? 'No definido') . "</li>";
    echo "<li><strong>Rol:</strong> " . ($_SESSION['rol'] ?? 'No definido') . "</li>";
    echo "<li><strong>Nombre Completo:</strong> " . ($_SESSION['nombre_completo'] ?? 'No definido') . "</li>";
    echo "<li><strong>Email:</strong> " . ($_SESSION['email'] ?? 'No definido') . "</li>";
    echo "<li><strong>Foto de Perfil:</strong> " . ($_SESSION['us_foto_perfil'] ?? 'No definida') . "</li>";
    echo "<li><strong>URL de Perfil:</strong> " . ($_SESSION['us_url_perfil'] ?? 'No definida') . "</li>";
    echo "</ul>";
    
    echo "<hr>";
    echo "<p><a href='test_foto_perfil.php'>🖼️ Ir a Test de Foto de Perfil</a></p>";
    echo "<p><a href='admin/dashboard.php'>📊 Ir al Dashboard</a></p>";
    echo "<p><a href='logout.php'>🚪 Cerrar Sesión</a></p>";
    
} else {
    echo "<h3>❌ No estás logueado</h3>";
    echo "<p>Para probar la foto de perfil, necesitas hacer login primero.</p>";
    
    echo "<hr>";
    echo "<h3>🔑 Credenciales de Prueba:</h3>";
    echo "<ul>";
    echo "<li><strong>Usuario:</strong> admin</li>";
    echo "<li><strong>Contraseña:</strong> password</li>";
    echo "</ul>";
    
    echo "<hr>";
    echo "<p><a href='index.php'>🔑 Ir al Login</a></p>";
    echo "<p><a href='debug_sesion.php'>🔍 Diagnóstico de Sesión</a></p>";
}

echo "<hr>";
echo "<p><strong>🕐 Timestamp:</strong> " . date('Y-m-d H:i:s') . "</p>";
?>
