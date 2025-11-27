<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'cliente') {
    header("Location: ../login.php");
    exit();
}
include '../includes/db.php';

// Corregido: usamos 'id_servicio' y 'duracion_minutos' (sin alias)
$stmt = $conn->prepare("SELECT id_servicio AS id, nombre, descripcion, duracion_minutos, precio, imagen_url, tipo FROM servicios ORDER BY tipo, nombre");
$stmt->execute();
$result = $stmt->get_result();
$servicios = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Nuestros Servicios - Semis by Marie</title>
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/servicios.css">
  <link rel="stylesheet" href="../css/header-footer.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
<?php include '../includes/header-cliente.php'; ?>

<main class="main-content">
  <section id="servicios" class="seccion">
    <!-- Título principal -->
    <h2 class="section-title">Nuestros Servicios</h2>

    <!-- Filtros -->
    <div class="filtros">
      <button class="filtro-btn active" data-tipo="todos">Todos</button>
      <button class="filtro-btn" data-tipo="manicura">Manicuras</button>
      <button class="filtro-btn" data-tipo="pedicura">Pedicuras</button>
    </div>

    <!-- Grid de servicios -->
    <div class="cards" id="servicios-container">
      <?php if (!empty($servicios)): ?>
        <?php foreach ($servicios as $s): ?>
          <div class="card servicio-item" data-tipo="<?php echo htmlspecialchars($s['tipo'] ?? 'otros'); ?>">
            <?php if (!empty($s['imagen_url'])): ?>
              <div class="img-wrap">
                <?php
                  // Asegurar que la ruta sea correcta desde cliente/
                  $img = $s['imagen_url'];
                  $img = trim($img);
                  if ($img !== ''){
                    if (preg_match('#^(https?:)?//#i', $img)){
                      // URL absoluta o relativa con protocolo -> dejar como está
                    } elseif (substr($img,0,1) === '/'){
                      // Ruta absoluta desde raíz del sitio
                      $img = '..' . $img;
                    } else {
                      // Ruta relativa desde cliente/ -> prefijar '../'
                      $img = '../' . ltrim($img, './');
                    }
                  }
                ?>
                <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($s['nombre']); ?>" class="servicio-img">
              </div>
            <?php else: ?>
              <div class="placeholder-img">
                <i class="fas fa-sparkles"></i>
              </div>
            <?php endif; ?>

            <!-- Contenido del card -->
            <div class="body">
              <h3><?php echo htmlspecialchars($s['nombre']); ?></h3>
              <p class="descripcion"><?php echo htmlspecialchars($s['descripcion']); ?></p>
              <p class="precio">Precio: $<?php echo number_format($s['precio'], 2); ?></p>
              <p class="duracion"><?php echo (int)($s['duracion_minutos'] ?? 0); ?> minutos</p>
              <p class="tipo">Tipo: <?php echo htmlspecialchars($s['tipo']); ?></p>
            </div>

            <!-- Botón de agendar -->
            <div class="card-footer">
              <a href="agendar-cita.php?servicio_id=<?php echo (int)$s['id']; ?>" class="btn-agendar">Agendar</a>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="no-servicios">No hay servicios disponibles.</p>
      <?php endif; ?>
    </div>
  </section>
</main>           



<?php include '../includes/footer.php'; ?>
<script src="../js/servicios.js"></script>
<script src="../js/ui.js"></script>
</body>
</html>
