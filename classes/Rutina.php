<?php
/**
 * Clase Rutina - Gestiona rutinas de entrenamiento
 * Implementa funcionalidades para crear, actualizar y obtener rutinas personalizadas
 */
class Rutina {
    private $id;
    private $usuario_id;
    private $entrenador_id;
    private $nombre;
    private $deporte;
    private $descripcion;
    private $objetivo; // ganar fuerza, resistencia, flexibilidad, etc.
    private $nivel; // principiante, intermedio, avanzado
    private $duracion_semanas;
    private $frecuencia_semanal; // número de días por semana
    private $fecha_inicio;
    private $fecha_fin;
    private $estado; // activa, completada, pausada
    private $fecha_creacion;
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    /**
     * Crear nueva rutina
     */
    public function crear() {
        $sql = "INSERT INTO rutinas (usuario_id, entrenador_id, nombre, deporte, descripcion, objetivo, nivel, 
                duracion_semanas, frecuencia_semanal, fecha_inicio, fecha_fin, estado, fecha_creacion) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) {
            return ['success' => false, 'message' => 'Error de preparación: ' . $this->conexion->error];
        }

        $stmt->bind_param(
            "iisssssssssss",
            $this->usuario_id,
            $this->entrenador_id,
            $this->nombre,
            $this->deporte,
            $this->descripcion,
            $this->objetivo,
            $this->nivel,
            $this->duracion_semanas,
            $this->frecuencia_semanal,
            $this->fecha_inicio,
            $this->fecha_fin,
            $this->estado
        );

        if ($stmt->execute()) {
            $this->id = $this->conexion->insert_id;
            return ['success' => true, 'message' => 'Rutina creada exitosamente', 'id' => $this->id];
        } else {
            return ['success' => false, 'message' => 'Error al crear rutina: ' . $stmt->error];
        }
    }

    /**
     * Actualizar rutina existente
     */
    public function actualizar() {
        $sql = "UPDATE rutinas SET nombre = ?, deporte = ?, descripcion = ?, objetivo = ?, nivel = ?, 
                duracion_semanas = ?, frecuencia_semanal = ?, fecha_inicio = ?, fecha_fin = ?, estado = ? WHERE id = ?";
        
        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) {
            return ['success' => false, 'message' => 'Error de preparación: ' . $this->conexion->error];
        }

        $stmt->bind_param("ssssssissi", $this->nombre, $this->deporte, $this->descripcion, 
                         $this->objetivo, $this->nivel, $this->duracion_semanas, 
                         $this->frecuencia_semanal, $this->fecha_inicio, $this->fecha_fin, 
                         $this->estado, $this->id);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Rutina actualizada exitosamente'];
        } else {
            return ['success' => false, 'message' => 'Error al actualizar rutina: ' . $stmt->error];
        }
    }

    /**
     * Obtener rutina por ID
     */
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM rutinas WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $rutina = $resultado->fetch_assoc();
            $this->cargarDesdeArray($rutina);
            return $rutina;
        }
        return null;
    }

    /**
     * Obtener rutinas de un usuario
     */
    public function obtenerPorUsuario($usuario_id, $estado = null) {
        $sql = "SELECT * FROM rutinas WHERE usuario_id = ?";
        if ($estado) {
            $sql .= " AND estado = '" . $this->conexion->real_escape_string($estado) . "'";
        }
        $sql .= " ORDER BY fecha_creacion DESC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Obtener rutinas activas de un usuario
     */
    public function obtenerActivasPorUsuario($usuario_id) {
        return $this->obtenerPorUsuario($usuario_id, 'activa');
    }

    /**
     * Obtener todas las rutinas de un entrenador
     */
    public function obtenerPorEntrenador($entrenador_id) {
        $sql = "SELECT * FROM rutinas WHERE entrenador_id = ? ORDER BY fecha_creacion DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $entrenador_id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Obtener todas las rutinas
     */
    public function obtenerTodas($filtro_deporte = null) {
        $sql = "SELECT * FROM rutinas";
        if ($filtro_deporte) {
            $sql .= " WHERE deporte = '" . $this->conexion->real_escape_string($filtro_deporte) . "'";
        }
        $sql .= " ORDER BY fecha_creacion DESC";
        
        $resultado = $this->conexion->query($sql);
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Cambiar estado de la rutina
     */
    public function cambiarEstado($id, $nuevo_estado) {
        $sql = "UPDATE rutinas SET estado = ? WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("si", $nuevo_estado, $id);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Estado actualizado exitosamente'];
        } else {
            return ['success' => false, 'message' => 'Error al actualizar estado: ' . $stmt->error];
        }
    }

    /**
     * Eliminar rutina
     */
    public function eliminar($id) {
        $sql = "DELETE FROM rutinas WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Rutina eliminada exitosamente'];
        } else {
            return ['success' => false, 'message' => 'Error al eliminar rutina: ' . $stmt->error];
        }
    }

    /**
     * Cargar datos desde array
     */
    private function cargarDesdeArray($datos) {
        $this->id = $datos['id'] ?? null;
        $this->usuario_id = $datos['usuario_id'] ?? null;
        $this->entrenador_id = $datos['entrenador_id'] ?? null;
        $this->nombre = $datos['nombre'] ?? '';
        $this->deporte = $datos['deporte'] ?? '';
        $this->descripcion = $datos['descripcion'] ?? '';
        $this->objetivo = $datos['objetivo'] ?? '';
        $this->nivel = $datos['nivel'] ?? 'principiante';
        $this->duracion_semanas = $datos['duracion_semanas'] ?? 4;
        $this->frecuencia_semanal = $datos['frecuencia_semanal'] ?? 3;
        $this->fecha_inicio = $datos['fecha_inicio'] ?? '';
        $this->fecha_fin = $datos['fecha_fin'] ?? '';
        $this->estado = $datos['estado'] ?? 'activa';
        $this->fecha_creacion = $datos['fecha_creacion'] ?? '';
    }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setUsuarioId($usuario_id) { $this->usuario_id = $usuario_id; }
    public function setEntrenadorId($entrenador_id) { $this->entrenador_id = $entrenador_id; }
    public function setNombre($nombre) { $this->nombre = $nombre; }
    public function setDeporte($deporte) { $this->deporte = $deporte; }
    public function setDescripcion($desc) { $this->descripcion = $desc; }
    public function setObjetivo($objetivo) { $this->objetivo = $objetivo; }
    public function setNivel($nivel) { $this->nivel = $nivel; }
    public function setDuracionSemanas($semanas) { $this->duracion_semanas = $semanas; }
    public function setFrecuenciaSemanal($freq) { $this->frecuencia_semanal = $freq; }
    public function setFechaInicio($fecha) { $this->fecha_inicio = $fecha; }
    public function setFechaFin($fecha) { $this->fecha_fin = $fecha; }
    public function setEstado($estado) { $this->estado = $estado; }

    // Getters
    public function getId() { return $this->id; }
    public function getUsuarioId() { return $this->usuario_id; }
    public function getEntrenadorId() { return $this->entrenador_id; }
    public function getNombre() { return $this->nombre; }
    public function getDeporte() { return $this->deporte; }
    public function getDescripcion() { return $this->descripcion; }
    public function getObjetivo() { return $this->objetivo; }
    public function getNivel() { return $this->nivel; }
    public function getDuracionSemanas() { return $this->duracion_semanas; }
    public function getFrecuenciaSemanal() { return $this->frecuencia_semanal; }
    public function getFechaInicio() { return $this->fecha_inicio; }
    public function getFechaFin() { return $this->fecha_fin; }
    public function getEstado() { return $this->estado; }
    public function getFechaCreacion() { return $this->fecha_creacion; }
}
?>

