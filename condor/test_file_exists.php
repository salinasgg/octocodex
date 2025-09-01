<?php
echo "<h2>🔍 Test de file_exists()</h2>";
echo "<hr>";

$archivo = "perfil_4_1756240474.jpg";
$rutas = [
    "uploads/perfiles/$archivo",
    "../uploads/perfiles/$archivo",
    "./uploads/perfiles/$archivo",
    __DIR__ . "/uploads/perfiles/$archivo",
    realpath("uploads/perfiles/$archivo")
];

echo "<h3>📋 Resultados de file_exists():</h3>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Ruta</th><th>file_exists()</th><th>is_file()</th><th>is_readable()</th><th>realpath()</th></tr>";

foreach ($rutas as $ruta) {
    $existe = file_exists($ruta);
    $es_archivo = is_file($ruta);
    $es_legible = is_readable($ruta);
    $ruta_real = realpath($ruta);
    
    echo "<tr>";
    echo "<td>$ruta</td>";
    echo "<td>" . ($existe ? "✅ Sí" : "❌ No") . "</td>";
    echo "<td>" . ($es_archivo ? "✅ Sí" : "❌ No") . "</td>";
    echo "<td>" . ($es_legible ? "✅ Sí" : "❌ No") . "</td>";
    echo "<td>" . ($ruta_real ?: "No encontrada") . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<hr>";

echo "<h3>📍 Información del Sistema:</h3>";
echo "<ul>";
echo "<li><strong>__DIR__:</strong> " . __DIR__ . "</li>";
echo "<li><strong>getcwd():</strong> " . getcwd() . "</li>";
echo "<li><strong>Document Root:</strong> " . ($_SERVER['DOCUMENT_ROOT'] ?? 'No disponible') . "</li>";
echo "</ul>";

echo "<hr>";

echo "<h3>🖼️ Prueba de Visualización:</h3>";
$ruta_correcta = "uploads/perfiles/$archivo";
if (file_exists($ruta_correcta)) {
    echo "<p>✅ Archivo encontrado en: $ruta_correcta</p>";
    echo "<img src='$ruta_correcta' style='width: 100px; height: 100px; object-fit: cover; border-radius: 50%; border: 2px solid #007bff;'>";
} else {
    echo "<p>❌ Archivo NO encontrado en: $ruta_correcta</p>";
}

echo "<hr>";
echo "<p><a href='debug_ruta_foto.php'>🔍 Diagnóstico Completo</a></p>";
?>
