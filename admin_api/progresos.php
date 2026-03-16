<?php
/**
 * API REST para gestionar Progresos
 * Endpoints disponibles:
 * - GET /admin_api/progresos.php?action=obtener&usuario_id=1
 * - POST /admin_api/progresos.php?action=registrar
 * - POST /admin_api/progresos.php?action=actualizar
 * - GET /admin_api/progresos.php?action=estadisticas&usuario_id=1&tipo_medida=peso
 * - GET /admin_api/progresos.php?action=mejora&usuario_id=1&tipo_medida=peso&dias=30
 * - DELETE /admin_api/progresos.php?action=eliminar&id=1
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../classes/Progreso.php';

$response = ['success' => false, 'message' => 'Acción no reconocida'];

try {
    $action = $_GET['action'] ?? $_POST['action'] ?? 'obtener';
    $progreso = new Progreso($conexion);

    switch ($action) {
        case 'obtener':
            // Obtener progreso por ID
            if (isset($_GET['id'])) {
                $progreso_data = $progreso->obtenerPorId($_GET['id']);
                $response = $progreso_data ? ['success' => true, 'data' => $progreso_data] : 
                           ['success' => false, 'message' => 'Progreso no encontrado'];
            }
            // Obtener progresos de un usuario
            elseif (isset($_GET['usuario_id'])) {
                $limite = $_GET['limite'] ?? 30;
                $progresos = $progreso->obtenerPorUsuario($_GET['usuario_id'], $limite);
                $response = ['success' => true, 'data' => $progresos, 'total' => count($progresos)];
            }
            // Obtener progresos de una rutina
            elseif (isset($_GET['rutina_id'])) {
                $progresos = $progreso->obtenerPorRutina($_GET['rutina_id']);
                $response = ['success' => true, 'data' => $progresos, 'total' => count($progresos)];
            }
            break;

        case 'registrar':
            $datos = json_decode(file_get_contents('php://input'), true);
            
            $progreso->setUsuarioId($datos['usuario_id']);
            $progreso->setRutinaId($datos['rutina_id'] ?? null);
            $progreso->setFechaRegistro($datos['fecha_registro'] ?? date('Y-m-d'));
            $progreso->setTipoMedida($datos['tipo_medida']);
            $progreso->setValorActual($datos['valor_actual']);
            $progreso->setValorObjetivo($datos['valor_objetivo'] ?? 0);
            $progreso->setNotas($datos['notas'] ?? '');
            $progreso->setEsfuerzo($datos['esfuerzo'] ?? 5);
            
            $response = $progreso->registrar();
            break;

        case 'actualizar':
            $datos = json_decode(file_get_contents('php://input'), true);
            
            $progreso->setId($datos['id']);
            $progreso->setValorActual($datos['valor_actual']);
            $progreso->setValorObjetivo($datos['valor_objetivo']);
            $progreso->setNotas($datos['notas'] ?? '');
            $progreso->setEsfuerzo($datos['esfuerzo'] ?? 5);
            
            $response = $progreso->actualizar();
            break;

        case 'estadisticas':
            $usuario_id = $_GET['usuario_id'];
            $tipo_medida = $_GET['tipo_medida'];
            $stats = $progreso->obtenerEstadisticas($usuario_id, $tipo_medida);
            $response = ['success' => true, 'data' => $stats];
            break;

        case 'mejora':
            $usuario_id = $_GET['usuario_id'];
            $tipo_medida = $_GET['tipo_medida'];
            $dias = $_GET['dias'] ?? 30;
            $mejora = $progreso->calcularMejora($usuario_id, $tipo_medida, $dias);
            
            if ($mejora) {
                $response = ['success' => true, 'data' => $mejora];
            } else {
                $response = ['success' => false, 'message' => 'No hay suficientes datos para calcular mejora'];
            }
            break;

        case 'reciente':
            $usuario_id = $_GET['usuario_id'];
            $dias = $_GET['dias'] ?? 7;
            $progresos_recientes = $progreso->obtenerProgresoReciente($usuario_id, $dias);
            $response = ['success' => true, 'data' => $progresos_recientes, 'total' => count($progresos_recientes)];
            break;

        case 'eliminar':
            $id = $_GET['id'] ?? (json_decode(file_get_contents('php://input'), true)['id'] ?? null);
            $response = $progreso->eliminar($id);
            break;

        default:
            $response = ['success' => false, 'message' => 'Acción no reconocida: ' . $action];
    }

} catch (Exception $e) {
    http_response_code(500);
    $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
}

http_response_code($response['success'] ? 200 : 400);
echo json_encode($response);
?>

