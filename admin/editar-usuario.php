<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}
require_once '../includes/functions.php';

$id = $_GET['id'] ?? null;
$usuario = obtener_usuario_por_id($id);

if (!$usuario) {
    die('Usuario no encontrado.');
}

$error = '';
$success = '';

if ($_POST) {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $rol = $_POST['rol'] === 'admin' ? 'admin' : 'usuario';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email no válido.';
    } elseif ($email !== $usuario['email'] && email_existe($email)) {
        $error = 'Email ya en uso.';
    } else {
        $stmt = $pdo->prepare("UPDATE usuarios SET nombre = ?, email = ?, rol = ? WHERE id = ?");
        if ($stmt->execute([$nombre, $email, $rol, $id])) {
            $success = 'Usuario actualizado.';
            $usuario = obtener_usuario_por_id($id);
        } else {
            $error = 'Error al actualizar.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
    <?php include '../includes/header.php'; ?>
</head>
<body class="bg-gray-100 flex">
    <?php include '../includes/sidebar.php'; ?>
    <div class="flex-1 p-6 max-w-2xl">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">✏️ Editar Usuario</h1>

        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-4">
                <label>Nombre</label>
                <input type="text" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" class="w-full px-3 py-2 border rounded">
            </div>
            <div class="mb-4">
                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" class="w-full px-3 py-2 border rounded">
            </div>
            <div class="mb-6">
                <label>Rol</label>
                <select name="rol" class="w-full px-3 py-2 border rounded">
                    <option value="usuario" <?= $usuario['rol'] === 'usuario' ? 'selected' : '' ?>>Usuario</option>
                    <option value="admin" <?= $usuario['rol'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                </select>
            </div>
            <div class="flex gap-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-6 rounded">Guardar</button>
                <a href="usuarios.php" class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-6 rounded">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>