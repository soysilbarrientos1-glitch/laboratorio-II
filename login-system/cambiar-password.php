<?php
session_start();
require_once 'includes/functions.php';

$error = '';
$success = '';
$token = $_GET['token'] ?? '';

if (empty($token)) {
    die('Token no proporcionado.');
}

// Verificar si el token es válido
$usuario = verificar_token_recuperacion($token);
if (!$usuario) {
    die('Token inválido o expirado.');
}

if ($_POST) {
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if ($password !== $confirm) {
        $error = 'Las contraseñas no coinciden.';
    } elseif (strlen($password) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres.';
    } else {
        if (cambiar_password_por_token($token, $password)) {
            $success = 'Contraseña actualizada. Puedes iniciar sesión.';
        } else {
            $error = 'Error al actualizar la contraseña.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Contraseña</title>
    <?php include 'includes/header.php'; ?>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <h1 class="text-2xl font-bold text-center text-gray-800 mb-6">🔄 Cambiar Contraseña</h1>

        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?= htmlspecialchars($success) ?>
            </div>
            <a href="index.php" class="block text-center w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded">
                Iniciar Sesión
            </a>
            <?php exit; ?>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-4">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Nueva Contraseña</label>
                <input type="password" id="password" name="password" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="mb-6">
                <label for="confirm_password" class="block text-gray-700 text-sm font-bold mb-2">Confirmar Contraseña</label>
                <input type="password" id="confirm_password" name="confirm_password" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit"
                    class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition duration-200">
                Cambiar Contraseña
            </button>
        </form>
    </div>
</body>
</html>