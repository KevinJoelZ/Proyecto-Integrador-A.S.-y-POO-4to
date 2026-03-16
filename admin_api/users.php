<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Cargar conexión soportando ambos nombres de archivo
ob_start();
if (file_exists(__DIR__ . '/../conexión.php')) {
    require_once __DIR__ . '/../conexión.php';
} else {
    require_once __DIR__ . '/../conexion.php';
}
// Descartar cualquier salida accidental del archivo de conexión
ob_end_clean();


require_once __DIR__ . '/../classes/Usuario.php';
$action = $_GET['action'] ?? 'list';

try {
    // Endpoint para obtener usuario por UID
    if ($action === 'obtener_por_uid' && isset($_GET['uid'])) {
        $usuario = new Usuario($conexion);
        $data = $usuario->obtenerPorUid($_GET['uid']);
        if ($data) {
            echo json_encode(['success' => true, 'data' => $data]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
        }
        exit;
    }

    // Listar usuarios (por defecto)
    $q = "SELECT id, uid, nombre, email, rol, ultima_conexion FROM usuarios ORDER BY ultima_conexion DESC, id DESC LIMIT 200";
    $res = $conexion->query($q);
    if ($res === false) {
        throw new Exception('Error al consultar usuarios: ' . $conexion->error);
    }
    $items = [];
    while ($row = $res->fetch_assoc()) { $items[] = $row; }
    echo json_encode(['success' => true, 'items' => $items]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

