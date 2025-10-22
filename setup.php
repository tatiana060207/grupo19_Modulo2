<?php
// Configuración de la base de datos
$servername = "localhost";
$username = "root";
$password = "";

// Crear conexión sin especificar base de datos
$conn = new mysqli($servername, $username, $password);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión falló: " . $conn->connect_error);
}

// Crear base de datos si no existe
$sql = "CREATE DATABASE IF NOT EXISTS eventos";
if ($conn->query($sql) === TRUE) {
    echo "Base de datos 'eventos' creada o ya existe.<br>";
} else {
    echo "Error creando base de datos: " . $conn->error . "<br>";
}

// Seleccionar la base de datos
$conn->select_db("eventos");

// Crear tabla eventos
$sql = "CREATE TABLE IF NOT EXISTS eventos (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    fechaIni DATE,
    horaIni TIME,
    fechaFin DATE,
    horaFin TIME,
    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Tabla 'eventos' creada o ya existe.<br>";
} else {
    echo "Error creando tabla eventos: " . $conn->error . "<br>";
}

// Crear tabla boletas
$sql = "CREATE TABLE IF NOT EXISTS boletas (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    evento_id INT(6) UNSIGNED,
    localidad VARCHAR(50) NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    cantidad_disponible INT(6) NOT NULL,
    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (evento_id) REFERENCES eventos(id)
)";

if ($conn->query($sql) === TRUE) {
    echo "Tabla 'boletas' creada o ya existe.<br>";
} else {
    echo "Error creando tabla boletas: " . $conn->error . "<br>";
}

// Crear tabla departamentos
$sql = "CREATE TABLE IF NOT EXISTS departamentos (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Tabla 'departamentos' creada o ya existe.<br>";
} else {
    echo "Error creando tabla departamentos: " . $conn->error . "<br>";
}

// Crear tabla municipios
$sql = "CREATE TABLE IF NOT EXISTS municipios (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    id_departamento INT(6) UNSIGNED,
    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_departamento) REFERENCES departamentos(id),
    UNIQUE KEY unique_municipio_depto (nombre, id_departamento)
)";

if ($conn->query($sql) === TRUE) {
    echo "Tabla 'municipios' creada o ya existe.<br>";
} else {
    echo "Error creando tabla municipios: " . $conn->error . "<br>";
}

// Actualizar tabla eventos para incluir ubicación
$sql = "ALTER TABLE eventos ADD COLUMN IF NOT EXISTS id_municipio INT(6) UNSIGNED AFTER horaFin,
        ADD FOREIGN KEY (id_municipio) REFERENCES municipios(id)";

if ($conn->query($sql) === TRUE) {
    echo "Tabla 'eventos' actualizada con ubicación.<br>";
} else {
    echo "Error actualizando tabla eventos: " . $conn->error . "<br>";
}

// Crear tabla artistas
$sql = "CREATE TABLE IF NOT EXISTS artistas (
    idArt INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nomArt VARCHAR(255) NOT NULL,
    apeArt VARCHAR(255) NOT NULL,
    genero VARCHAR(100) NOT NULL,
    ciudadOrig VARCHAR(100) NOT NULL,
    idEvento INT(6) UNSIGNED,
    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (idEvento) REFERENCES eventos(id)
)";

if ($conn->query($sql) === TRUE) {
    echo "Tabla 'artistas' creada o ya existe.<br>";
} else {
    echo "Error creando tabla artistas: " . $conn->error . "<br>";
}

$conn->close();
echo "Configuración completada.";
?>