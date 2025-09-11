<?php
session_start();
require_once 'includes/functions.php';

$error = '';
$success = '';

if ($_POST) {
    $email = trim($_POST['email']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email no válido.';
    } elseif (!email_existe($email)) {
        $error = 'Si este email está registrado, recibirás instrucciones.';
    } else {
        if (iniciar_recuperacion($email)) {
            $success = 'Hemos enviado un enlace a tu correo para restablecer tu contraseña.';
        } else {
            $error = 'Error al enviar el correo.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
    <?php include 'includes/header.php'; ?>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <h1 class="text-2xl font-bold text-center text-gray-800 mb-6">🔑 Recuperar Contraseña</h1>

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

        <p class="text-sm text-gray-600 mb-6 text-center">
            Ingresa tu email y te enviaremos un enlace para restablecer tu contraseña.
        </p>

        <form method="POST">
            <div class="mb-6">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                <input type="email" id="email" name="email" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-200">
                Enviar Enlace
            </button>
        </form>

        <p class="mt-4 text-center text-sm">
            <a href="index.php" class="text-blue-600 hover:underline">Volver al login</a>
        </p>
    </div>
</body>
</html>