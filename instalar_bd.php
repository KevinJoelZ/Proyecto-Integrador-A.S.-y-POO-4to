<?php
/**
 * Script de instalación de Base de Datos
 * Sistema Deportivo - XAMPP Local
 * 
 * Este script crea la base de datos y todas las tablas necesarias
 * para que el sistema funcione correctamente.
 * 
 * Uso: Ejecutar en el navegador: http://localhost/Pagina_deportiva2/instalar_bd.php
 */

// Configuración de la base de datos
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'pagina_deportiva2';

echo "<!DOCTYPE html>";
echo "<html><head>";
echo "<meta charset='UTF-8'>";
echo "<title>Instalador - Base de Datos</title>";
echo "<style>";
echo "body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }";
echo "h1 { color: #2c3e50; }";
echo ".success { color: #27ae60; background: #d5f4e6; padding: 10px; border-radius: 5px; margin: 10px 0; }";
echo ".error { color: #e74c3c; background: #fadbd8; padding: 10px; border-radius: 5px; margin: 10px 0; }";
echo ".info { color: #2980b9; background: #d4e6f1; padding: 10px; border-radius: 5px; margin: 10px 0; }";
echo "table { width: 100%; border-collapse: collapse; margin-top: 20px; }";
echo "th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }";
echo "th { background-color: #3498db; color: white; }";
echo ".btn { display: inline-block; padding: 10px 20px; background: #3498db; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; }";
echo "</style>";
echo "</head><body>";

echo "<h1>🔧 Instalador de Base de Datos</h1>";

// Conectar sin especificar base de datos
$conn = new mysqli($host, $user, $password);
if ($conn->connect_error) {
    echo "<p class='error'>❌ Error de conexión al servidor MySQL: " . $conn->connect_error . "</p>";
    echo "<p> Asegúrate de que XAMPP esté ejecutándose y que el servicio MySQL esté activo.</p>";
    exit;
}

echo "<p class='success'>✅ Conexión al servidor MySQL exitosa</p>";

// Crear base de datos
$sql = "CREATE DATABASE IF NOT EXISTS $database CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
if ($conn->query($sql) === TRUE) {
    echo "<p class='success'>✅ Base de datos '$database' creada o ya existe</p>";
} else {
    echo "<p class='error'>❌ Error al crear la base de datos: " . $conn->error . "</p>";
    exit;
}

// Seleccionar base de datos
$conn->select_db($database);
$conn->set_charset("utf8mb4");

echo "<p class='info'>📦 Creando tablas...</p>";

// =====================================================
// CREAR TABLAS
// =====================================================

$tablas = [];

// Tabla usuarios
$tablas[] = "CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    uid_firebase VARCHAR(255) UNIQUE,
    contraseña VARCHAR(255),
    foto_perfil TEXT,
    deporte_favorito VARCHAR(100),
    nivel_experiencia VARCHAR(50),
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    rol ENUM('admin', 'cliente', 'entrenador') DEFAULT 'cliente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// Tabla contactos
$tablas[] = "CREATE TABLE IF NOT EXISTS contactos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    motivo ENUM('informacion', 'soporte', 'entrenadores', 'planes', 'servicios', 'otros') NOT NULL,
    mensaje TEXT NOT NULL,
    privacidad TINYINT(1) DEFAULT 0,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('pendiente', 'respondido', 'archivado') DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// Tabla entrenadores
$tablas[] = "CREATE TABLE IF NOT EXISTS entrenadores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE,
    telefono VARCHAR(20),
    especialidad VARCHAR(100),
    experiencia_años INT DEFAULT 0,
    certificaciones TEXT,
    foto_perfil TEXT,
    disponible TINYINT(1) DEFAULT 1,
    calificacion DECIMAL(3,2) DEFAULT 5.00,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// Tabla rutinas
$tablas[] = "CREATE TABLE IF NOT EXISTS rutinas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    entrenador_id INT,
    nombre VARCHAR(100) NOT NULL,
    deporte VARCHAR(50) NOT NULL,
    descripcion TEXT,
    objetivo VARCHAR(100),
    nivel VARCHAR(50),
    duracion_semanas INT,
    frecuencia_semanal INT,
    fecha_inicio DATE,
    fecha_fin DATE,
    estado VARCHAR(20) DEFAULT 'activa',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// Tabla progresos
$tablas[] = "CREATE TABLE IF NOT EXISTS progresos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    rutina_id INT,
    fecha DATE NOT NULL,
    tipo_medida VARCHAR(50),
    valor_actual FLOAT,
    valor_objetivo FLOAT,
    esfuerzo INT,
    descripcion TEXT,
    porcentaje_completado FLOAT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// Tabla planes
$tablas[] = "CREATE TABLE IF NOT EXISTS planes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2),
    duracion_semanas INT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// Tabla ejercicios
$tablas[] = "CREATE TABLE IF NOT EXISTS ejercicios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rutina_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    repeticiones INT,
    series INT,
    descanso INT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// Tabla solicitudes_entrenadores
$tablas[] = "CREATE TABLE IF NOT EXISTS solicitudes_entrenadores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    motivo ENUM('informacion', 'soporte', 'entrenadores', 'otros') NOT NULL,
    mensaje TEXT NOT NULL,
    entrenador_id INT,
    entrenador_nombre VARCHAR(100),
    especialidad_interes VARCHAR(100),
    fecha_solicitud DATETIME DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('pendiente', 'respondido', 'archivado') DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// Tabla solicitudes_planes
$tablas[] = "CREATE TABLE IF NOT EXISTS solicitudes_planes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    motivo ENUM('informacion', 'soporte', 'planes', 'otros') NOT NULL,
    mensaje TEXT NOT NULL,
    plan_id INT,
    plan_nombre VARCHAR(100),
    fecha_solicitud DATETIME DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('pendiente', 'respondido', 'archivado') DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// Tabla solicitudes_servicios
