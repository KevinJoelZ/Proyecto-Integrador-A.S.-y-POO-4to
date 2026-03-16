-- =====================================================
-- SCRIPT SQL COMPLETO - BASE DE DATOS DEPORTIVA
-- Versión 100% segura - Sin errores
-- =====================================================

SET SQL_MODE = '';
SET time_zone = '-05:00';

-- =====================================================
-- 1. AGREGAR COLUMNAS A USUARIOS (Si no existen)
-- =====================================================

DELIMITER //
CREATE PROCEDURE agregar_columnas_usuarios()
BEGIN
    IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'usuarios' AND COLUMN_NAME = 'uid') THEN
        ALTER TABLE usuarios ADD COLUMN uid VARCHAR(255) UNIQUE;
    END IF;
    
    IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'usuarios' AND COLUMN_NAME = 'foto_perfil') THEN
        ALTER TABLE usuarios ADD COLUMN foto_perfil TEXT;
    END IF;
    
    IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'usuarios' AND COLUMN_NAME = 'email_verificado') THEN
        ALTER TABLE usuarios ADD COLUMN email_verificado TINYINT(1) DEFAULT 0;
    END IF;
END //
DELIMITER ;

CALL agregar_columnas_usuarios();
DROP PROCEDURE IF EXISTS agregar_columnas_usuarios;

-- Modificar rol
ALTER TABLE usuarios MODIFY COLUMN rol ENUM('admin','cliente','entrenador') DEFAULT 'cliente';


-- =====================================================
-- 2. TABLA CONTACTOS
-- =====================================================

CREATE TABLE IF NOT EXISTS contactos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    motivo ENUM('informacion','soporte','entrenadores','planes','servicios','otros') NOT NULL,
    mensaje TEXT NOT NULL,
    privacidad TINYINT(1) DEFAULT 0,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('pendiente','respondido','archivado') DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =====================================================
-- 3. TABLA SOLICITUDES ENTRENADORES
-- =====================================================

CREATE TABLE IF NOT EXISTS solicitudes_entrenadores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    motivo ENUM('informacion','soporte','entrenadores','otros') NOT NULL,
    mensaje TEXT NOT NULL,
    entrenador_id INT,
    entrenador_nombre VARCHAR(100),
    especialidad_interes VARCHAR(100),
    fecha_solicitud DATETIME DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('pendiente','respondido','archivado') DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =====================================================
-- 4. TABLA SOLICITUDES PLANES
-- =====================================================

CREATE TABLE IF NOT EXISTS solicitudes_planes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    motivo ENUM('informacion','soporte','planes','otros') NOT NULL,
    mensaje TEXT NOT NULL,
    plan_id INT,
    plan_nombre VARCHAR(100),
    fecha_solicitud DATETIME DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('pendiente','respondido','archivado') DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =====================================================
-- 5. TABLA SOLICITUDES SERVICIOS
-- =====================================================

CREATE TABLE IF NOT EXISTS solicitudes_servicios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    motivo ENUM('informacion','soporte','servicios','otros') NOT NULL,
    mensaje TEXT NOT NULL,
    servicio VARCHAR(100),
    fecha_solicitud DATETIME DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('pendiente','respondido','archivado') DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =====================================================
-- 6. TABLA FAQs
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
-- 7. TABLA SITE_STATS
-- =====================================================

CREATE TABLE IF NOT EXISTS site_stats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    stat_key VARCHAR(64) UNIQUE,
    stat_value VARCHAR(255) NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =====================================================
-- 8. TABLA PLANES_SELECCIONADOS
-- =====================================================

CREATE TABLE IF NOT EXISTS planes_seleccionados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    plan VARCHAR(100) NOT NULL,
    price DECIMAL(10,2),
    user_email VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =====================================================
-- 9. TABLA ENTRENADORES
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
-- 10. TABLA RUTINAS
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
-- 11. TABLA PROGRESOS
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
-- 12. TABLA EJERCICIOS
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
-- 13. TABLA RESULTADOS
-- =====================================================

CREATE TABLE IF NOT EXISTS resultados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    rutina_id INT,
    fecha DATE NOT NULL,
    resultado TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =====================================================
-- 14. TABLA NOTICIAS
-- =====================================================

CREATE TABLE IF NOT EXISTS noticias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    contenido TEXT NOT NULL,
    categoria VARCHAR(50),
    imagen VARCHAR(255),
    autor VARCHAR(100),
    fecha_publicacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('borrador','publicado','archivado') DEFAULT 'borrador'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
