-- =====================================================
-- SCRIPT SQL COMPLETO - INSTALACIÓN DE BASE DE DATOS
-- Sistema Deportivo - XAMPP Local
-- =====================================================

SET SQL_MODE = '';
SET time_zone = '-05:00';

-- =====================================================
-- CREAR BASE DE DATOS
-- =====================================================
CREATE DATABASE IF NOT EXISTS pagina_deportiva2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pagina_deportiva2;

-- =====================================================
-- 1. TABLA USUARIOS
-- =====================================================

CREATE TABLE IF NOT EXISTS usuarios (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 2. TABLA CONTACTOS
-- =====================================================

CREATE TABLE IF NOT EXISTS contactos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    motivo ENUM('informacion', 'soporte', 'entrenadores', 'planes', 'servicios', 'otros') NOT NULL,
    mensaje TEXT NOT NULL,
    privacidad TINYINT(1) DEFAULT 0,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('pendiente', 'respondido', 'archivado') DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 3. TABLA ENTRENADORES
-- =====================================================

CREATE TABLE IF NOT EXISTS entrenadores (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 4. TABLA RUTINAS
-- =====================================================

CREATE TABLE IF NOT EXISTS rutinas (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 5. TABLA PROGRESOS
-- =====================================================

CREATE TABLE IF NOT EXISTS progresos (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 6. TABLA PLANES
-- =====================================================

CREATE TABLE IF NOT EXISTS planes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2),
    duracion_semanas INT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 7. TABLA EJERCICIOS
-- =====================================================

CREATE TABLE IF NOT EXISTS ejercicios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rutina_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    repeticiones INT,
    series INT,
    descanso INT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 8. TABLA RESULTADOS
-- =====================================================

CREATE TABLE IF NOT EXISTS resultados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    rutina_id INT,
    fecha DATE NOT NULL,
    resultado TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 9. TABLA SUSCRIPCIONES
-- =====================================================

CREATE TABLE IF NOT EXISTS suscripciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    plan_id INT NOT NULL,
    fecha_inicio DATE,
    fecha_fin DATE,
    estado VARCHAR(20) DEFAULT 'activa'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 10. TABLA SOLICITUDES ENTRENADORES
-- =====================================================

CREATE TABLE IF NOT EXISTS solicitudes_entrenadores (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 11. TABLA SOLICITUDES PLANES
-- =====================================================

CREATE TABLE IF NOT EXISTS solicitudes_planes (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 12. TABLA SOLICITUDES SERVICIOS
-- =====================================================

CREATE TABLE IF NOT EXISTS solicitudes_servicios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    motivo ENUM('informacion', 'soporte', 'servicios', 'otros') NOT NULL,
    mensaje TEXT NOT NULL,
    servicio VARCHAR(100),
    fecha_solicitud DATETIME DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('pendiente', 'respondido', 'archivado') DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 13. TABLA SOLICITUDES INFO
-- =====================================================

CREATE TABLE IF NOT EXISTS solicitudes_info (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    servicio VARCHAR(100) NOT NULL,
    plan VARCHAR(50),
    mensaje TEXT,
    fecha_solicitud DATETIME DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('pendiente', 'respondido', 'archivado') DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 14. TABLA FAQs
-- =====================================================

CREATE TABLE IF NOT EXISTS faqs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pregunta VARCHAR(255) NOT NULL,
    respuesta TEXT NOT NULL,
    page_slug VARCHAR(64) DEFAULT 'cliente',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 15. TABLA NOTICIAS
-- =====================================================

CREATE TABLE IF NOT EXISTS noticias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    contenido TEXT NOT NULL,
    categoria VARCHAR(50),
    imagen VARCHAR(255),
    autor VARCHAR(100),
    fecha_publicacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('borrador', 'publicado', 'archivado') DEFAULT 'borrador'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 16. TABLA PLANES SELECCIONADOS
-- =====================================================

CREATE TABLE IF NOT EXISTS planes_seleccionados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    plan VARCHAR(100) NOT NULL,
    price DECIMAL(10,2),
    user_email VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- DATOS DE EJEMPLO
-- =====================================================

-- Insertar usuario admin de prueba
INSERT INTO usuarios (nombre, email, contraseña, rol) VALUES 
('Administrador', 'admin@deportiva.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insertar usuario demo para pruebas locales (sin Firebase)
INSERT IGNORE INTO usuarios (nombre, email, rol) VALUES ('Usuario Demo', 'demo@deportiva.com', 'cliente');

-- Insertar planes de ejemplo
INSERT INTO planes (nombre, descripcion, precio, duracion_semanas) VALUES 
('Básico', 'Plan de entrenamiento básico con rutinas semanal', 29.99, 4),
('Estándar', 'Plan completo con seguimiento personalizado', 49.99, 8),
('Premium', 'Plan premium con coach personal y nutrición', 99.99, 12);

-- Insertar datos de ejemplo para probar formularios
INSERT INTO contactos (nombre, email, telefono, motivo, mensaje, privacidad, fecha_creacion, estado) VALUES 
('Usuario Ejemplo', 'ejemplo@email.com', '0991234567', 'informacion', 'Me gustaría obtener más información sobre los servicios de entrenamiento.', 1, NOW(), 'pendiente');

INSERT INTO solicitudes_entrenadores (nombre, email, telefono, motivo, mensaje, fecha_solicitud, estado) VALUES 
('Deportista Ejemplo', 'deportista@email.com', '0976543210', 'entrenadores', 'Quisiera contactar a un entrenador de fitness para comenzar mi entrenamiento', NOW(), 'pendiente');

INSERT INTO solicitudes_planes (nombre, email, telefono, motivo, mensaje, fecha_solicitud, estado) VALUES 
('Interesado Plan', 'plan@email.com', '0965432109', 'informacion', 'Necesito información detallada sobre los planes de entrenamiento disponibles', NOW(), 'pendiente');

INSERT INTO solicitudes_servicios (nombre, email, telefono, motivo, mensaje, fecha_solicitud, estado) VALUES 
('Cliente Servicio', 'servicio@email.com', '0954321098', 'informacion', 'Me interesa conocer más sobre los servicios de entrenamiento deportivo', NOW(), 'pendiente');

-- =====================================================
-- VERIFICACIÓN
-- =====================================================

SELECT 'Base de datos instalada correctamente' AS mensaje;
SHOW TABLES;
