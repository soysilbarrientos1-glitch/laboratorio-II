<?php
session_start();

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['rol'], ['manicurista', 'pedicurista'])) {
    header("Location: ../login.php");
    exit();
}

require_once '../includes/db.php';
$id_especialista = $_SESSION['user_id'];

// Consultar historial (citas con fecha anterior a hoy, o estado != 'confirmada')
$sql = "SELECT c.*, u.nombre AS nombre_cliente, s.nombre AS nombre_servicio 
        FROM citas c
        JOIN usuarios u ON c.id_cliente = u.id_usuario
        JOIN servicios s ON c.id_servicio = s.id_servicio
        WHERE c.id_empleado = ?
        ORDER BY c.fecha DESC, c.hora DESC
        LIMIT 50";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_especialista);
$stmt->execute();
$historial = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Citas</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .estado-completada { color: green; }
        .estado-cancelada { color: red; }
        .estado-pendiente { color: orange; }
    </style>
</head>
<body>
    <h1>Historial de Citas</h1>
    <a href="perfil.php">← Volver al perfil</a>

    <?php if ($historial->num_rows > 0): ?>
        <table border="1" style="width:100%; border-collapse: collapse; margin-top: 1rem;">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Cliente</th>
                    <th>Servicio</th>
                    <th>Total</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($cita = $historial->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $cita['fecha']; ?></td>
                        <td><?php echo $cita['hora']; ?></td>
                        <td><?php echo htmlspecialchars($cita['nombre_cliente']); ?></td>
                        <td><?php echo htmlspecialchars($cita['nombre_servicio']); ?></td>
                        <td>$<?php echo number_format($cita['total'], 2); ?></td>
                        <td class="estado-<?php echo $cita['estado']; ?>">
                            <?php echo ucfirst($cita['estado']); ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No tienes citas en tu historial.</p>
    <?php endif; ?>
</body>
</html>