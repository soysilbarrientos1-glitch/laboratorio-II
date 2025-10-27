<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
requireRole(['administrador']);

// Obtener especialistas para el filtro
$stmt = $conn->prepare("SELECT id_usuario, nombre FROM usuarios WHERE id_rol IN (2, 4) ORDER BY nombre");
$stmt->execute();
$especialistas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Procesar actualización de estado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar_estado'])) {
    $cita_id = filter_input(INPUT_POST, 'cita_id', FILTER_VALIDATE_INT);
    $nuevo_estado = $_POST['estado'] ?? '';
    if ($cita_id && in_array($nuevo_estado, ['confirmada', 'completada', 'cancelada'])) {
        $stmt = $conn->prepare("UPDATE citas SET estado = ? WHERE id_cita = ?");
        $stmt->bind_param("si", $nuevo_estado, $cita_id);
        $stmt->execute();
        $mensaje = "Estado actualizado correctamente.";
    }
}

// Construir consulta con filtros
$fecha_filtro = $_GET['fecha'] ?? '';
$especialista_filtro = filter_input(INPUT_GET, 'especialista', FILTER_VALIDATE_INT);

$sql = "SELECT c.id_cita, c.fecha, c.hora, c.total, c.estado,
               u_cliente.nombre AS cliente,
               u_especialista.nombre AS especialista,
               s.nombre AS servicio
        FROM citas c
        JOIN usuarios u_cliente ON c.id_cliente = u_cliente.id_usuario
        JOIN usuarios u_especialista ON c.id_empleado = u_especialista.id_usuario
        JOIN servicios s ON c.id_servicio = s.id_servicio
        WHERE 1=1";

$params = [];
$types = "";

if ($fecha_filtro) {
    $sql .= " AND c.fecha = ?";
    $params[] = $fecha_filtro;
    $types .= "s";
}

if ($especialista_filtro) {
    $sql .= " AND c.id_empleado = ?";
    $params[] = $especialista_filtro;
    $types .= "i";
}

$sql .= " ORDER BY c.fecha DESC, c.hora DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Turnos - Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .tabla-turnos { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        .tabla-turnos th, .tabla-turnos td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        .tabla-turnos th { background: #f5f5f5; }
        .btn { padding: 6px 10px; margin: 2px; text-decoration: none; border-radius: 4px; font-size: 0.9rem; }
        .btn-edit { background: #2196F3; color: white; }
        .btn-cancel { background: #f44336; color: white; }
        .estado-confirmada { color: #4CAF50; }
        .estado-completada { color: #2E7D32; }
        .estado-cancelada { color: #f44336; }
        .filtros { background: #fff; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; }
        .filtros label { display: inline-block; margin-right: 10px; font-weight: bold; }
        .filtros input, .filtros select { padding: 6px; margin-right: 10px; border: 1px solid #ccc; border-radius: 4px; }
        .mensaje { color: green; font-weight: bold; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>Gestión de Turnos</h1>
    <p>Bienvenida, <?php echo htmlspecialchars($_SESSION['nombre']); ?>.</p>
    <a href="dashboard.php">← Volver al panel</a>

    <?php if (!empty($mensaje)): ?>
        <p class="mensaje"><?php echo htmlspecialchars($mensaje); ?></p>
    <?php endif; ?>

    <!-- Filtros -->
    <div class="filtros">
        <form method="GET">
            <label for="fecha">Fecha:</label>
            <input type="date" name="fecha" id="fecha" value="<?php echo htmlspecialchars($fecha_filtro); ?>">

            <label for="especialista">Especialista:</label>
            <select name="especialista" id="especialista">
                <option value="">Todos</option>
                <?php foreach ($especialistas as $e): ?>
                    <option value="<?php echo $e['id_usuario']; ?>" 
                            <?php echo ($especialista_filtro == $e['id_usuario']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($e['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Filtrar</button>
            <a href="gestion-turnos.php" style="margin-left: 10px;">Limpiar</a>
        </form>
    </div>

    <!-- Tabla de turnos -->
    <?php if ($result->num_rows > 0): ?>
        <table class="tabla-turnos">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Cliente</th>
                    <th>Especialista</th>
                    <th>Servicio</th>
                    <th>Total</th>
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
                    <td><?php echo htmlspecialchars($cita['especialista']); ?></td>
                    <td><?php echo htmlspecialchars($cita['servicio']); ?></td>
                    <td>$<?php echo number_format($cita['total'], 2); ?></td>
                    <td class="estado-<?php echo $cita['estado']; ?>">
                        <?php echo ucfirst($cita['estado']); ?>
                    </td>
                    <td>
                        <!-- Formulario para cambiar estado -->
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="cita_id" value="<?php echo $cita['id_cita']; ?>">
                            <select name="estado" style="font-size:0.85rem; padding:2px;">
                                <option value="confirmada" <?php echo $cita['estado'] === 'confirmada' ? 'selected' : ''; ?>>Confirmada</option>
                                <option value="completada" <?php echo $cita['estado'] === 'completada' ? 'selected' : ''; ?>>Completada</option>
                                <option value="cancelada" <?php echo $cita['estado'] === 'cancelada' ? 'selected' : ''; ?>>Cancelada</option>
                            </select>
                            <button type="submit" name="actualizar_estado" class="btn btn-edit" style="padding:2px 6px;">Aplicar</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay turnos registrados.</p>
    <?php endif; ?>
</body>
</html>