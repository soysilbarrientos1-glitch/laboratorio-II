<?php
include 'includes/db.php'; // Este archivo debe usar mysqli y definir $conn
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit();
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    header("Location: login.php?error=required");
    exit();
}

// Preparar la consulta para evitar inyección SQL
$stmt = $conn->prepare("SELECT id_usuario, nombre, email, password, id_rol FROM usuarios WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user && password_verify($password, $user['password'])) {
    // Obtener el nombre del rol desde la tabla roles
    $stmtRol = $conn->prepare("SELECT nombre_rol FROM rol WHERE id_rol = ?");
    $stmtRol->bind_param("i", $user['id_rol']);
    $stmtRol->execute();
    $rolData = $stmtRol->get_result()->fetch_assoc();
    
    $rol = $rolData ? $rolData['nombre_rol'] : 'cliente'; // Por defecto 'cliente'

    // Guardar datos en sesión
    $_SESSION['user_id'] = $user['id_usuario'];
    $_SESSION['nombre'] = $user['nombre'];
    $_SESSION['rol'] = $rol;

    // Redirigir según el rol
    switch ($rol) {
        case 'administrador':
            header("Location: admin/dashboard.php");
            break;
        case 'manicurista':
        case 'pedicurista':
            header("Location: especialista/perfil.php");
            break;
        case 'cliente':
        default:
            header("Location: cliente/servicios.php");
    }
    exit();

} else {
    header("Location: login.php?error=invalid");
    exit();
}

$stmt->close();
$conn->close();
?>