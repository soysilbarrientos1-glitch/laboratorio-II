<?php
session_start();

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['rol'], ['manicurista', 'pedicurista'])) {
    header("Location: ../login.php");
    exit();
}

require_once '../includes/db.php';
$id_especialista = $_SESSION['user_id'];
$fecha_hoy = date('Y-m-d');
$mensaje = '';
$error = '';

// Procesar acción: marcar como completada
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['completar_cita'])) {
    $cita_id = filter_input(INPUT_POST, 'cita_id', FILTER_VALIDATE_INT);
    if ($cita_id) {
        $stmt = $conn->prepare("UPDATE citas SET estado = 'completada' WHERE id_cita = ? AND id_empleado = ?");
        $stmt->bind_param("ii", $cita_id, $id_especialista);
        if ($stmt->execute()) {
            $mensaje = "✅ Cita marcada como completada.";
        } else {
            $error = "❌ Error al actualizar la cita.";
        }
    }
}

// Procesar acción: cancelar cita
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancelar_cita'])) {
    $cita_id = filter_input(INPUT_POST, 'cita_id', FILTER_VALIDATE_INT);
    if ($cita_id) {
        $stmt = $conn->prepare("UPDATE citas SET estado = 'cancelada' WHERE id_cita = ? AND id_empleado = ?");
        $stmt->bind_param("ii", $cita_id, $id_especialista);
        if ($stmt->execute()) {
            $mensaje = "❌ Cita cancelada.";
            // Aquí iría la notificación al cliente (simulada)
            $mensaje .= " (Notificación enviada al cliente).";
        } else {
            $error = "❌ Error al cancelar la cita.";
        }
    }
}

// Consultar turnos de hoy (solo confirmadas)
$sql = "SELECT c.*, u.nombre AS nombre_cliente, s.nombre AS nombre_servicio 
        FROM citas c
        JOIN usuarios u ON c.id_cliente = u.id_usuario
        JOIN servicios s ON c.id_servicio = s.id_servicio
        WHERE c.id_empleado = ? AND c.fecha = ? AND c.estado = 'confirmada'
        ORDER BY c.hora";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $id_especialista, $fecha_hoy);
$stmt->execute();
$turnos_hoy = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Especialista</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .btn-completar {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 4px 8px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9em;
            margin-left: 8px;
        }
        .btn-cancelar {
            background: #f44336;
            color: white;
            border: none;
            padding: 4px 8px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9em;
            margin-left: 4px;
        }
        .btn-historial {
            display: inline-block;
            background: #2196F3;
            color: white;
            text-decoration: none;
            padding: 6px 12px;
            border-radius: 4px;
            margin-top: 10px;
            display: block;
            width: fit-content;
        }
        .mensaje { color: green; font-weight: bold; }
        .error { color: red; }
        .acciones { display: inline-block; margin-left: 10px; }
    </style>
</head>
<body>
    <h1>Hola, <?php echo htmlspecialchars($_SESSION['nombre']); ?>!</h1>
    <p>Eres <?php echo htmlspecialchars($_SESSION['rol']); ?></p>
    <a href="../logout.php">Cerrar Sesión</a>

    <?php if ($mensaje): ?>
        <p class="mensaje"><?php echo htmlspecialchars($mensaje); ?></p>
    <?php endif; ?>
    <?php if ($error): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <h2>Agenda de Hoy</h2>

    <?php if ($turnos_hoy->num_rows > 0): ?>
        <ul>
            <?php while ($cita = $turnos_hoy->fetch_assoc()): ?>
                <li>
                    <strong><?php echo htmlspecialchars($cita['nombre_cliente']); ?></strong> - 
                    <?php echo htmlspecialchars($cita['nombre_servicio']); ?> a las 
                    <?php echo htmlspecialchars($cita['hora']); ?>
                    <div class="acciones">
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="cita_id" value="<?php echo $cita['id_cita']; ?>">
                            <button type="submit" name="completar_cita" class="btn-completar">Completada</button>
                        </form>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="cita_id" value="<?php echo $cita['id_cita']; ?>">
                            <button type="submit" name="cancelar_cita" class="btn-cancelar"
                                    onclick="return confirm('¿Seguro que deseas cancelar esta cita?');">
                                Cancelar
                            </button>
                        </form>
                    </div>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>No tienes turnos asignados para hoy.</p>
    <?php endif; ?>

    <a href="historial-citas.php" class="btn-historial">Ver historial de citas</a>
</body>
</html>