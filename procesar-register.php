<?php
// Incluir la conexión a la base de datos
include 'includes/db.php'; // Este archivo debe contener tu conexión $conn

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: register.php");
    exit();
}

$nombre = trim($_POST['nombre'] ?? '');
$email = trim($_POST['email'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$password = $_POST['password'] ?? '';

// Validaciones
if (empty($nombre) || empty($email) || empty($password)) {
    header("Location: register.php?error=Todos los campos son obligatorios.");
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: register.php?error=El correo no es válido.");
    exit();
}

// Verificar si el correo ya existe
$stmt = $conn->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    header("Location: register.php?error=Este correo ya está registrado.");
    exit();
}

// Cifrar la contraseña
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

// ID del rol "cliente" (según tu tabla roles)
$id_rol_cliente = 2;

// Insertar nuevo usuario
$stmt = $conn->prepare("
    INSERT INTO usuarios (nombre, email, telefono, password, id_rol, activo, fecha_registro)
    VALUES (?, ?, ?, ?, ?, 1, NOW())
");
$stmt->bind_param("sssss", $nombre, $email, $telefono, $passwordHash, $id_rol_cliente);

if ($stmt->execute()) {
    header("Location: login.php?registro=exitoso");
} else {
    error_log("Error en registro: " . $stmt->error);
    header("Location: register.php?error=Error al registrar. Inténtalo más tarde.");
}

$stmt->close();
$conn->close();
exit();
?>