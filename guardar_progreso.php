<?php
/**
 * Guardar progreso - Versión simplificada
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
$fecha = isset($datos['fecha_registro']) ? trim($datos['fecha_registro']) : date('Y-m-d');

if (!$usuario_id) {
    $response['message'] = 'Faltan datos requeridos: usuario_id=' . $usuario_id;
    echo json_encode($response);
    exit;
}

// Valores opcionales
$rutina_id = isset($datos['rutina_id']) ? intval($datos['rutina_id']) : null;
$tipo_medida = isset($datos['tipo_medida']) ? trim($datos['tipo_medida']) : '';
$valor_actual = isset($datos['valor_actual']) ? floatval($datos['valor_actual']) : 0;
$valor_objetivo = isset($datos['valor_objetivo']) ? floatval($datos['valor_objetivo']) : 0;
$esfuerzo = isset($datos['esfuerzo']) ? intval($datos['esfuerzo']) : 0;
$descripcion = isset($datos['notas']) ? trim($datos['notas']) : '';

// Calcular porcentaje completado
$porcentaje = 0;
if ($valor_objetivo > 0 && $valor_actual > 0) {
    $porcentaje = min(100, ($valor_actual / $valor_objetivo) * 100);
}

// Insertar en la base de datos
$sql = "INSERT INTO progresos (usuario_id, rutina_id, fecha, tipo_medida, valor_actual, valor_objetivo, esfuerzo, descripcion, porcentaje_completado, fecha_creacion) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

$stmt = $conexion->prepare($sql);

if (!$stmt) {
    $response['message'] = 'Error de preparación: ' . $conexion->error;
    echo json_encode($response);
    exit;
}

$stmt->bind_param("iissdiisd", 
    $usuario_id,
    $rutina_id,
    $fecha,
    $tipo_medida,
    $valor_actual,
    $valor_objetivo,
    $esfuerzo,
    $descripcion,
    $porcentaje
);

if ($stmt->execute()) {
    $response['success'] = true;
    $response['message'] = 'Progreso guardado exitosamente';
    $response['id'] = $conexion->insert_id;
} else {
    $response['message'] = 'Error al guardar progreso: ' . $stmt->error;
}

$stmt->close();
$conexion->close();

echo json_encode($response);
?>
