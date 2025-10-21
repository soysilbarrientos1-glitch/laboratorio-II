<header class="navbar">
  <div class="nav-inner">
    <div class="logo">
      <a href="../index.php" class="logo-link">
        <img src="../Imagenes/nuevologo.jpg" alt="Logo Semis by Marie" width="60" height="60">
      </a>
      <div class="title-container">
        <a href="../index.php" class="brand-link"><h1>Semipermanentes by Marie</h1></a>
      </div>
    </div>

      <button class="nav-toggle" aria-label="Abrir menú" aria-expanded="false"> 
        <span class="hamburger"></span>
      </button>
      <nav class="menu-nav" role="navigation">
        <ul class="menu" role="menubar">
        <li><a href="../index.php">Inicio</a></li>
        <li><a href="../cliente/servicios.php">Servicios</a></li>
        <li><a href="../cliente/agendar-cita.php" class="btn-primary">Agendar</a></li>
        <li class="menu-user">
          <a href="#">Hola, <?php echo htmlspecialchars($_SESSION['nombre']); ?> <i class="fas fa-caret-down" style="margin-left:6px;"></i></a>
          <ul class="submenu">
            <li><a href="../logout.php">Cerrar Sesión</a></li>
          </ul>
        </li>
      </ul>
    </nav>
  </div>
</header>