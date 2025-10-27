<?php
// Configuración de la conexión
$host = "localhost";
$user = "root"; // usuario de MySQL (por defecto es "root" en WAMP/XAMPP)
$password = ""; // contraseña de MySQL (por defecto es vacía en WAMP/XAMPP)
$dbname = "semis_marie"; // nombre de tu base de datos

// Crear conexión
$conn = new mysqli($host, $user, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Establecer codificación UTF-8
$conn->set_charset("utf8");
?>