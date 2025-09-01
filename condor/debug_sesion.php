<?php
// Configuración de errores para desarrollo
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🔍 Diagnóstico de Sesión</h2>";
echo "<hr>";

// Verificar si la sesión está iniciada
echo "<h3>📋 Estado de la Sesión:</h3>";
echo "<ul>";
echo "<li><strong>session_status():</strong> " . session_status() . "</li>";
echo "<li><strong>session_id():</strong> " . (session_id() ?: 'No iniciada') . "</li>";
echo "<li><strong>session_name():</strong> " . session_name() . "</li>";
echo "</ul>";

// Intentar iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    echo "<p>⚠️ La sesión no está iniciada. Intentando iniciar...</p>";
    session_start();
    echo "<p>✅ Sesión iniciada manualmente</p>";
} else {
    echo "<p>✅ La sesión ya está iniciada</p>";
}

echo "<hr>";

// Mostrar todas las variables de sesión
echo "<h3>📋 Variables de Sesión:</h3>";
if (empty($_SESSION)) {
    echo "<p>❌ No hay variables de sesión definidas</p>";
} else {
    echo "<ul>";
    foreach ($_SESSION as $key => $value) {
        echo "<li><strong>$key:</strong> " . (is_array($value) ? json_encode($value) : htmlspecialchars($value)) . "</li>";
    }
    echo "</ul>";
}

echo "<hr>";

// Verificar si el usuario está logueado
echo "<h3>🔐 Estado de Login:</h3>";
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    echo "<p>✅ Usuario logueado correctamente</p>";
    echo "<ul>";
    echo "<li><strong>User ID:</strong> " . ($_SESSION['user_id'] ?? 'No definido') . "</li>";
    echo "<li><strong>Username:</strong> " . ($_SESSION['username'] ?? 'No definido') . "</li>";
    echo "<li><strong>Rol:</strong> " . ($_SESSION['rol'] ?? 'No definido') . "</li>";
    echo "<li><strong>Nombre Completo:</strong> " . ($_SESSION['nombre_completo'] ?? 'No definido') . "</li>";
    echo "<li><strong>Email:</strong> " . ($_SESSION['email'] ?? 'No definido') . "</li>";
    echo "<li><strong>Foto de Perfil:</strong> " . ($_SESSION['us_foto_perfil'] ?? 'No definida') . "</li>";
    echo "<li><strong>URL de Perfil:</strong> " . ($_SESSION['us_url_perfil'] ?? 'No definida') . "</li>";
    echo "</ul>";
} else {
    echo "<p>❌ Usuario NO está logueado</p>";
    echo "<p><strong>logged_in:</strong> " . ($_SESSION['logged_in'] ?? 'No definido') . "</p>";
}

echo "<hr>";

// Verificar configuración de cookies de sesión
echo "<h3>🍪 Configuración de Cookies:</h3>";
echo "<ul>";
echo "<li><strong>session.cookie_lifetime:</strong> " . ini_get('session.cookie_lifetime') . "</li>";
echo "<li><strong>session.cookie_path:</strong> " . ini_get('session.cookie_path') . "</li>";
echo "<li><strong>session.cookie_domain:</strong> " . ini_get('session.cookie_domain') . "</li>";
echo "<li><strong>session.cookie_secure:</strong> " . ini_get('session.cookie_secure') . "</li>";
echo "<li><strong>session.cookie_httponly:</strong> " . ini_get('session.cookie_httponly') . "</li>";
echo "</ul>";

echo "<hr>";

// Verificar cookies del navegador
echo "<h3>🍪 Cookies del Navegador:</h3>";
if (empty($_COOKIE)) {
    echo "<p>❌ No hay cookies disponibles</p>";
} else {
    echo "<ul>";
    foreach ($_COOKIE as $key => $value) {
        echo "<li><strong>$key:</strong> " . htmlspecialchars($value) . "</li>";
    }
    echo "</ul>";
}

echo "<hr>";

// Enlaces de acción
echo "<h3>🔗 Acciones:</h3>";
echo "<ul>";
echo "<li><a href='index.php'>🔑 Ir al Login</a></li>";
echo "<li><a href='admin/dashboard.php'>📊 Ir al Dashboard Admin</a></li>";
echo "<li><a href='usuario/dashboard.php'>👤 Ir al Dashboard Usuario</a></li>";
echo "<li><a href='logout.php'>🚪 Cerrar Sesión</a></li>";
echo "</ul>";

echo "<hr>";
echo "<p><strong>🕐 Timestamp:</strong> " . date('Y-m-d H:i:s') . "</p>";
?>
