<?php
/**
 * Script de prueba para crear una rutina directamente
 */

echo "<h1>Prueba de API de Rutinas</h1>";

require_once 'conexion.php';

// Verificar conexión
if (!$conexion) {
    echo "<p style='color:red'>❌ Error de conexión a la base de datos</p>";
    exit;
}

echo "<p style='color:green'>✅ Conexión a la base de datos exitosa</p>";

// Verificar si la tabla rutinas existe
$result = $conexion->query("SHOW TABLES LIKE 'rutinas'");
if ($result->num_rows == 0) {
    echo "<p style='color:red'>❌ La tabla 'rutinas' NO existe</p>";
    echo "<p>Ejecuta primero: <a href='instalar_bd.php'>instalar_bd.php</a></p>";
} else {
    echo "<p style='color:green'>✅ La tabla 'rutinas' existe</p>";
    
    // Ver estructura de la tabla
    echo "<h3>Estructura de la tabla:</h3>";
    $estructura = $conexion->query("DESCRIBE rutinas");
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
    } else {
        echo "<p style='color:red'>❌ Usuario demo NO existe</p>";
        // Crear usuario demo
        $conexion->query("INSERT INTO usuarios (nombre, email, rol) VALUES ('Usuario Demo', 'demo@deportiva.com', 'cliente')");
        echo "<p style='color:green'>✅ Usuario demo creado</p>";
    }
    
    // Probar insertar rutina
    echo "<h3>Probando insertar rutina:</h3>";
    $sql = "INSERT INTO rutinas (usuario_id, nombre, deporte, descripcion, objetivo, nivel, duracion_semanas, frecuencia_semanal, fecha_inicio, fecha_fin, estado) 
            VALUES (1, 'Rutina Prueba', 'futbol', 'Rutina de prueba', 'resistencia', 'principiante', 4, 3, NOW(), DATE_ADD(NOW(), INTERVAL 4 WEEK), 'activa')";
    
    if ($conexion->query($sql)) {
        echo "<p style='color:green'>✅ Rutina creada exitosamente. ID: " . $conexion->insert_id . "</p>";
    } else {
        echo "<p style='color:red'>❌ Error al crear rutina: " . $conexion->error . "</p>";
    }
    
    // Ver rutinas
    echo "<h3>Rutinas en la base de datos:</h3>";
    $result = $conexion->query("SELECT * FROM rutinas");
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Usuario</th><th>Nombre</th><th>Deporte</th><th>Estado</th><th>Fecha Creación</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['usuario_id'] . "</td>";
        echo "<td>" . $row['nombre'] . "</td>";
        echo "<td>" . $row['deporte'] . "</td>";
        echo "<td>" . $row['estado'] . "</td>";
        echo "<td>" . $row['fecha_creacion'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

$conexion->close();
?>
