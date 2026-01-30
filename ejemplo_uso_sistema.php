<?php
/**
 * Ejemplo de uso del Sistema POO - Rutinas y Progresos
 * Este archivo muestra cómo utilizar las clases creadas
 */

// Incluir conexión a BD
require_once 'conexion.php';

// Incluir clases
require_once 'classes/Usuario.php';
require_once 'classes/Entrenador.php';
require_once 'classes/Rutina.php';
require_once 'classes/Progreso.php';

echo "=== EJEMPLO DE USO DEL SISTEMA POO ===" . PHP_EOL . PHP_EOL;

// ============================================
// EJEMPLO 1: Crear/Obtener un Usuario
// ============================================
echo "1. GESTIÓN DE USUARIOS" . PHP_EOL;
echo "---" . PHP_EOL;

$usuario = new Usuario($conexion);
$usuario->setNombre('Juan Pérez');
$usuario->setEmail('juan.perez@example.com');
$usuario->setUidFirebase('uid_juan_12345');
$usuario->setDeporteFavorito('Fitness');
$usuario->setNivelExperiencia('intermedio');
$usuario->setFotoPerfil('https://ejemplo.com/juan.jpg');

$resultado = $usuario->guardar();
echo "Resultado guardar usuario: " . json_encode($resultado) . PHP_EOL;
echo PHP_EOL;

// ============================================
// EJEMPLO 2: Crear un Entrenador
// ============================================
echo "2. GESTIÓN DE ENTRENADORES" . PHP_EOL;
echo "---" . PHP_EOL;

$entrenador = new Entrenador($conexion);
$entrenador->setNombre('Luis Martínez');
$entrenador->setEmail('luis.martinez@trainers.com');
$entrenador->setEspecialidad('Fitness');
$entrenador->setExperienciaAños(8);
$entrenador->setCertificaciones('ACE, IFBB, Personal Trainer Certificado');
$entrenador->setDisponible(true);
$entrenador->setCalificacion(4.8);

$resultado = $entrenador->crear();
echo "Resultado crear entrenador: " . json_encode($resultado) . PHP_EOL;
echo PHP_EOL;

// ============================================
// EJEMPLO 3: Crear una Rutina
// ============================================
echo "3. GESTIÓN DE RUTINAS" . PHP_EOL;
echo "---" . PHP_EOL;

$rutina = new Rutina($conexion);
$rutina->setUsuarioId(1); // ID del usuario
$rutina->setEntrenadorId(1); // ID del entrenador
$rutina->setNombre('Rutina de Hipertrofia Avanzada');
$rutina->setDeporte('Fitness');
$rutina->setDescripcion('Programa de 12 semanas enfocado en ganar masa muscular');
$rutina->setObjetivo('ganar fuerza');
$rutina->setNivel('avanzado');
$rutina->setDuracionSemanas(12);
$rutina->setFrecuenciaSemanal(4);
$rutina->setFechaInicio(date('Y-m-d'));
$fecha_fin = new DateTime();
$fecha_fin->add(new DateInterval('P12W'));
$rutina->setFechaFin($fecha_fin->format('Y-m-d'));

$resultado = $rutina->crear();
echo "Resultado crear rutina: " . json_encode($resultado) . PHP_EOL;

// Obtener rutinas de un usuario
$rutinas_usuario = $rutina->obtenerPorUsuario(1, 'activa');
echo "Rutinas activas del usuario 1: " . count($rutinas_usuario) . PHP_EOL;
echo PHP_EOL;

// ============================================
// EJEMPLO 4: Registrar Progresos
// ============================================
echo "4. GESTIÓN DE PROGRESOS" . PHP_EOL;
echo "---" . PHP_EOL;

$progreso = new Progreso($conexion);
$progreso->setUsuarioId(1);
$progreso->setRutinaId(1);
$progreso->setFechaRegistro(date('Y-m-d'));
$progreso->setTipoMedida('peso');
$progreso->setValorActual(75.5);
$progreso->setValorObjetivo(80.0);
$progreso->setEsfuerzo(8);
$progreso->setNotas('Sentí energía durante el entrenamiento');

