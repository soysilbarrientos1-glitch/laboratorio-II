<?php
session_start();
require_once 'includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$usuario = obtener_usuario_por_id($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <?php include 'includes/header.php'; ?>
</head>
<body class="bg-gray-50">
    <?php include 'includes/navbar.php'; ?>

    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Hola, <?= htmlspecialchars($usuario['nombre']) ?> 👋</h1>
        <p class="text-lg text-gray-600">Bienvenido a tu panel personal.</p>

        <div class="mt-8 bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Información de cuenta</h2>
            <p><strong>Email:</strong> <?= htmlspecialchars($usuario['email']) ?></p>
            <p><strong>Rol:</strong> <span class="capitalize"><?= htmlspecialchars($usuario['rol']) ?></span></p>
            <p><strong>Último login:</strong> <?= $usuario['ultimo_login'] ?? 'Primera vez' ?></p>
        </div>

        <div class="mt-6 space-y-4">
            <a href="editar-perfil.php" class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded">
                Editar Perfil
            </a>
            <?php if ($_SESSION['rol'] === 'admin'): ?>
                <a href="admin/index.php" class="block w-full text-center bg-purple-600 hover:bg-purple-700 text-white py-2 px-4 rounded">
                    Panel de Administrador
                </a>
            <?php endif; ?>
            <a href="logout.php" class="block w-full text-center bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded">
                Cerrar Sesión
            </a>
        </div>
    </div>
</body>
</html>