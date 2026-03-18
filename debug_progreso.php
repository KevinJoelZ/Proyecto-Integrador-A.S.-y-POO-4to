<?php
/**
 * Depurar error de progreso
 */

header('Content-Type: application/json');

$response = ['success' => false, 'message' => '', 'debug' => []];

$response['debug'][] = '1. Incluyendo conexión...';

// Incluir conexión
if (file_exists(__DIR__ . '/conexión.php')) {
    include __DIR__ . '/conexión.php';
    $response['debug'][] = '2. Conexión incluida (conexión.php)';
} elseif (file_exists(__DIR__ . '/conexion.php')) {
    include __DIR__ . '/conexion.php';
    $response['debug'][] = '2. Conexión incluida (conexion.php)';
} else {
    $response['message'] = 'Archivo de conexión no encontrado';
    echo json_encode($response);
    exit;
}

if (!$conexion) {
    $response['message'] = 'Error de conexión a la base de datos';
    $response['debug'][] = '3. Error de conexión';
    echo json_encode($response);
    exit;
}

$response['debug'][] = '3. Conexión OK';

// Obtener datos JSON
$datos = json_decode(file_get_contents('php://input'), true);
$response['debug'][] = '4. Datos recibidos: ' . json_encode($datos);

if (!$datos) {
    $response['message'] = 'No se recibieron datos';
    echo json_encode($response);
    exit;
}

// Validar datos mínimos
$usuario_id = isset($datos['usuario_id']) ? intval($datos['usuario_id']) : 0;
$response['debug'][] = '5. Usuario ID: ' . $usuario_id;

if (!$usuario_id) {
    $response['message'] = 'Faltan datos requeridos: usuario_id=' . $usuario_id;
    echo json_encode($response);
    exit;
}

// Obtener campos
$fecha = isset($datos['fecha_registro']) ? trim($datos['fecha_registro']) : date('Y-m-d');
$tipo_medida = isset($datos['tipo_medida']) ? trim($datos['tipo_medida']) : '';
$valor_actual = isset($datos['valor_actual']) ? floatval($datos['valor_actual']) : 0;
$valor_objetivo = isset($datos['valor_objetivo']) ? floatval($datos['valor_objetivo']) : 0;
$esfuerzo = isset($datos['esfuerzo']) ? intval($datos['esfuerzo']) : 0;
$descripcion = isset($datos['notas']) ? trim($datos['notas']) : '';

$response['debug'][] = '6. Fecha: ' . $fecha;
$response['debug'][] = '7. Tipo: ' . $tipo_medida;
$response['debug'][] = '8. Valor: ' . $valor_actual;
$response['debug'][] = '9. Objetivo: ' . $valor_objetivo;

// Verificar estructura de la tabla
$response['debug'][] = '10. Verificando estructura de tabla...';
$estructura = $conexion->query("DESCRIBE progresos");
$columnas = [];
while ($row = $estructura->fetch_assoc()) {
    $columnas[] = $row['Field'];
}
$response['debug'][] = '11. Columnas de progresos: ' . json_encode($columnas);

// Verificar si hay valores nulos que causan problemas
$rutina_id = null; // Ahora es null explícitamente
$porcentaje = 0;
if ($valor_objetivo > 0 && $valor_actual > 0) {
    $porcentaje = min(100, ($valor_actual / $valor_objetivo) * 100);
}

// Insertar en la base de datos - usando solo campos que existen
$sql = "INSERT INTO progresos (usuario_id, fecha, tipo_medida, valor_actual, valor_objetivo, esfuerzo, descripcion, porcentaje_completado) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$response['debug'][] = '12. Preparando SQL: ' . $sql;

$stmt = $conexion->prepare($sql);

if (!$stmt) {
    $response['message'] = 'Error de preparación: ' . $conexion->error;
    $response['debug'][] = '13. Error de preparación: ' . $conexion->error;
    echo json_encode($response);
    exit;
}

$stmt->bind_param("issdiisd", 
    $usuario_id,
    $fecha,
    $tipo_medida,
    $valor_actual,
    $valor_objetivo,
    $esfuerzo,
    $descripcion,
    $porcentaje
);

$response['debug'][] = '14. Ejecutando...';

if ($stmt->execute()) {
    $response['success'] = true;
    $response['message'] = 'Progreso guardado exitosamente';
    $response['id'] = $conexion->insert_id;
    $response['debug'][] = '15. Éxito! ID: ' . $conexion->insert_id;
} else {
    $response['message'] = 'Error al guardar progreso: ' . $stmt->error;
    $response['debug'][] = '16. Error: ' . $stmt->error;
}

$stmt->close();
$conexion->close();

echo json_encode($response);
?>
