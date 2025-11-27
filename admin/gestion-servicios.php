<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
requireRole(['administrador']); // Solo admins pueden gestionar servicios
include '../includes/header.php';

// Procesar eliminación si se envió por GET
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $stmt = $conn->prepare("DELETE FROM servicios WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: gestion-servicios.php");
    exit;
}

// Procesar formulario de nuevo servicio
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre'], $_POST['precio'])) {
    $nombre = trim($_POST['nombre']);
    $precio = floatval($_POST['precio']);

    if ($nombre !== '' && $precio > 0) {
        $stmt = $conn->prepare("INSERT INTO servicios (nombre, precio) VALUES (?, ?)");
        $stmt->bind_param("sd", $nombre, $precio);
        $stmt->execute();
        $stmt->close();
        header("Location: gestion-servicios.php");
        exit;
    }
}

// Obtener servicios existentes
$result = $conn->query("SELECT id_servicio, nombre, precio FROM servicios ORDER BY nombre ASC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestión de Servicios</title>
  <link rel="stylesheet" href="../css/dashboard.css">
</head>
<body>

<h2 style="text-align:center;">Gestión de Servicios</h2>

<section class="formulario-servicio">
  <h3>Agregar nuevo servicio</h3>
  <form method="POST">
    <input type="text" name="nombre" placeholder="Nombre del servicio" required>
    <input type="number" step="0.01" name="precio" placeholder="Precio" required>
    <button type="submit">Agregar</button>
  </form>
</section>

<section class="lista-servicios">
  <h3>Servicios existentes</h3>
  <table>
    <thead>
      <tr>
        <th>Nombre</th>
        <th>Precio</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['nombre']) ?></td>
          <td>$<?= number_format($row['precio'], 2) ?></td>
          <td>
            <a href="editar-servicio.php?id=<?= $row['id'] ?>">Editar</a> |
            <a href="gestion-servicios.php?eliminar=<?= $row['id'] ?>" onclick="return confirm('¿Eliminar este servicio?')">Eliminar</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</section>

<footer>
  <p>Todos los derechos reservados</p>
  <p>&copy; Marie Semipermanentes 2015</p>
</footer>

</body>
</html>
