<?php
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
    <title>Panel de Admin</title>
    <?php include '../includes/header.php'; ?>
</head>
<body class="bg-gray-100 flex">
    <?php include '../includes/sidebar.php'; ?>
    <div class="flex-1 p-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-4">Panel de Administración</h1>
        <p class="text-gray-600">Bienvenido, <?= htmlspecialchars($_SESSION['nombre']) ?> 👨‍💼</p>
    </div>
</body>
</html>