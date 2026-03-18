<?php
/**
 * Script de prueba para crear un progreso directamente
 */

echo "<h1>Prueba de API de Progresos</h1>";

require_once 'conexion.php';

// Verificar conexión
if (!$conexion) {
    echo "<p style='color:red'>❌ Error de conexión a la base de datos</p>";
    exit;
}

echo "<p style='color:green'>✅ Conexión a la base de datos exitosa</p>";

// Verificar si la tabla progresos existe
$result = $conexion->query("SHOW TABLES LIKE 'progresos'");
if ($result->num_rows == 0) {
    echo "<p style='color:red'>❌ La tabla 'progresos' NO existe</p>";
    echo "<p>Ejecuta primero: <a href='instalar_bd.php'>instalar_bd.php</a></p>";
} else {
    echo "<p style='color:green'>✅ La tabla 'progresos' existe</p>";
    
    // Ver estructura de la tabla
    echo "<h3>Estructura de la tabla:</h3>";
    $estructura = $conexion->query("DESCRIBE progresos");
    echo "<table border='1'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Key</th><th>Default</th></tr>";
    while ($row = $estructura->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Verificar usuario demo existe
    echo "<h3>Verificando usuario demo:</h3>";
    $result = $conexion->query("SELECT * FROM usuarios WHERE id = 1");
    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
        echo "<p style='color:green'>✅ Usuario demo existe: " . $usuario['nombre'] . " (" . $usuario['email'] . ")</p>";
        echo "<pre>";
        print_r($usuario);
        echo "</pre>";
    } else {
        echo "<p style='color:red'>❌ Usuario demo NO existe</p>";
    }
    
    // Probar insertar progreso
    echo "<h3>Probando insertar progreso:</h3>";
    $sql = "INSERT INTO progresos (usuario_id, fecha, tipo_medida, valor_actual, valor_objetivo, esfuerzo, descripcion, porcentaje_completado) 
            VALUES (1, NOW(), 'peso', 70, 65, 8, 'Progreso en peso corporal', 85)";
    
    if ($conexion->query($sql)) {
        echo "<p style='color:green'>✅ Progreso creado exitosamente. ID: " . $conexion->insert_id . "</p>";
    } else {
        echo "<p style='color:red'>❌ Error al crear progreso: " . $conexion->error . "</p>";
    }
    
    // Ver progresos
    echo "<h3>Progresos en la base de datos:</h3>";
    $result = $conexion->query("SELECT * FROM progresos");
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Usuario</th><th>Fecha</th><th>Tipo</th><th>Valor</th><th>Objetivo</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['usuario_id'] . "</td>";
        echo "<td>" . $row['fecha'] . "</td>";
        echo "<td>" . $row['tipo_medida'] . "</td>";
        echo "<td>" . $row['valor_actual'] . "</td>";
        echo "<td>" . $row['valor_objetivo'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

$conexion->close();
?>
