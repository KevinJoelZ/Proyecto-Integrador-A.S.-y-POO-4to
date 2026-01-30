-- =============================================================================
-- BASE DE DATOS: Gestión de Rutinas, Planes y Progresos - DeporteFit
-- =============================================================================
-- Tablas para gestionar usuarios, entrenadores, rutinas, ejercicios y progresos

-- ============== TABLA: USUARIOS ==============
-- Almacena información de usuarios del sistema
CREATE TABLE IF NOT EXISTS `usuarios` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `nombre` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `uid_firebase` VARCHAR(255) NOT NULL UNIQUE,
    `foto_perfil` VARCHAR(500),
    `deporte_favorito` VARCHAR(100),
    `nivel_experiencia` ENUM('principiante', 'intermedio', 'avanzado') DEFAULT 'principiante',
    `fecha_registro` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_uid_firebase` (`uid_firebase`),
    KEY `idx_email` (`email`),
    KEY `idx_deporte` (`deporte_favorito`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============== TABLA: ENTRENADORES ==============
-- Almacena información de entrenadores certificados
CREATE TABLE IF NOT EXISTS `entrenadores` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `nombre` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `especialidad` VARCHAR(100) NOT NULL,
    `experiencia_años` INT(11) DEFAULT 0,
    `certificaciones` TEXT,
    `foto_perfil` VARCHAR(500),
    `disponible` TINYINT(1) DEFAULT 1,
    `calificacion` DECIMAL(3, 2) DEFAULT 5.0,
    `fecha_registro` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_email` (`email`),
    KEY `idx_especialidad` (`especialidad`),
    KEY `idx_disponible` (`disponible`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============== TABLA: RUTINAS ==============
-- Almacena rutinas de entrenamiento asignadas a usuarios
CREATE TABLE IF NOT EXISTS `rutinas` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `usuario_id` INT(11) NOT NULL,
    `entrenador_id` INT(11),
    `nombre` VARCHAR(150) NOT NULL,
    `deporte` VARCHAR(100) NOT NULL,
    `descripcion` TEXT,
    `objetivo` VARCHAR(100) NOT NULL,
    `nivel` ENUM('principiante', 'intermedio', 'avanzado') DEFAULT 'intermedio',
    `duracion_semanas` INT(11) DEFAULT 4,
    `frecuencia_semanal` INT(11) DEFAULT 3,
    `fecha_inicio` DATE,
    `fecha_fin` DATE,
    `estado` ENUM('activa', 'completada', 'pausada') DEFAULT 'activa',
    `fecha_creacion` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_usuario_id` (`usuario_id`),
    KEY `idx_entrenador_id` (`entrenador_id`),
    KEY `idx_deporte` (`deporte`),
    KEY `idx_estado` (`estado`),
    CONSTRAINT `fk_rutinas_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_rutinas_entrenador` FOREIGN KEY (`entrenador_id`) REFERENCES `entrenadores`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============== TABLA: EJERCICIOS ==============
-- Almacena ejercicios individuales dentro de una rutina
CREATE TABLE IF NOT EXISTS `ejercicios` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `rutina_id` INT(11) NOT NULL,
    `nombre` VARCHAR(150) NOT NULL,
    `descripcion` TEXT,
    `series` INT(11),
    `repeticiones` INT(11),
    `duracion_segundos` INT(11),
    `descanso_segundos` INT(11),
    `dificultad` ENUM('baja', 'media', 'alta') DEFAULT 'media',
    `dia_semana` INT(11) COMMENT '0-6: Lunes a Domingo',
    `video_url` VARCHAR(500),
    `orden` INT(11) DEFAULT 0,
    `fecha_creacion` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_rutina_id` (`rutina_id`),
    KEY `idx_dia` (`dia_semana`),
    CONSTRAINT `fk_ejercicios_rutina` FOREIGN KEY (`rutina_id`) REFERENCES `rutinas`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============== TABLA: PROGRESOS ==============
-- Registra el progreso del usuario en sus entrenamientos
CREATE TABLE IF NOT EXISTS `progresos` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `usuario_id` INT(11) NOT NULL,
    `rutina_id` INT(11),
    `fecha_registro` DATE NOT NULL,
    `tipo_medida` VARCHAR(100) NOT NULL COMMENT 'peso, distancia, series, repeticiones, tiempo, velocidad, etc.',
    `valor_actual` DECIMAL(10, 2) NOT NULL,
    `valor_objetivo` DECIMAL(10, 2),
    `porcentaje_completado` DECIMAL(5, 2) DEFAULT 0,
    `notas` TEXT,
    `esfuerzo` INT(11) COMMENT '1-10: Escala de percepción de esfuerzo',
    `fecha_creacion` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_usuario_id` (`usuario_id`),
    KEY `idx_rutina_id` (`rutina_id`),
    KEY `idx_fecha_registro` (`fecha_registro`),
    KEY `idx_tipo_medida` (`tipo_medida`),
    CONSTRAINT `fk_progresos_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_progresos_rutina` FOREIGN KEY (`rutina_id`) REFERENCES `rutinas`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============== TABLA: RESULTADOS ==============
-- Almacena resultados resumidos y métricas de desempeño
CREATE TABLE IF NOT EXISTS `resultados` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `usuario_id` INT(11) NOT NULL,
    `rutina_id` INT(11),
    `tipo_metrica` VARCHAR(100) NOT NULL,
    `valor_inicio` DECIMAL(10, 2),
    `valor_actual` DECIMAL(10, 2),
    `mejora_porcentual` DECIMAL(5, 2),
    `fecha_inicio_medicion` DATE,
    `fecha_fin_medicion` DATE,
    `estado` ENUM('en_progreso', 'completado', 'no_iniciado') DEFAULT 'en_progreso',
    `fecha_creacion` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_usuario_id` (`usuario_id`),
    KEY `idx_rutina_id` (`rutina_id`),
    KEY `idx_tipo_metrica` (`tipo_metrica`),
    CONSTRAINT `fk_resultados_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_resultados_rutina` FOREIGN KEY (`rutina_id`) REFERENCES `rutinas`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============== TABLA: PLANES ==============
-- Almacena planes de entrenamiento predefinidos
CREATE TABLE IF NOT EXISTS `planes` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `nombre` VARCHAR(150) NOT NULL,
    `deporte` VARCHAR(100) NOT NULL,
    `descripcion` TEXT,
    `objetivo` VARCHAR(100) NOT NULL,
    `nivel` ENUM('principiante', 'intermedio', 'avanzado') DEFAULT 'intermedio',
    `duracion_semanas` INT(11) DEFAULT 4,
    `frecuencia_semanal` INT(11) DEFAULT 3,
    `precio` DECIMAL(10, 2),
    `activo` TINYINT(1) DEFAULT 1,
    `fecha_creacion` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_deporte` (`deporte`),
    KEY `idx_nivel` (`nivel`),
    KEY `idx_activo` (`activo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============== TABLA: SUSCRIPCIONES ==============
-- Gestiona suscripciones de usuarios a planes
CREATE TABLE IF NOT EXISTS `suscripciones` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `usuario_id` INT(11) NOT NULL,
    `plan_id` INT(11) NOT NULL,
    `fecha_inicio` DATE NOT NULL,
    `fecha_fin` DATE,
    `estado` ENUM('activa', 'cancelada', 'expirada') DEFAULT 'activa',
    `fecha_creacion` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_usuario_id` (`usuario_id`),
    KEY `idx_plan_id` (`plan_id`),
    KEY `idx_estado` (`estado`),
    CONSTRAINT `fk_suscripciones_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_suscripciones_plan` FOREIGN KEY (`plan_id`) REFERENCES `planes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============== DATOS DE EJEMPLO ==============
-- Datos de prueba para desarrollo

-- Insertar usuarios de ejemplo
INSERT INTO `usuarios` (`nombre`, `email`, `uid_firebase`, `deporte_favorito`, `nivel_experiencia`) VALUES
('Juan Pérez', 'juan@example.com', 'uid_juan_001', 'Fitness', 'intermedio'),
('María García', 'maria@example.com', 'uid_maria_001', 'Running', 'avanzado'),
('Carlos López', 'carlos@example.com', 'uid_carlos_001', 'Natación', 'principiante');

-- Insertar entrenadores de ejemplo
INSERT INTO `entrenadores` (`nombre`, `email`, `especialidad`, `experiencia_años`, `certificaciones`, `disponible`, `calificacion`) VALUES
('Luis Martínez', 'luis@trainers.com', 'Fitness', 8, 'ACE, IFBB', 1, 4.8),
('Ana Rodríguez', 'ana@trainers.com', 'Running', 6, 'IAAF, Marathon Coach', 1, 4.9),
('Roberto Sánchez', 'roberto@trainers.com', 'Natación', 10, 'FINA, Olímpico', 1, 5.0);

-- Insertar planes de ejemplo
INSERT INTO `planes` (`nombre`, `deporte`, `descripcion`, `objetivo`, `nivel`, `duracion_semanas`, `frecuencia_semanal`, `precio`) VALUES
('Plan Fitness Básico', 'Fitness', 'Programa básico para iniciar en el fitness', 'ganar fuerza', 'principiante', 8, 3, 50.00),
('Plan Running Avanzado', 'Running', 'Entrenamiento para maratón', 'mejorar resistencia', 'avanzado', 16, 5, 120.00),
('Plan Natación Intermedia', 'Natación', 'Técnica y resistencia en natación', 'mejorar técnica', 'intermedio', 12, 4, 80.00);

-- Insertar rutinas de ejemplo
INSERT INTO `rutinas` (`usuario_id`, `entrenador_id`, `nombre`, `deporte`, `descripcion`, `objetivo`, `nivel`, `duracion_semanas`, `frecuencia_semanal`, `fecha_inicio`, `estado`) VALUES
(1, 1, 'Rutina de Hipertrofia', 'Fitness', 'Enfocada en ganar masa muscular', 'ganar fuerza', 'intermedio', 12, 4, CURDATE(), 'activa'),
(2, 2, 'Preparación 10K', 'Running', 'Entrenamiento para carrera de 10 km', 'mejorar resistencia', 'intermedio', 8, 4, CURDATE(), 'activa'),
(3, 3, 'Técnica de Crawl', 'Natación', 'Mejora de técnica de natación estilo libre', 'mejorar técnica', 'principiante', 6, 3, CURDATE(), 'activa');

-- Insertar ejercicios de ejemplo
INSERT INTO `ejercicios` (`rutina_id`, `nombre`, `descripcion`, `series`, `repeticiones`, `dificultad`, `dia_semana`, `orden`) VALUES
(1, 'Sentadillas', 'Ejercicio de pierna con peso corporal o mancuernas', 4, 10, 'media', 0, 1),
(1, 'Press de Banca', 'Ejercicio de pecho horizontal con barra', 4, 8, 'media', 0, 2),
(1, 'Dominadas', 'Ejercicio de espalda y brazos', 3, 8, 'alta', 2, 1),
(2, 'Carrera de Resistencia', 'Carrera a ritmo constante', 1, NULL, 'media', 1, 1),
(2, 'Intervalos de Velocidad', 'Carrera con cambios de ritmo', 1, NULL, 'alta', 3, 1),
(3, 'Práctica de Crawl', 'Nado estilo libre a ritmo constante', 5, 50, 'media', 1, 1),
(3, 'Patadas con Tabla', 'Fortalecimiento de piernas en agua', 4, 25, 'baja', 3, 1);

-- Insertar progresos de ejemplo
INSERT INTO `progresos` (`usuario_id`, `rutina_id`, `fecha_registro`, `tipo_medida`, `valor_actual`, `valor_objetivo`, `esfuerzo`) VALUES
(1, 1, CURDATE(), 'peso', 75.5, 80.0, 8),
(1, 1, DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'peso', 75.0, 80.0, 7),
(2, 2, CURDATE(), 'distancia', 9.8, 10.0, 8),
(2, 2, DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'distancia', 9.5, 10.0, 7),
(3, 3, CURDATE(), 'tiempo', 12.5, 11.0, 6),
(3, 3, DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'tiempo', 13.0, 11.0, 7);

-- Verificar tablas creadas
-- SHOW TABLES;
-- SELECT * FROM usuarios;
-- SELECT * FROM rutinas;
-- SELECT * FROM progresos;
