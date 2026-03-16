<?php
// Configuración para XAMPP local

$host = "localhost";
$user = "root";
$password = "";
$database = "pagina_deportiva"; // Cambia este nombre si tu base de datos local tiene otro nombre

// Crear conexión
$conexion = new mysqli($host, $user, $password, $database);

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión a XAMPP: " . $conexion->connect_error);
}

// Establecer charset a utf8
$conexion->set_charset("utf8");

//echo "Conexión exitosa a XAMPP"; // Para pruebas
?>