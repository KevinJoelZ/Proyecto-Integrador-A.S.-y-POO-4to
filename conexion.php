<?php

$host = 'localhost';
$usuario = 'root';
$contraseña = '';
$base_datos = 'pagina_deportiva2'; // Cambia este nombre si tu base de datos local tiene otro nombre

$conexion = new mysqli($host, $usuario, $contraseña, $base_datos);

if ($conexion->connect_error) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos: ' . $conexion->connect_error]);
    exit;
}

$conexion->set_charset("utf8mb4");
// Ajustar zona horaria para que NOW() y TIMESTAMP reflejen hora local
// PHP
if (function_exists('date_default_timezone_set')) {
    @date_default_timezone_set('America/Guayaquil');
}
// MySQL (por conexión)
@$conexion->query("SET time_zone = '-05:00'");
?>
