<?php
/**
 * Guardar rutina - Versión simplificada
 */

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

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

// Obtener datos JSON
$datos = json_decode(file_get_contents('php://input'), true);

if (!$datos) {
    $response['message'] = 'No se recibieron datos';
    echo json_encode($response);
    exit;
}

// Validar datos mínimos
$usuario_id = isset($datos['usuario_id']) ? intval($datos['usuario_id']) : 0;
$nombre = isset($datos['nombre']) ? trim($datos['nombre']) : '';
$deporte = isset($datos['deporte']) ? trim($datos['deporte']) : '';

if (!$usuario_id || !$nombre || !$deporte) {
    $response['message'] = 'Faltan datos requeridos: usuario_id=' . $usuario_id . ', nombre=' . $nombre . ', deporte=' . $deporte;
    echo json_encode($response);
    exit;
}

// Valores opcionales
$descripcion = isset($datos['descripcion']) ? trim($datos['descripcion']) : '';
$objetivo = isset($datos['objetivo']) ? trim($datos['objetivo']) : '';
$nivel = isset($datos['nivel']) ? trim($datos['nivel']) : 'intermedio';
$duracion = isset($datos['duracion_semanas']) ? intval($datos['duracion_semanas']) : 4;
$frecuencia = isset($datos['frecuencia_semanal']) ? intval($datos['frecuencia_semanal']) : 3;
$fecha_inicio = isset($datos['fecha_inicio']) ? trim($datos['fecha_inicio']) : date('Y-m-d');
$fecha_fin = isset($datos['fecha_fin']) ? trim($datos['fecha_fin']) : null;

// Insertar en la base de datos
$sql = "INSERT INTO rutinas (usuario_id, nombre, deporte, descripcion, objetivo, nivel, duracion_semanas, frecuencia_semanal, fecha_inicio, fecha_fin, estado, fecha_creacion) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'activa', NOW())";

$stmt = $conexion->prepare($sql);

if (!$stmt) {
    $response['message'] = 'Error de preparación: ' . $conexion->error;
    echo json_encode($response);
    exit;
}

$stmt->bind_param("isssssiiss", 
    $usuario_id,
    $nombre,
    $deporte,
    $descripcion,
    $objetivo,
    $nivel,
    $duracion,
    $frecuencia,
    $fecha_inicio,
    $fecha_fin
);

if ($stmt->execute()) {
    $response['success'] = true;
    $response['message'] = 'Rutina creada exitosamente';
    $response['id'] = $conexion->insert_id;
} else {
    $response['message'] = 'Error al crear rutina: ' . $stmt->error;
}

$stmt->close();
$conexion->close();

echo json_encode($response);
?>
