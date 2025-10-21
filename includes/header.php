<header class="navbar">
  <div class="nav-inner">
    <div class="logo">
      <img src="Imagenes/nuevologo.jpg" alt="Logo Semis by Marie"/>
      <h1>Semipermanentes by Marie</h1>
      <p>"Esmaltado que dura. Belleza que brilla."</p>
    </div>

    <button class="nav-toggle" aria-label="Abrir menú" aria-expanded="false">
      <span class="hamburger"></span>
    </button>

    <nav class="menu-nav">
      <ul class="menu">
      <li><a href="index.php">Inicio</a></li>
      <li><a href="cliente/servicios.php">Servicios</a></li>

      <?php if (isset($_SESSION['user_id'])): ?>
        <!-- Si el usuario está logueado -->
        <li>
          <a href="#">Hola, <?php echo htmlspecialchars($_SESSION['nombre']); ?></a>
          <ul class="submenu">
            <li><a href="logout.php">Cerrar Sesión</a></li>
          </ul>
        </li>
      <?php else: ?>
        <!-- Si el usuario NO está logueado -->
        <li><a href="login.php">Iniciar Sesión</a></li>
      <?php endif; ?>
      </ul>
    </nav>
  </div>
</header>