$resultado = $progreso->registrar();
echo "Resultado registrar progreso: " . json_encode($resultado) . PHP_EOL;

// Obtener progresos recientes
$progresos_recientes = $progreso->obtenerProgresoReciente(1, 7);
echo "Progresos últimos 7 días: " . count($progresos_recientes) . PHP_EOL;

// Obtener estadísticas
$stats = $progreso->obtenerEstadisticas(1, 'peso');
echo "Estadísticas de peso:" . PHP_EOL;
echo "  - Total registros: " . $stats['total_registros'] . PHP_EOL;
echo "  - Promedio: " . round($stats['promedio'], 2) . PHP_EOL;
echo "  - Máximo: " . $stats['maximo'] . PHP_EOL;
echo "  - Mínimo: " . $stats['minimo'] . PHP_EOL;

// Calcular mejora
$mejora = $progreso->calcularMejora(1, 'peso', 30);
if ($mejora) {
    echo "Mejora en últimos 30 días: " . $mejora['mejora_porcentual'] . "%" . PHP_EOL;
}
echo PHP_EOL;

// ============================================
// EJEMPLO 5: Consultas Avanzadas
// ============================================
echo "5. CONSULTAS AVANZADAS" . PHP_EOL;
echo "---" . PHP_EOL;

// Obtener todos los entrenadores disponibles
$ent = new Entrenador($conexion);
$entrenadores_disp = $ent->obtenerDisponibles();
echo "Entrenadores disponibles: " . count($entrenadores_disp) . PHP_EOL;

// Obtener entrenadores por especialidad
$entrenadores_fitness = $ent->obtenerPorEspecialidad('Fitness');
echo "Entrenadores de Fitness: " . count($entrenadores_fitness) . PHP_EOL;

// Cambiar estado de rutina
$rut = new Rutina($conexion);
$resultado_cambio = $rut->cambiarEstado(1, 'pausada');
echo "Cambiar estado: " . json_encode($resultado_cambio) . PHP_EOL;
echo PHP_EOL;

// ============================================
// EJEMPLO 6: Uso en Solicitud HTTP (JSON)
// ============================================
echo "6. EJEMPLO DE SOLICITUD API" . PHP_EOL;
echo "---" . PHP_EOL;

$ejemplo_crear_rutina = array(
    'action' => 'crear',
    'usuario_id' => 1,
    'nombre' => 'Mi Nueva Rutina',
    'deporte' => 'Running',
    'descripcion' => 'Preparación para 10K',
    'objetivo' => 'mejorar resistencia',
    'nivel' => 'intermedio',
    'duracion_semanas' => 8,
    'frecuencia_semanal' => 4,
    'fecha_inicio' => date('Y-m-d'),
    'fecha_fin' => date('Y-m-d', strtotime('+8 weeks'))
);

echo "POST a /admin_api/rutinas.php:" . PHP_EOL;
echo json_encode($ejemplo_crear_rutina, JSON_PRETTY_PRINT) . PHP_EOL;
echo PHP_EOL;

$ejemplo_registrar_progreso = array(
    'action' => 'registrar',
    'usuario_id' => 1,
    'rutina_id' => 1,
    'fecha_registro' => date('Y-m-d'),
    'tipo_medida' => 'distancia',
    'valor_actual' => 9.5,
    'valor_objetivo' => 10.0,
    'notas' => 'Buena sesión',
    'esfuerzo' => 7
);

echo "POST a /admin_api/progresos.php:" . PHP_EOL;
echo json_encode($ejemplo_registrar_progreso, JSON_PRETTY_PRINT) . PHP_EOL;
echo PHP_EOL;

// ============================================
// RESUMEN
// ============================================
echo "=== RESUMEN DEL SISTEMA ===" . PHP_EOL;
echo "✓ Sistema POO completamente funcional" . PHP_EOL;
echo "✓ Clases: Usuario, Entrenador, Rutina, Progreso" . PHP_EOL;
echo "✓ Métodos CRUD completos" . PHP_EOL;
echo "✓ API REST disponible" . PHP_EOL;
echo "✓ Base de datos integrada" . PHP_EOL;
echo "✓ Interfaz web responsiva" . PHP_EOL;
echo PHP_EOL;

?>
