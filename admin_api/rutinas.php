<?php
/**
 * API REST para gestionar Rutinas
 * Endpoints disponibles:
 * - GET /admin_api/rutinas.php?action=obtener&usuario_id=1
 * - POST /admin_api/rutinas.php?action=crear
 * - POST /admin_api/rutinas.php?action=actualizar
 * - POST /admin_api/rutinas.php?action=cambiar_estado
 * - DELETE /admin_api/rutinas.php?action=eliminar&id=1
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../classes/Rutina.php';

$response = ['success' => false, 'message' => 'Acción no reconocida'];

try {
    $action = $_GET['action'] ?? $_POST['action'] ?? 'obtener';
    $rutina = new Rutina($conexion);

    switch ($action) {
        case 'obtener':
            // Obtener rutina por ID
            if (isset($_GET['id'])) {
                $rutina_data = $rutina->obtenerPorId($_GET['id']);
                $response = $rutina_data ? ['success' => true, 'data' => $rutina_data] : 
                           ['success' => false, 'message' => 'Rutina no encontrada'];
            }
            // Obtener rutinas de un usuario
            elseif (isset($_GET['usuario_id'])) {
                $estado = $_GET['estado'] ?? null;
                $rutinas = $rutina->obtenerPorUsuario($_GET['usuario_id'], $estado);
                $response = ['success' => true, 'data' => $rutinas, 'total' => count($rutinas)];
            }
            // Obtener rutinas de un entrenador
            elseif (isset($_GET['entrenador_id'])) {
                $rutinas = $rutina->obtenerPorEntrenador($_GET['entrenador_id']);
                $response = ['success' => true, 'data' => $rutinas, 'total' => count($rutinas)];
            }
            // Obtener todas las rutinas
            else {
                $filtro = $_GET['deporte'] ?? null;
                $rutinas = $rutina->obtenerTodas($filtro);
                $response = ['success' => true, 'data' => $rutinas, 'total' => count($rutinas)];
            }
            break;

        case 'crear':
            $datos = json_decode(file_get_contents('php://input'), true);
            
            $rutina->setUsuarioId($datos['usuario_id']);
            $rutina->setEntrenadorId($datos['entrenador_id'] ?? null);
            $rutina->setNombre($datos['nombre']);
            $rutina->setDeporte($datos['deporte']);
            $rutina->setDescripcion($datos['descripcion'] ?? '');
            $rutina->setObjetivo($datos['objetivo']);
            $rutina->setNivel($datos['nivel'] ?? 'intermedio');
            $rutina->setDuracionSemanas($datos['duracion_semanas'] ?? 4);
            $rutina->setFrecuenciaSemanal($datos['frecuencia_semanal'] ?? 3);
            $rutina->setFechaInicio($datos['fecha_inicio'] ?? date('Y-m-d'));
            $rutina->setFechaFin($datos['fecha_fin'] ?? null);
            
            $response = $rutina->crear();
            break;

        case 'actualizar':
            $datos = json_decode(file_get_contents('php://input'), true);
            
            $rutina->setId($datos['id']);
            $rutina->setNombre($datos['nombre']);
            $rutina->setDeporte($datos['deporte']);
            $rutina->setDescripcion($datos['descripcion'] ?? '');
            $rutina->setObjetivo($datos['objetivo']);
            $rutina->setNivel($datos['nivel']);
            $rutina->setDuracionSemanas($datos['duracion_semanas']);
            $rutina->setFrecuenciaSemanal($datos['frecuencia_semanal']);
            $rutina->setFechaInicio($datos['fecha_inicio']);
            $rutina->setFechaFin($datos['fecha_fin']);
            $rutina->setEstado($datos['estado'] ?? 'activa');
            
            $response = $rutina->actualizar();
            break;

        case 'cambiar_estado':
            $datos = json_decode(file_get_contents('php://input'), true);
            $response = $rutina->cambiarEstado($datos['id'], $datos['estado']);
            break;

        case 'eliminar':
            $id = $_GET['id'] ?? (json_decode(file_get_contents('php://input'), true)['id'] ?? null);
            $response = $rutina->eliminar($id);
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

