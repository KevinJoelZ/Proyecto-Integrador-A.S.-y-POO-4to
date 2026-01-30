<?php
/**
 * Clase Entrenador - Gestiona información de entrenadores
 * Implementa funcionalidades para crear, actualizar y obtener datos de entrenadores
 */
class Entrenador {
    private $id;
    private $nombre;
    private $email;
    private $especialidad; // deporte en el que se especializa
    private $experiencia_años;
    private $certificaciones;
    private $foto_perfil;
    private $disponible;
    private $calificacion;
    private $fecha_registro;
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    /**
     * Crear nuevo entrenador
     */
    public function crear() {
        $sql = "INSERT INTO entrenadores (nombre, email, especialidad, experiencia_años, certificaciones, foto_perfil, disponible, calificacion, fecha_registro) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) {
            return ['success' => false, 'message' => 'Error de preparación: ' . $this->conexion->error];
        }

        $disponible = $this->disponible ? 1 : 0;
        $stmt->bind_param("sssiisdi", $this->nombre, $this->email, $this->especialidad, 
                         $this->experiencia_años, $this->certificaciones, $this->foto_perfil, 
                         $disponible, $this->calificacion);

        if ($stmt->execute()) {
            $this->id = $this->conexion->insert_id;
            return ['success' => true, 'message' => 'Entrenador creado exitosamente', 'id' => $this->id];
        } else {
            return ['success' => false, 'message' => 'Error al crear entrenador: ' . $stmt->error];
        }
    }

    /**
     * Actualizar datos del entrenador
     */
    public function actualizar() {
        $sql = "UPDATE entrenadores SET nombre = ?, email = ?, especialidad = ?, experiencia_años = ?, 
                certificaciones = ?, foto_perfil = ?, disponible = ?, calificacion = ? WHERE id = ?";
        
        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) {
            return ['success' => false, 'message' => 'Error de preparación: ' . $this->conexion->error];
        }

        $disponible = $this->disponible ? 1 : 0;
        $stmt->bind_param("sssiisddi", $this->nombre, $this->email, $this->especialidad, 
                         $this->experiencia_años, $this->certificaciones, $this->foto_perfil, 
                         $disponible, $this->calificacion, $this->id);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Entrenador actualizado exitosamente'];
        } else {
            return ['success' => false, 'message' => 'Error al actualizar entrenador: ' . $stmt->error];
        }
    }

    /**
     * Obtener entrenador por ID
     */
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM entrenadores WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $entrenador = $resultado->fetch_assoc();
            $this->cargarDesdeArray($entrenador);
            return $entrenador;
        }
        return null;
    }

    /**
     * Obtener todos los entrenadores
     */
    public function obtenerTodos($filtro_especialidad = null) {
        $sql = "SELECT * FROM entrenadores";
        if ($filtro_especialidad) {
            $sql .= " WHERE especialidad = '" . $this->conexion->real_escape_string($filtro_especialidad) . "'";
        }
        $sql .= " ORDER BY calificacion DESC, fecha_registro DESC";
        
        $resultado = $this->conexion->query($sql);
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Obtener entrenadores disponibles
     */
    public function obtenerDisponibles() {
        $sql = "SELECT * FROM entrenadores WHERE disponible = 1 ORDER BY calificacion DESC";
        $resultado = $this->conexion->query($sql);
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Obtener entrenadores por especialidad
     */
    public function obtenerPorEspecialidad($especialidad) {
        return $this->obtenerTodos($especialidad);
    }

    /**
     * Eliminar entrenador
     */
    public function eliminar($id) {
        $sql = "DELETE FROM entrenadores WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Entrenador eliminado exitosamente'];
        } else {
            return ['success' => false, 'message' => 'Error al eliminar entrenador: ' . $stmt->error];
        }
    }

    /**
     * Cargar datos desde array
     */
    private function cargarDesdeArray($datos) {
        $this->id = $datos['id'] ?? null;
        $this->nombre = $datos['nombre'] ?? '';
        $this->email = $datos['email'] ?? '';
        $this->especialidad = $datos['especialidad'] ?? '';
        $this->experiencia_años = $datos['experiencia_años'] ?? 0;
        $this->certificaciones = $datos['certificaciones'] ?? '';
        $this->foto_perfil = $datos['foto_perfil'] ?? '';
        $this->disponible = $datos['disponible'] ?? true;
        $this->calificacion = $datos['calificacion'] ?? 5.0;
        $this->fecha_registro = $datos['fecha_registro'] ?? '';
    }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setNombre($nombre) { $this->nombre = $nombre; }
    public function setEmail($email) { $this->email = $email; }
    public function setEspecialidad($especialidad) { $this->especialidad = $especialidad; }
    public function setExperienciaAños($años) { $this->experiencia_años = $años; }
    public function setCertificaciones($cert) { $this->certificaciones = $cert; }
    public function setFotoPerfil($foto) { $this->foto_perfil = $foto; }
    public function setDisponible($disponible) { $this->disponible = $disponible; }
    public function setCalificacion($calif) { $this->calificacion = $calif; }

    // Getters
    public function getId() { return $this->id; }
    public function getNombre() { return $this->nombre; }
    public function getEmail() { return $this->email; }
    public function getEspecialidad() { return $this->especialidad; }
    public function getExperienciaAños() { return $this->experiencia_años; }
    public function getCertificaciones() { return $this->certificaciones; }
    public function getFotoPerfil() { return $this->foto_perfil; }
    public function getDisponible() { return $this->disponible; }
    public function getCalificacion() { return $this->calificacion; }
    public function getFechaRegistro() { return $this->fecha_registro; }
}
?>
