<?php
require_once '../php/config_bd.php';

try {
    $database = Database::getInstance();
    $pdo = $database->getConnection();
    
    echo "<h3>Estructura de la tabla 'proyectos':</h3>";
    
    $stmt = $pdo->query("DESCRIBE proyectos");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse; margin: 10px;'>";
    echo "<tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    foreach ($columns as $column) {
        echo "<tr>";
        foreach ($column as $value) {
            echo "<td style='padding: 5px; border: 1px solid #ccc;'>" . htmlspecialchars($value) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<br><h3>Datos de ejemplo (primeras 3 filas):</h3>";
    $stmt = $pdo->query("SELECT * FROM proyectos LIMIT 3");
    $sample = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($sample)) {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px;'>";
        echo "<tr>";
        foreach (array_keys($sample[0]) as $header) {
            echo "<th style='padding: 5px; border: 1px solid #ccc;'>" . htmlspecialchars($header) . "</th>";
        }
        echo "</tr>";
        
        foreach ($sample as $row) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td style='padding: 5px; border: 1px solid #ccc;'>" . htmlspecialchars($value) . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No hay datos en la tabla proyectos</p>";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>