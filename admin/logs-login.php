<?php
// Aquí deberías tener una tabla `logs_login` con: id, usuario_id, ip, fecha
// Este es un ejemplo básico
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logs de Login</title>
    <?php include '../includes/header.php'; ?>
</head>
<body class="bg-gray-100 flex">
    <?php include '../includes/sidebar.php'; ?>
    <div class="flex-1 p-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">📋 Historial de Inicios de Sesión</h1>
        <p class="text-gray-600">Funcionalidad en desarrollo. Aquí se mostrarían los logs de acceso.</p>
    </div>
</body>
</html>