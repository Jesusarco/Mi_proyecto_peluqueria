-- Borrar BD si existe y crear nueva
DROP DATABASE IF EXISTS peluqueria_db;
CREATE DATABASE peluqueria_db;
USE peluqueria_db;

-- 1. Tabla usuarios (con borrado lógico)
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol ENUM('cliente', 'admin') DEFAULT 'cliente',  -- Cambiado user por cliente
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    activo BOOLEAN DEFAULT TRUE                     -- Borrado lógico
);

-- 2. Tabla servicios
CREATE TABLE servicios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL,
    duracion INT NOT NULL
);

-- 3. Tabla citas (con notas y sin ON DELETE CASCADE)
CREATE TABLE citas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    servicio_id INT NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    estado ENUM('reservado', 'cancelado', 'completado') DEFAULT 'reservado',
    notas TEXT,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    FOREIGN KEY (servicio_id) REFERENCES servicios(id) ON DELETE RESTRICT
);

-- 4. Tabla productos (con destacado)
CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL,
    stock INT DEFAULT 0,
    imagen VARCHAR(255),
    destacado BOOLEAN DEFAULT FALSE
);

-- 5. Tabla pedidos (sin CASCADE)
CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE RESTRICT
);

-- 6. Tabla detalle_pedido (sin CASCADE)
CREATE TABLE detalle_pedido (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE RESTRICT,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE RESTRICT
);

-- 7. Tabla gastos (mejorada)
CREATE TABLE gastos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    descripcion VARCHAR(255) NOT NULL,
    categoria VARCHAR(50),  -- Ej: 'Alquiler', 'Material', 'Sueldos', 'Publicidad', 'Otros'
    cantidad DECIMAL(10,2) NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    usuario_id INT NULL,     -- Administrador que registró el gasto
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
);

-- Índices para mejorar rendimiento
CREATE INDEX idx_citas_fecha ON citas(fecha);
CREATE INDEX idx_citas_usuario ON citas(usuario_id);
CREATE INDEX idx_citas_estado ON citas(estado);
CREATE INDEX idx_pedidos_usuario ON pedidos(usuario_id);
CREATE INDEX idx_pedidos_fecha ON pedidos(fecha);
CREATE INDEX idx_productos_nombre ON productos(nombre);
CREATE INDEX idx_productos_destacado ON productos(destacado);
CREATE INDEX idx_servicios_nombre ON servicios(nombre);
CREATE INDEX idx_gastos_fecha ON gastos(fecha);
CREATE INDEX idx_gastos_categoria ON gastos(categoria);