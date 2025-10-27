<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
requireRole(['administrador']);

$error = '';
$usuario = null;

// Si se pasa un ID, cargar usuario para editar
if (isset($_GET['id'])) {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($id) {
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $usuario = $stmt->get_result()->fetch_assoc();
    }
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $password = $_POST['password'] ?? '';
    $id_rol = filter_input(INPUT_POST, 'id_rol', FILTER_VALIDATE_INT);
    $activo = isset($_POST['activo']);

    if (empty($nombre) || empty($email) || !$id_rol) {
        $error = "Nombre, email y rol son obligatorios.";
    } else {
        // Verificar email único (solo si es nuevo o cambia)
        $stmt = $conn->prepare("SELECT id_usuario FROM usuarios WHERE email = ? AND id_usuario != ?");
        $check_id = $id ?: 0;
        $stmt->bind_param("si", $email, $check_id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $error = "Este email ya está en uso.";
        } else {
            if ($id) {
                // Actualizar
                if (!empty($password)) {
                    $hashed = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("UPDATE usuarios SET nombre=?, apellido=?, email=?, telefono=?, password=?, id_rol=?, activo=? WHERE id_usuario=?");
                    $stmt->bind_param("sssssi", $nombre, $apellido, $email, $telefono, $hashed, $id_rol, $activo, $id);
                } else {
                    $stmt = $conn->prepare("UPDATE usuarios SET nombre=?, apellido=?, email=?, telefono=?, id_rol=?, activo=? WHERE id_usuario=?");
                    $stmt->bind_param("ssssii", $nombre, $apellido, $email, $telefono, $id_rol, $activo, $id);
                }
            } else {
                // Crear nuevo
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO usuarios (nombre, apellido, email, telefono, password, id_rol, activo, fecha_registro) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
                $stmt->bind_param("ssssiii", $nombre, $apellido, $email, $telefono, $hashed, $id_rol, $activo);
            }
            if ($stmt->execute()) {
                header("Location: gestion-usuarios.php?exito=1");
                exit();
            } else {
                $error = "Error al guardar el usuario.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo $usuario ? 'Editar Usuario' : 'Crear Usuario'; ?> - Admin</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <h1><?php echo $usuario ? 'Editar Usuario' : 'Crear Nuevo Usuario'; ?></h1>
    <a href="gestion-usuarios.php">← Volver</a>

    <?php if ($error): ?>
        <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="id" value="<?php echo $usuario['id_usuario'] ?? ''; ?>">

        <label>Nombre:</label>
        <input type="text" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre'] ?? ''); ?>" required><br><br>

        <label>Apellido:</label>
        <input type="text" name="apellido" value="<?php echo htmlspecialchars($usuario['apellido'] ?? ''); ?>"><br><br>

        <label>Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($usuario['email'] ?? ''); ?>" required><br><br>

        <label>Teléfono:</label>
        <input type="text" name="telefono" value="<?php echo htmlspecialchars($usuario['telefono'] ?? ''); ?>"><br><br>

        <label>Contraseña:</label>
        <input type="password" name="password" <?php echo $usuario ? '' : 'required'; ?> 
               placeholder="<?php echo $usuario ? 'Dejar vacío para no cambiar' : 'Requerida'; ?>"><br><br>

        <label>Rol:</label>
        <select name="id_rol" required>
            <option value="1" <?php echo ($usuario['id_rol'] ?? 0) == 1 ? 'selected' : ''; ?>>Cliente</option>
            <option value="2" <?php echo ($usuario['id_rol'] ?? 0) == 2 ? 'selected' : ''; ?>>Manicurista</option>
            <option value="4" <?php echo ($usuario['id_rol'] ?? 0) == 4 ? 'selected' : ''; ?>>Pedicurista</option>
            <option value="5" <?php echo ($usuario['id_rol'] ?? 0) == 5 ? 'selected' : ''; ?>>Secretaria</option>
            <option value="3" <?php echo ($usuario['id_rol'] ?? 0) == 3 ? 'selected' : ''; ?>>Administrador</option>
        </select><br><br>

        <label>
            <input type="checkbox" name="activo" <?php echo !isset($usuario) || ($usuario['activo'] ?? false) ? 'checked' : ''; ?>>
            Cuenta activa
        </label><br><br>

        <button type="submit"><?php echo $usuario ? 'Actualizar' : 'Crear Usuario'; ?></button>
    </form>
</body>
</html>