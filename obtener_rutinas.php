<?php
/**
 * Obtener rutinas - Versión simplificada
 */

header('Content-Type: application/json');

$response = ['success' => false, 'data' => [], 'message' => ''];

// Incluir conexión
if (file_exists(__DIR__ . '/conexión.php')) {
    include __DIR__ . '/conexión.php';
} elseif (file_exists(__DIR__ . '/conexion.php')) {
    include __DIR__ . '/conexion.php';
} else {
    $response['message'] = 'Archivo de conexión no encontrado';
    echo json_encode($response);
    exit;
}

if (!$conexion) {
    $response['message'] = 'Error de conexión a la base de datos';
    echo json_encode($response);
    exit;
}

// Obtener usuario_id
$usuario_id = isset($_GET['usuario_id']) ? intval($_GET['usuario_id']) : 0;

if (!$usuario_id) {
    $response['message'] = 'Usuario no especificado';
    echo json_encode($response);
    exit;
}

// Obtener rutinas
$sql = "SELECT * FROM rutinas WHERE usuario_id = ? ORDER BY fecha_creacion DESC";
$stmt = $conexion->prepare($sql);

if (!$stmt) {
    $response['message'] = 'Error de preparación: ' . $conexion->error;
    echo json_encode($response);
    exit;
}

$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$resultado = $stmt->get_result();

$rutinas = [];
while ($row = $resultado->fetch_assoc()) {
    $rutinas[] = $row;
}

$response['success'] = true;
$response['data'] = $rutinas;
$response['total'] = count($rutinas);

$stmt->close();
$conexion->close();

echo json_encode($response);
?>
