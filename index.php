<?php
session_start();

// Reutilizar la conexión desde includes/db.php
require_once __DIR__ . '/includes/db.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Semis by Marie</title>
  <link rel="stylesheet" href="css/style.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
</head>

<body>

<!-- HEADER FIJO -->
<header class="navbar">
  <div class="logo">
    <img src="Imagenes/nuevologo.jpg" alt="Logo Semis by Marie" width="80" height="80">
  </div>
  <div class="title-container">
    <h1>Semipermanentes by Marie</h1>
    <p>"Esmaltado que dura. Belleza que brilla."</p>
  </div>
  <nav class="menu-nav">
    <ul class="menu">
      <li><a href="index.php">Inicio</a></li>
  <li><a href="cliente/servicios.php">Servicios</a></li>
  <li><a href="cliente/agendar-cita.php" class="btn-primary">Agenda tu cita</a></li>

        <?php if (!empty($_SESSION['user_id'])): ?>
          <?php if (!empty($_SESSION['rol']) && $_SESSION['rol'] !== 'cliente'): ?>
            <li><a href="admin/dashboard.php">Panel de Control</a></li>
          <?php endif; ?>
          <li><a href="#">Hola, <?php echo htmlspecialchars(!empty($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario'); ?></a></li>
          <li><a href="logout.php">Cerrar Sesión</a></li>
        <?php else: ?>
          <li><a href="login.php">Iniciar Sesión</a></li>
        <?php endif; ?>
    </ul>
  </nav>
</header>

<!-- ... código anterior ... -->

<!-- CONTENIDO PRINCIPAL -->
<main>
  <section class="main-section">
    <aside class="aside">
      <img src="Imagenes/salon.jpg" alt="Interior del Salón" class="aside-image">
    </aside>

    <div class="content">
      <h2 class="welcome-title">Bienvenidos</h2>
      <p>Llevamos 4 años realzando tu belleza natural con manicures y pedicures impecables, diseños únicos y esmaltado semipermanente de calidad.</p>
      <p>Ofrecemos precios accesibles, atención personalizada y un ambiente acogedor y relajante.</p>
      <p>Agenda tu cita hoy y descubre por qué somos tu destino favorito para manos y pies perfectos.</p>

  <h3 class="destacados">Nuestros Servicios Destacados</h3>
      <ul class="servicios-lista">
        <?php
        $sql = "SELECT nombre FROM servicios ORDER BY nombre";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<li>" . htmlspecialchars($row['nombre']) . "</li>";
            }
        } else {
            echo "<li>No hay servicios disponibles en este momento.</li>";
        }
        ?>
      </ul>

      <?php if (!empty($_SESSION['user_id'])): ?>
        <a href="cliente/agendar-cita.php" class="btn-primary">Agenda tu cita</a>
      <?php else: ?>
        <a href="login.php" class="btn-secondary">Inicia sesión para agendar tu cita</a>
      <?php endif; ?>
    </div>
  </section>
</main>

<!-- FOOTER FIJO -->
<footer class="footer">
  <div class="footer-container">
    <div class="social-icons">
      <a href="https://www.instagram.com/semipermanentes_bymarie" target="_blank">
        <i class="fab fa-instagram"></i>
      </a>
      <a href="https://www.facebook.com/" target="_blank" aria-label="Facebook">
        <i class="fab fa-facebook-f"></i>
      </a>
      <a href="https://twitter.com/" target="_blank" aria-label="Twitter">
        <i class="fab fa-twitter"></i>
      </a>
    </div>
    <div class="copyright">
      &copy; Flor Acosta & Mary Barrientos 2025 | Todos los derechos reservados
    </div>
  </div>
</footer>

<?php
// La conexión se crea en includes/db.php y se usa en otras páginas; cerrarla está bien si no se necesita más.
if (isset($conn) && $conn instanceof mysqli) {
  $conn->close();
}
?>
<script src="js/ui.js"></script>
</body>
</html>