<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
requireRole(['administrador']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios - Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
        .btn { padding: 4px 8px; margin: 2px; text-decoration: none; border-radius: 4px; }
        .btn-edit { background: #2196F3; color: white; }
        .btn-toggle { background: #4CAF50; color: white; }
        .btn-delete { background: #f44336; color: white; }
        .estado-inactivo { color: red; }
        .estado-activo { color: green; }
    </style>
</head>
<body>
    <h1>Gestión de Usuarios</h1>
    <p>Bienvenida, <?php echo htmlspecialchars($_SESSION['nombre']); ?>.</p>
    <a href="crear-usuario.php" class="btn btn-edit">+ Crear Nuevo Usuario</a>
    <a href="dashboard.php">← Volver al panel</a>

    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT u.id_usuario, u.nombre, u.email, u.activo, r.nombre_rol 
                    FROM usuarios u
                    JOIN rol r ON u.id_rol = r.id_rol
                    ORDER BY u.id_usuario DESC";
            $result = $conn->query($sql);
            while ($user = $result->fetch_assoc()):
            ?>
            <tr>
                <td><?php echo htmlspecialchars($user['nombre']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo htmlspecialchars($user['nombre_rol']); ?></td>
                <td class="<?php echo $user['activo'] ? 'estado-activo' : 'estado-inactivo'; ?>">
                    <?php echo $user['activo'] ? 'Activo' : 'Inactivo'; ?>
                </td>
                <td>
                    <a href="crear-usuario.php?id=<?php echo $user['id_usuario']; ?>" class="btn btn-edit">Editar</a>
                    <?php if ($user['id_usuario'] != $_SESSION['user_id']): ?>
                        <a href="accion-usuario.php?accion=<?php echo $user['activo'] ? 'desactivar' : 'activar'; ?>&id=<?php echo $user['id_usuario']; ?>" 
                           class="btn btn-toggle" 
                           onclick="return confirm('¿Confirmar?');">
                            <?php echo $user['activo'] ? 'Desactivar' : 'Activar'; ?>
                        </a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>