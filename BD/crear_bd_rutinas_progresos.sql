-- Script para crear la base de datos y tablas para 'Mis Rutinas' y 'Mis Progresos'
-- Compatible con phpMyAdmin y MySQL

CREATE DATABASE IF NOT EXISTS pagina_deportiva2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pagina_deportiva2;

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(100) NOT NULL UNIQUE,
    contraseña VARCHAR(255) NOT NULL,
    rol VARCHAR(50) DEFAULT 'cliente'
);

-- Tabla de rutinas
CREATE TABLE IF NOT EXISTS rutinas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    nombre_rutina VARCHAR(100) NOT NULL,
    descripcion TEXT,
    fecha_creacion DATE DEFAULT CURRENT_DATE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabla de progresos
CREATE TABLE IF NOT EXISTS progresos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    rutina_id INT,
    fecha DATE NOT NULL,
    descripcion TEXT,
    avance VARCHAR(100),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (rutina_id) REFERENCES rutinas(id) ON DELETE SET NULL
);
