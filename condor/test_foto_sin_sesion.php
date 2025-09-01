<?php
echo "<h2>🖼️ Test de Foto Sin Sesión</h2>";
echo "<hr>";

// Simular la foto de perfil directamente
$foto_perfil = "perfil_4_1756240474.jpg";
$ruta_foto = "uploads/perfiles/$foto_perfil";

echo "<h3>📋 Información:</h3>";
echo "<ul>";
echo "<li><strong>Archivo:</strong> $foto_perfil</li>";
echo "<li><strong>Ruta:</strong> $ruta_foto</li>";
echo "<li><strong>Existe:</strong> " . (file_exists($ruta_foto) ? "✅ Sí" : "❌ No") . "</li>";
echo "</ul>";

echo "<hr>";

echo "<h3>🖼️ Visualización de la Foto:</h3>";
if (file_exists($ruta_foto)) {
    echo "<div style='text-align: center;'>";
    echo "<img src='$ruta_foto' style='width: 150px; height: 150px; object-fit: cover; border-radius: 50%; border: 3px solid #007bff; box-shadow: 0 4px 8px rgba(0,0,0,0.1);'>";
    echo "<p style='margin-top: 10px; color: #28a745;'>✅ Foto cargada correctamente</p>";
    echo "</div>";
} else {
    echo "<p style='color: #dc3545;'>❌ No se pudo cargar la foto</p>";
}

echo "<hr>";

echo "<h3>🔗 Enlaces de Prueba:</h3>";
echo "<ul>";
echo "<li><a href='test_login_simple.php'>🔐 Verificar Login</a></li>";
echo "<li><a href='admin/dashboard.php'>📊 Ir al Dashboard</a></li>";
echo "<li><a href='admin/dashboard_home.php'>🏠 Dashboard Home</a></li>";
echo "</ul>";

echo "<hr>";

echo "<h3>📝 Código para Dashboard:</h3>";
echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px; font-family: monospace;'>";
echo "&lt;?php if (file_exists('uploads/perfiles/$foto_perfil')): ?&gt;<br>";
echo "&nbsp;&nbsp;&lt;img src='uploads/perfiles/$foto_perfil' class='rounded-circle' width='60' height='60' alt='Foto de perfil' style='object-fit: cover;'&gt;<br>";
echo "&lt;?php else: ?&gt;<br>";
echo "&nbsp;&nbsp;&lt;div class='rounded-circle d-flex align-items-center justify-content-center bg-white' style='width: 60px; height: 60px;'&gt;<br>";
echo "&nbsp;&nbsp;&nbsp;&nbsp;&lt;i class='fas fa-user fa-2x text-secondary'&gt;&lt;/i&gt;<br>";
echo "&nbsp;&nbsp;&lt;/div&gt;<br>";
echo "&lt;?php endif; ?&gt;";
echo "</div>";
?>
