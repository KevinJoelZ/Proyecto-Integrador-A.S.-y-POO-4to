<?php
/**
 * Clase Progreso - Gestiona el registro de progresos y resultados de entrenamientos
 * Implementa funcionalidades para registrar, actualizar y analizar el progreso del usuario
 */
class Progreso {
    private $id;
    private $usuario_id;
    private $rutina_id;
    private $fecha_registro;
    private $tipo_medida; // peso, distancia, series, repeticiones, tiempo, velocidad, etc.
    private $valor_actual;
    private $valor_objetivo;
    private $porcentaje_completado;
    private $notas;
    private $esfuerzo; // 1-10 escala de percepción de esfuerzo
    private $fecha_creacion;
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    /**
     * Registrar nuevo progreso
     */
    public function registrar() {
        // Calcular porcentaje completado
        if ($this->valor_objetivo > 0) {
            $this->porcentaje_completado = ($this->valor_actual / $this->valor_objetivo) * 100;
        } else {
            $this->porcentaje_completado = 0;
        }

        $sql = "INSERT INTO progresos (usuario_id, rutina_id, fecha_registro, tipo_medida, valor_actual, 
                valor_objetivo, porcentaje_completado, notas, esfuerzo, fecha_creacion) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) {
            return ['success' => false, 'message' => 'Error de preparación: ' . $this->conexion->error];
        }

        $stmt->bind_param("iissdddis", $this->usuario_id, $this->rutina_id, $this->fecha_registro, 
                         $this->tipo_medida, $this->valor_actual, $this->valor_objetivo, 
                         $this->porcentaje_completado, $this->notas, $this->esfuerzo);

        if ($stmt->execute()) {
            $this->id = $this->conexion->insert_id;
            return ['success' => true, 'message' => 'Progreso registrado exitosamente', 'id' => $this->id];
        } else {
            return ['success' => false, 'message' => 'Error al registrar progreso: ' . $stmt->error];
        }
    }

    /**
     * Actualizar registro de progreso
     */
    public function actualizar() {
        // Recalcular porcentaje
        if ($this->valor_objetivo > 0) {
            $this->porcentaje_completado = ($this->valor_actual / $this->valor_objetivo) * 100;
        }

        $sql = "UPDATE progresos SET valor_actual = ?, valor_objetivo = ?, porcentaje_completado = ?, 
                notas = ?, esfuerzo = ? WHERE id = ?";
        
        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) {
            return ['success' => false, 'message' => 'Error de preparación: ' . $this->conexion->error];
        }

        $stmt->bind_param("dddisi", $this->valor_actual, $this->valor_objetivo, 
                         $this->porcentaje_completado, $this->notas, $this->esfuerzo, $this->id);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Progreso actualizado exitosamente'];
        } else {
            return ['success' => false, 'message' => 'Error al actualizar progreso: ' . $stmt->error];
        }
    }

    /**
     * Obtener progreso por ID
     */
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM progresos WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $progreso = $resultado->fetch_assoc();
            $this->cargarDesdeArray($progreso);
            return $progreso;
        }
        return null;
    }

    /**
     * Obtener progresos de un usuario
     */
    public function obtenerPorUsuario($usuario_id, $limite = 30) {
        $sql = "SELECT * FROM progresos WHERE usuario_id = ? ORDER BY fecha_registro DESC LIMIT ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ii", $usuario_id, $limite);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Obtener progresos de una rutina específica
     */
    public function obtenerPorRutina($rutina_id) {
        $sql = "SELECT * FROM progresos WHERE rutina_id = ? ORDER BY fecha_registro DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $rutina_id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Obtener estadísticas de un usuario por tipo de medida
     */
    public function obtenerEstadisticas($usuario_id, $tipo_medida) {
        $sql = "SELECT 
                COUNT(*) as total_registros,
                AVG(valor_actual) as promedio,
                MAX(valor_actual) as maximo,
                MIN(valor_actual) as minimo,
                AVG(esfuerzo) as esfuerzo_promedio
                FROM progresos 
                WHERE usuario_id = ? AND tipo_medida = ?";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("is", $usuario_id, $tipo_medida);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->fetch_assoc();
    }

    /**
     * Obtener progreso reciente de un usuario
     */
    public function obtenerProgresoReciente($usuario_id, $dias = 7) {
        $sql = "SELECT * FROM progresos 
                WHERE usuario_id = ? 
                AND fecha_registro >= DATE_SUB(NOW(), INTERVAL ? DAY)
                ORDER BY fecha_registro DESC";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ii", $usuario_id, $dias);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Calcular mejora porcentual en un período
     */
    public function calcularMejora($usuario_id, $tipo_medida, $dias = 30) {
        $fecha_inicio = date('Y-m-d H:i:s', strtotime("-{$dias} days"));
        
        $sql = "SELECT 
                (SELECT valor_actual FROM progresos 
                 WHERE usuario_id = ? AND tipo_medida = ? AND fecha_registro >= ? 
                 ORDER BY fecha_registro ASC LIMIT 1) as valor_inicio,
                (SELECT valor_actual FROM progresos 
                 WHERE usuario_id = ? AND tipo_medida = ? AND fecha_registro >= ? 
                 ORDER BY fecha_registro DESC LIMIT 1) as valor_fin";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("isisis", $usuario_id, $tipo_medida, $fecha_inicio, 
                         $usuario_id, $tipo_medida, $fecha_inicio);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $datos = $resultado->fetch_assoc();

        if ($datos['valor_inicio'] && $datos['valor_fin']) {
            $mejora = (($datos['valor_fin'] - $datos['valor_inicio']) / $datos['valor_inicio']) * 100;
            return ['mejora_porcentual' => round($mejora, 2), 'valor_inicio' => $datos['valor_inicio'], 'valor_fin' => $datos['valor_fin']];
        }
        return null;
    }

    /**
     * Eliminar registro de progreso
     */
    public function eliminar($id) {
        $sql = "DELETE FROM progresos WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Progreso eliminado exitosamente'];
        } else {
            return ['success' => false, 'message' => 'Error al eliminar progreso: ' . $stmt->error];
        }
    }

    /**
     * Cargar datos desde array
     */
    private function cargarDesdeArray($datos) {
        $this->id = $datos['id'] ?? null;
        $this->usuario_id = $datos['usuario_id'] ?? null;
        $this->rutina_id = $datos['rutina_id'] ?? null;
        $this->fecha_registro = $datos['fecha_registro'] ?? date('Y-m-d');
        $this->tipo_medida = $datos['tipo_medida'] ?? '';
        $this->valor_actual = $datos['valor_actual'] ?? 0;
        $this->valor_objetivo = $datos['valor_objetivo'] ?? 0;
        $this->porcentaje_completado = $datos['porcentaje_completado'] ?? 0;
        $this->notas = $datos['notas'] ?? '';
        $this->esfuerzo = $datos['esfuerzo'] ?? 5;
        $this->fecha_creacion = $datos['fecha_creacion'] ?? '';
    }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setUsuarioId($usuario_id) { $this->usuario_id = $usuario_id; }
    public function setRutinaId($rutina_id) { $this->rutina_id = $rutina_id; }
    public function setFechaRegistro($fecha) { $this->fecha_registro = $fecha; }
    public function setTipoMedida($tipo) { $this->tipo_medida = $tipo; }
    public function setValorActual($valor) { $this->valor_actual = $valor; }
    public function setValorObjetivo($valor) { $this->valor_objetivo = $valor; }
    public function setNotas($notas) { $this->notas = $notas; }
    public function setEsfuerzo($esfuerzo) { $this->esfuerzo = $esfuerzo; }

    // Getters
    public function getId() { return $this->id; }
    public function getUsuarioId() { return $this->usuario_id; }
    public function getRutinaId() { return $this->rutina_id; }
    public function getFechaRegistro() { return $this->fecha_registro; }
    public function getTipoMedida() { return $this->tipo_medida; }
    public function getValorActual() { return $this->valor_actual; }
    public function getValorObjetivo() { return $this->valor_objetivo; }
    public function getPorcentajeCompletado() { return $this->porcentaje_completado; }
    public function getNotas() { return $this->notas; }
    public function getEsfuerzo() { return $this->esfuerzo; }
    public function getFechaCreacion() { return $this->fecha_creacion; }
}
?>
