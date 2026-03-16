<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'pagina_deportiva2';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Error: " . $conn->connect_error);
}

$conn->query("ALTER TABLE usuarios ADD COLUMN ultima_conexion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
$conn->query("ALTER TABLE usuarios ADD COLUMN plan VARCHAR(100) DEFAULT NULL");
$conn->query("ALTER TABLE usuarios ADD COLUMN precio DECIMAL(10,2) DEFAULT NULL");

echo "Listo";
$conn->close();
?>
