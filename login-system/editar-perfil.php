<?php
session_start();
require_once 'includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$usuario = obtener_usuario_por_id($_SESSION['user_id']);
$error = '';
$success = '';

if ($_POST) {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $foto_actual = $usuario['foto_perfil'];

    // Validar email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email no válido.';
    } elseif ($email !== $usuario['email'] && email_existe($email)) {
        $error = 'Este email ya está en uso.';
    } else {
        $foto_perfil = $foto_actual;

        // Subir nueva foto si se envió
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
            $foto_perfil = subir_foto_perfil($_FILES['foto']);
            if (!$foto_perfil) {
                $error = 'Error al subir la foto.';
            } else {
                // Eliminar foto anterior si existe
                if ($foto_actual && file_exists("uploads/$foto_actual")) {
                    unlink("uploads/$foto_actual");
                }
            }
        }

        if (empty($error)) {
            if (editar_perfil($_SESSION['user_id'], $nombre, $email, $foto_perfil)) {
                $_SESSION['nombre'] = $nombre;
                $success = 'Perfil actualizado correctamente.';
                $usuario = obtener_usuario_por_id($_SESSION['user_id']); // Refrescar datos
            } else {
                $error = 'Error al guardar.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    <?php include 'includes/header.php'; ?>
</head>
<body class="bg-gray-50">
    <?php include 'includes/navbar.php'; ?>

    <div class="container mx-auto p-6 max-w-2xl">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">✏️ Editar Perfil</h1>

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

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-6">
                <label for="nombre" class="block text-gray-700 font-bold mb-2">Nombre</label>
                <input type="text" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md">
            </div>

            <div class="mb-6">
                <label for="email" class="block text-gray-700 font-bold mb-2">Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md">
            </div>

            <div class="mb-6">
                <label for="foto" class="block text-gray-700 font-bold mb-2">Foto de Perfil</label>
                <?php if ($usuario['foto_perfil']): ?>
                    <div class="mb-2">
                        <img src="uploads/<?= $usuario['foto_perfil'] ?>" alt="Foto" class="w-20 h-20 rounded-full object-cover">
                    </div>
                <?php endif; ?>
                <input type="file" name="foto" accept="image/*" class="w-full">
            </div>

            <div class="flex gap-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                    Guardar Cambios
                </button>
                <a href="dashboard.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</body>
</html>