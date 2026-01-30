<?php
/**
 * Clase Usuario - Gestiona la información de usuarios del sistema
 * Implementa funcionalidades para crear, actualizar y obtener datos de usuarios
 */
class Usuario {
    private $id;
    private $nombre;
    private $email;
    private $uid_firebase;
    private $foto_perfil;
    private $deporte_favorito;
    private $nivel_experiencia; // principiante, intermedio, avanzado
    private $fecha_registro;
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    /**
     * Crear o actualizar usuario en la base de datos
     */
    public function guardar() {
        // Verificar si el usuario ya existe
        $sql_verificar = "SELECT id FROM usuarios WHERE uid_firebase = ?";
        $stmt = $this->conexion->prepare($sql_verificar);
        $stmt->bind_param("s", $this->uid_firebase);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            // Actualizar usuario existente
            return $this->actualizar();
        } else {
            // Insertar nuevo usuario
            return $this->insertar();
        }
    }

    /**
     * Insertar nuevo usuario
     */
    private function insertar() {
        $sql = "INSERT INTO usuarios (nombre, email, uid_firebase, foto_perfil, deporte_favorito, nivel_experiencia, fecha_registro) 
                VALUES (?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) {
            return ['success' => false, 'message' => 'Error de preparación: ' . $this->conexion->error];
        }

        $stmt->bind_param("ssssss", $this->nombre, $this->email, $this->uid_firebase, 
                         $this->foto_perfil, $this->deporte_favorito, $this->nivel_experiencia);

        if ($stmt->execute()) {
            $this->id = $this->conexion->insert_id;
            return ['success' => true, 'message' => 'Usuario creado exitosamente', 'id' => $this->id];
        } else {
            return ['success' => false, 'message' => 'Error al crear usuario: ' . $stmt->error];
        }
    }

    /**
     * Actualizar usuario existente
     */
    private function actualizar() {
        $sql = "UPDATE usuarios SET nombre = ?, email = ?, foto_perfil = ?, deporte_favorito = ?, nivel_experiencia = ? 
                WHERE uid_firebase = ?";
        
        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) {
            return ['success' => false, 'message' => 'Error de preparación: ' . $this->conexion->error];
        }

        $stmt->bind_param("ssssss", $this->nombre, $this->email, $this->foto_perfil, 
                         $this->deporte_favorito, $this->nivel_experiencia, $this->uid_firebase);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Usuario actualizado exitosamente'];
        } else {
            return ['success' => false, 'message' => 'Error al actualizar usuario: ' . $stmt->error];
        }
    }

    /**
     * Obtener usuario por UID de Firebase
     */
    public function obtenerPorUid($uid) {
        $sql = "SELECT * FROM usuarios WHERE uid_firebase = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("s", $uid);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $usuario = $resultado->fetch_assoc();
            $this->cargarDesdeArray($usuario);
            return $usuario;
        }
        return null;
    }

    /**
     * Obtener usuario por ID
     */
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM usuarios WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            return $resultado->fetch_assoc();
        }
        return null;
    }

    /**
     * Obtener todos los usuarios
     */
    public function obtenerTodos() {
        $sql = "SELECT * FROM usuarios ORDER BY fecha_registro DESC";
        $resultado = $this->conexion->query($sql);
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Cargar datos desde array
     */
    private function cargarDesdeArray($datos) {
        $this->id = $datos['id'] ?? null;
        $this->nombre = $datos['nombre'] ?? '';
        $this->email = $datos['email'] ?? '';
        $this->uid_firebase = $datos['uid_firebase'] ?? '';
        $this->foto_perfil = $datos['foto_perfil'] ?? '';
        $this->deporte_favorito = $datos['deporte_favorito'] ?? '';
        $this->nivel_experiencia = $datos['nivel_experiencia'] ?? 'principiante';
        $this->fecha_registro = $datos['fecha_registro'] ?? '';
    }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setNombre($nombre) { $this->nombre = $nombre; }
    public function setEmail($email) { $this->email = $email; }
    public function setUidFirebase($uid) { $this->uid_firebase = $uid; }
    public function setFotoPerfil($foto) { $this->foto_perfil = $foto; }
    public function setDeporteFavorito($deporte) { $this->deporte_favorito = $deporte; }
    public function setNivelExperiencia($nivel) { $this->nivel_experiencia = $nivel; }

    // Getters
    public function getId() { return $this->id; }
    public function getNombre() { return $this->nombre; }
    public function getEmail() { return $this->email; }
    public function getUidFirebase() { return $this->uid_firebase; }
    public function getFotoPerfil() { return $this->foto_perfil; }
    public function getDeporteFavorito() { return $this->deporte_favorito; }
    public function getNivelExperiencia() { return $this->nivel_experiencia; }
    public function getFechaRegistro() { return $this->fecha_registro; }
}
?>