$tablas[] = "CREATE TABLE IF NOT EXISTS solicitudes_servicios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    motivo ENUM('informacion', 'soporte', 'servicios', 'otros') NOT NULL,
    mensaje TEXT NOT NULL,
    servicio VARCHAR(100),
    fecha_solicitud DATETIME DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('pendiente', 'respondido', 'archivado') DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// Tabla solicitudes_info
$tablas[] = "CREATE TABLE IF NOT EXISTS solicitudes_info (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    servicio VARCHAR(100) NOT NULL,
    plan VARCHAR(50),
    mensaje TEXT,
    fecha_solicitud DATETIME DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('pendiente', 'respondido', 'archivado') DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// Tabla faqs
$tablas[] = "CREATE TABLE IF NOT EXISTS faqs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pregunta VARCHAR(255) NOT NULL,
    respuesta TEXT NOT NULL,
    page_slug VARCHAR(64) DEFAULT 'cliente',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// Tabla noticias
$tablas[] = "CREATE TABLE IF NOT EXISTS noticias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    contenido TEXT NOT NULL,
    categoria VARCHAR(50),
    imagen VARCHAR(255),
    autor VARCHAR(100),
    fecha_publicacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('borrador', 'publicado', 'archivado') DEFAULT 'borrador'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// Tabla planes_seleccionados
$tablas[] = "CREATE TABLE IF NOT EXISTS planes_seleccionados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    plan VARCHAR(100) NOT NULL,
    price DECIMAL(10,2),
    user_email VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// Ejecutar creación de tablas
$tablasCreadas = 0;
foreach ($tablas as $sql) {
    if ($conn->query($sql) === TRUE) {
        $tablasCreadas++;
    } else {
        echo "<p class='error'>❌ Error al crear tabla: " . $conn->error . "</p>";
    }
}

echo "<p class='success'>✅ Se crearon $tablasCreadas tablas correctamente</p>";

// Insertar datos de ejemplo
echo "<p class='info'>📝 Insertando datos de ejemplo...</p>";

// Insertar usuario demo para pruebas locales (sin Firebase)
$conn->query("INSERT IGNORE INTO usuarios (nombre, email, rol) VALUES ('Usuario Demo', 'demo@deportiva.com', 'cliente')");

// Verificar si ya existen datos
$result = $conn->query("SELECT COUNT(*) as total FROM contactos");
$row = $result->fetch_assoc();
if ($row['total'] == 0) {
    // Insertar datos de ejemplo
    $conn->query("INSERT INTO contactos (nombre, email, telefono, motivo, mensaje, privacidad, fecha_creacion, estado) VALUES 
        ('Usuario Ejemplo', 'ejemplo@email.com', '0991234567', 'informacion', 'Me gustaría obtener más información sobre los servicios de entrenamiento.', 1, NOW(), 'pendiente')");
    
    $conn->query("INSERT INTO solicitudes_entrenadores (nombre, email, telefono, motivo, mensaje, fecha_solicitud, estado) VALUES 
        ('Deportista Ejemplo', 'deportista@email.com', '0976543210', 'entrenadores', 'Quisiera contactar a un entrenador de fitness para comenzar mi entrenamiento', NOW(), 'pendiente')");
    
    $conn->query("INSERT INTO solicitudes_planes (nombre, email, telefono, motivo, mensaje, fecha_solicitud, estado) VALUES 
        ('Interesado Plan', 'plan@email.com', '0965432109', 'informacion', 'Necesito información detallada sobre los planes de entrenamiento disponibles', NOW(), 'pendiente')");
    
    $conn->query("INSERT INTO solicitudes_servicios (nombre, email, telefono, motivo, mensaje, fecha_solicitud, estado) VALUES 
        ('Cliente Servicio', 'servicio@email.com', '0954321098', 'informacion', 'Me interesa conocer más sobre los servicios de entrenamiento deportivo', NOW(), 'pendiente')");
    
    $conn->query("INSERT INTO planes (nombre, descripcion, precio, duracion_semanas) VALUES 
        ('Básico', 'Plan de entrenamiento básico con rutinas semanal', 29.99, 4),
        ('Estándar', 'Plan completo con seguimiento personalizado', 49.99, 8),
        ('Premium', 'Plan premium con coach personal y nutrición', 99.99, 12)");
    
    echo "<p class='success'>✅ Datos de ejemplo insertados</p>";
} else {
    echo "<p class='info'>ℹ️ Los datos de ejemplo ya existen</p>";
}

// Listar tablas creadas
echo "<h2>📋 Tablas en la base de datos:</h2>";
echo "<table><tr><th>Tabla</th><th>Registros</th></tr>";
$result = $conn->query("SHOW TABLES");
while ($row = $result->fetch_array(MYSQLI_NUM)) {
    $tabla = $row[0];
    $count = $conn->query("SELECT COUNT(*) as total FROM $tabla")->fetch_assoc()['total'];
    echo "<tr><td>$tabla</td><td>$count</td></tr>";
}
echo "</table>";

echo "<p class='success'>🎉 ¡Instalación completada exitosamente!</p>";
echo "<p>Ahora puedes:</p>";
echo "<ul>";
echo "<li>Ir a <a href='test_db.php'>Probar la conexión</a></li>";
echo "<li>Probar los formularios de contacto, planes, servicios y entrenadores</li>";
echo "<li>Ver las solicitudes en el panel de administración</li>";
echo "</ul>";

echo "<a href='index.php' class='btn'>🏠 Ir a la página principal</a>";

$conn->close();
echo "</body></html>";
?>
