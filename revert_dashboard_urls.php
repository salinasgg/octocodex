<?php
// Script para revertir las URLs incorrectas en admin/dashboard.php
$archivo = 'admin/dashboard.php';

if (!file_exists($archivo)) {
    die("❌ Error: El archivo $archivo no existe\n");
}

// Leer el contenido actual
$contenido = file_get_contents($archivo);

// Contar ocurrencias antes del cambio
$ocurrenciasAntes = substr_count($contenido, "../../condor/php/asignaciones_proyectos.php");

if ($ocurrenciasAntes === 0) {
    echo "✅ No se encontraron URLs para revertir en $archivo\n";
    exit;
}

echo "🔧 Revertiendo URLs en $archivo...\n";
echo "📊 Ocurrencias encontradas: $ocurrenciasAntes\n";

// Revertir el cambio: cambiar de '../../condor/php/asignaciones_proyectos.php' a '../php/asignaciones_proyectos.php'
$contenidoNuevo = str_replace(
    "../../condor/php/asignaciones_proyectos.php",
    "../php/asignaciones_proyectos.php",
    $contenido
);

// Contar ocurrencias después del cambio
$ocurrenciasDespues = substr_count($contenidoNuevo, "../php/asignaciones_proyectos.php");

// Guardar el archivo
if (file_put_contents($archivo, $contenidoNuevo)) {
    echo "✅ Revertido exitosamente en $archivo:\n";
    echo "   - URLs corregidas: $ocurrenciasDespues\n";
    echo "   - De: '../../condor/php/asignaciones_proyectos.php'\n";
    echo "   - A: '../php/asignaciones_proyectos.php'\n";
} else {
    echo "❌ Error al guardar el archivo $archivo\n";
}
?>
