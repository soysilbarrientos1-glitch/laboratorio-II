<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
requireRole(['secretaria']); // Solo secretarias acceden

// Obtener citas
$stmt = $conn->prepare("SELECT c.id_cita, c.fecha, c.hora, c.estado, 
                               cli.nombre AS cliente, emp.nombre AS empleado, s.nombre AS servicio
                        FROM citas c
                        JOIN clientes cli ON c.id_cliente = cli.id_cliente
                        JOIN empleados emp ON c.id_empleado = emp.id_empleado
                        JOIN servicios s ON c.id_servicio = s.id_servicio
                        ORDER BY c.fecha, c.hora");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Secretaria</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { padding: 10px; border-bottom: 1px solid #ccc; }
        th { background-color: #f2f2f2; }
        .acciones form { display: inline; }
    </style>
</head>
<body>
    <h1>Panel de Secretaria</h1>
    <p>Bienvenida, <?php echo htmlspecialchars($_SESSION['nombre']); ?>.</p>
    <a href="dashboard.php">← Volver al panel</a>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Cliente</th>
                <th>Especialista</th>
                <th>Servicio</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($cita = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $cita['fecha']; ?></td>
                <td><?php echo $cita['hora']; ?></td>
                <td><?php echo htmlspecialchars($cita['cliente']); ?></td>
                <td><?php echo htmlspecialchars($cita['empleado']); ?></td>
                <td><?php echo htmlspecialchars($cita['servicio']); ?></td>
                <td><?php echo $cita['estado']; ?></td>
                <td class="acciones">
                    <form method="post" action="accion-estado.php">
                        <input type="hidden" name="id_cita" value="<?php echo $cita['id_cita']; ?>">
                        <select name="nuevo_estado">
                            <option value="pendiente">Pendiente</option>
                            <option value="confirmada">Confirmada</option>
                            <option value="completada">Completada</option>
                            <option value="cancelada">Cancelada</option>
                        </select>
                        <button type="submit">Actualizar</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html