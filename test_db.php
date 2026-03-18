<?php
/**
 * Script de prueba de conexión a la base de datos
 * Sistema Deportivo - XAMPP Local
 */

$host = "localhost";
$user = "root";
$password = "";
$database = "pagina_deportiva2";

echo "<!DOCTYPE html>";
echo "<html><head>";
echo "<meta charset='UTF-8'>";
echo "<title>Verificación de Base de Datos</title>";
echo "<style>";
echo "body { font-family: Arial, sans-serif; max-width: 900px; margin: 50px auto; padding: 20px; background: #f5f5f5; }";
echo "h1 { color: #2c3e50; }";
echo "h2 { color: #34495e; }";
echo ".success { color: #27ae60; background: #d5f4e6; padding: 10px; border-radius: 5px; margin: 10px 0; }";
echo ".error { color: #e74c3c; background: #fadbd8; padding: 10px; border-radius: 5px; margin: 10px 0; }";
echo ".warning { color: #f39c12; background: #fdebd0; padding: 10px; border-radius: 5px; margin: 10px 0; }";
echo ".info { color: #2980b9; background: #d4e6f1; padding: 10px; border-radius: 5px; margin: 10px 0; }";
echo "table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; }";
echo "th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }";
echo "th { background-color: #3498db; color: white; }";
echo "tr:nth-child(even) { background-color: #f9f9f9; }";
echo ".btn { display: inline-block; padding: 10px 20px; background: #3498db; color: white; text-decoration: none; border-radius: 5px; margin: 5px; }";
echo ".btn-success { background: #27ae60; }";
echo ".card { background: white; padding: 20px; border-radius: 10px; margin: 20px 0; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }";
echo "</style>";
echo "</head><body>";

echo "<h1>🔍 Verificación de Base de Datos</h1>";
echo "<p>Configuración: Host: $host | Usuario: $user | Base de datos: $database</p>";

// 1. Conectar al servidor MySQL
echo "<div class='card'>";
echo "<h2>1. Conexión al Servidor MySQL</h2>";
$conn = new mysqli($host, $user, $password);
if ($conn->connect_error) {
    echo "<p class='error'>❌ Error de conexión al servidor MySQL: " . $conn->connect_error . "</p>";
    echo "<p><strong>Solución:</strong> Asegúrate de que XAMPP esté ejecutándose y que el servicio MySQL esté activo.</p>";
    echo "</body></html>";
    exit;
}
echo "<p class='success'>✅ Conexión al servidor MySQL exitosa</p>";

// 2. Listar bases de datos disponibles
echo "<h2>2. Bases de datos disponibles</h2>";
$databases = [];
$result = $conn->query("SHOW DATABASES");
echo "<ul>";
while ($row = $result->fetch_array(MYSQLI_NUM)) {
    $databases[] = $row[0];
    $isTarget = ($row[0] == $database) ? " <strong>(TARGET)</strong>" : "";
    echo "<li>" . $row[0] . $isTarget . "</li>";
}
echo "</ul>";

// 3. Verificar si existe la base de datos
echo "<h2>3. Verificación de Base de Datos '$database'</h2>";
if (in_array($database, $databases)) {
    echo "<p class='success'>✅ Base de datos '$database' existe</p>";
    
    // Conectar a la base de datos específica
    $conexion = new mysqli($host, $user, $password, $database);
    if ($conexion->connect_error) {
        echo "<p class='error'>❌ Error conectando a '$database': " . $conexion->connect_error . "</p>";
    } else {
        echo "<p class='success'>✅ Conexión a '$database' exitosa</p>";
        
        // 4. Listar tablas
        echo "<h2>4. Tablas en '$database'</h2>";
        $tables_result = $conexion->query("SHOW TABLES");
        echo "<table><tr><th>Tabla</th><th>Registros</th><th>Estado</th></tr>";
        $tablasEsperadas = ['usuarios', 'contactos', 'entrenadores', 'rutinas', 'progresos', 'planes', 'ejercicios', 'solicitudes_entrenadores', 'solicitudes_planes', 'solicitudes_servicios', 'solicitudes_info'];
        while ($table_row = $tables_result->fetch_array(MYSQLI_NUM)) {
            $tabla = $table_row[0];
            $count = $conexion->query("SELECT COUNT(*) as total FROM $tabla")->fetch_assoc()['total'];
            $estado = in_array($tabla, $tablasEsperadas) ? "✅ Requerida" : "ℹ️ Adicional";
            echo "<tr><td>$tabla</td><td>$count</td><td>$estado</td></tr>";
        }
        echo "</table>";

        // 5. Verificar tablas críticas para formularios
        echo "<h2>5. Tablas críticas para formularios</h2>";
        $tablasCriticas = [
            'contactos' => 'Formulario de contacto',
            'solicitudes_entrenadores' => 'Formulario de entrenadores',
            'solicitudes_planes' => 'Formulario de planes',
            'solicitudes_servicios' => 'Formulario de servicios'
        ];
        
        foreach ($tablasCriticas as $tabla => $descripcion) {
            $tabla_result = $conexion->query("SHOW TABLES LIKE '$tabla'");
            if ($tabla_result->num_rows > 0) {
                $count = $conexion->query("SELECT COUNT(*) FROM $tabla")->fetch_row()[0];
                echo "<p class='success'>✅ '$tabla' - $descripcion ($count registros)</p>";
            } else {
                echo "<p class='error'>❌ '$tabla' NO existe - $descripcion</p>";
            }
        }

        $conexion->close();
    }
} else {
    echo "<p class='error'>❌ Base de datos '$database' NO existe</p>";
    echo "<p><strong>Solución:</strong> Ejecuta el instalador: <a href='instalar_bd.php'>instalar_bd.php</a></p>";
    echo "<a href='instalar_bd.php' class='btn btn-success'>🚀 Instalar Base de Datos</a>";
}
echo "</div>";

echo "<div class='card'>";
echo "<h2>📋 Acciones</h2>";
echo "<a href='instalar_bd.php' class='btn btn-success'>🔧 Reinstalar Base de Datos</a>";
echo "<a href='index.php' class='btn'>🏠 Ir a la página principal</a>";
echo "<a href='contacto.php' class='btn'>📧 Probar formulario de contacto</a>";
echo "<a href='planes.php' class='btn'>📋 Probar formulario de planes</a>";
echo "</div>";

$conn->close();
echo "</body></html>";
?>
