<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
requireRole(['administrador']); // Solo admins pueden ver los logs

// Obtener los logs
$stmt = $conn->prepare("SELECT id_turno, usuario, accion, fecha FROM logs_turnos ORDER BY fecha DESC");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Logs - Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .tabla-logs { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        .tabla-logs th, .tabla-logs td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        .tabla-logs th { background: #f5f5f5; }
        .volver { margin-top: 1rem; display: inline-block; }
    </style>
</head>
<body>
    <h1>Historial de Logs</h1>
    <p>Bienvenida, <?php echo htmlspecialchars($_SESSION['nombre']); ?>.</p>
    <a href="dashboard.php" class="volver">← Volver al panel</a>

    <?php if ($result->num_rows > 0): ?>
        <table class="tabla-logs">
            <thead>
                <tr>
                    <th>ID Turno</th>
                    <th>Usuario</th>
                    <th>Acción</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($log = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $log['id_turno']; ?></td>
                    <td><?php echo htmlspecialchars($log['usuario']); ?></td>
                    <td><?php echo htmlspecialchars($log['accion']); ?></td>
                    <td><?php echo $log['fecha']; ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay registros en el historial.</p>
    <?php endif; ?>
</body>
</html>
