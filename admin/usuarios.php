<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}
require_once '../includes/functions.php';
$usuarios = obtener_todos_usuarios();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios - Admin</title>
    <?php include '../includes/header.php'; ?>
</head>
<body class="bg-gray-100 flex">
    <?php include '../includes/sidebar.php'; ?>
    <div class="flex-1 p-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">👥 Gestión de Usuarios</h1>
        <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow">
            <thead class="bg-gray-200">
                <tr>
                    <th class="py-2 px-4 border-b">ID</th>
                    <th class="py-2 px-4 border-b">Nombre</th>
                    <th class="py-2 px-4 border-b">Email</th>
                    <th class="py-2 px-4 border-b">Rol</th>
                    <th class="py-2 px-4 border-b">Creado</th>
                    <th class="py-2 px-4 border-b">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $u): ?>
                <tr class="hover:bg-gray-50">
                    <td class="py-2 px-4 border-b"><?= $u['id'] ?></td>
                    <td class="py-2 px-4 border-b"><?= htmlspecialchars($u['nombre']) ?></td>
                    <td class="py-2 px-4 border-b"><?= htmlspecialchars($u['email']) ?></td>
                    <td class="py-2 px-4 border-b capitalize"><?= $u['rol'] ?></td>
                    <td class="py-2 px-4 border-b"><?= $u['creado_en'] ?></td>
                    <td class="py-2 px-4 border-b space-x-2">
                        <a href="editar-usuario.php?id=<?= $u['id'] ?>" class="text-blue-600 hover:underline">Editar</a>
                        <a href="eliminar-usuario.php?id=<?= $u['id'] ?>" class="text-red-600 hover:underline" onclick="return confirm('¿Eliminar?')">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>