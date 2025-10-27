<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'cliente') {
    header("Location: ../login.php");
    exit();
}
include '../includes/db.php';

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(24));
}

// Obtener servicios
$stmt = $conn->prepare("SELECT id_servicio AS id, nombre, precio FROM servicios ORDER BY nombre");
$stmt->execute();
$servicios = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Obtener especialistas (manicuristas y pedicuristas)
$stmt = $conn->prepare("SELECT id_usuario, nombre FROM usuarios WHERE id_rol IN (2, 4)");
$stmt->execute();
$especialistas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Agendar Cita - Semis by Marie</title>
  <link rel="stylesheet" href="../css/agendar.css">
</head>
<body>

<?php include '../includes/header-cliente.php'; ?>

<main class="main-content">
  <div class="agendar-container">
    <!-- Aside izquierdo -->
    <aside class="aside aside-left">
      <img src="../Imagenes/agenda.jpg" alt="Manicura" class="aside-img">
      </aside>

    <!-- Formulario central -->
    <section class="form-section">
      <h2>Agendar Cita</h2>

      <?php if (!empty($_SESSION['booking_errors'])): ?>
        <div class="errors">
          <ul>
            <?php foreach ($_SESSION['booking_errors'] as $err): ?>
              <li><?php echo htmlspecialchars($err); ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <?php unset($_SESSION['booking_errors']); ?>
      <?php endif; ?>

      <?php
        $old = $_SESSION['booking_old'] ?? null;
      ?>

      <form id="bookingForm" action="confirmacion.php" method="POST">
        <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf']); ?>">

        <!-- Servicio -->
        <label for="servicio">Servicio:</label>
        <select name="servicio_id" id="servicio" required>
          <option value="">-- Selecciona un servicio --</option>
          <?php foreach ($servicios as $s): ?>
            <option value="<?php echo (int)$s['id']; ?>" data-precio="<?php echo $s['precio']; ?>"
              <?php if (!empty($old) && (int)$old['servicio_id'] === (int)$s['id']) echo 'selected'; ?>>
              <?php echo htmlspecialchars($s['nombre']); ?> - $<?php echo number_format($s['precio'], 2); ?>
            </option>
          <?php endforeach; ?>
        </select>

        <!-- Especialista -->
        <label for="especialista">Especialista:</label>
        <select name="especialista_id" id="especialista" required>
          <option value="">-- Selecciona un especialista --</option>
          <?php foreach ($especialistas as $e): ?>
            <option value="<?php echo (int)$e['id_usuario']; ?>" <?php if (!empty($old) && (int)$old['especialista_id'] === (int)$e['id_usuario']) echo 'selected'; ?>>
              <?php echo htmlspecialchars($e['nombre']); ?>
            </option>
          <?php endforeach; ?>
        </select>

        <!-- Fecha -->
        <label for="fecha">Fecha:</label>
        <input type="date" name="fecha" id="fecha" min="<?php echo date('Y-m-d'); ?>" 
               value="<?php echo !empty($old['fecha']) ? htmlspecialchars($old['fecha']) : ''; ?>" required>

        <!-- Hora -->
        <label for="hora">Hora:</label>
        <select name="hora" id="hora" required>
          <option value="">-- Selecciona hora --</option>
        </select>

        <!-- Total -->
        <p class="total">Total: <strong id="total">
          <?php echo !empty($old['total']) ? ('$' . number_format((float)$old['total'], 2)) : '$0.00'; ?>
        </strong></p>
        <input type="hidden" name="total" id="totalInput" 
               value="<?php echo !empty($old['total']) ? htmlspecialchars($old['total']) : '0'; ?>">

        <button type="submit" class="btn-confirmar">Confirmar y Pagar</button>
      </form>
    </section>

    <!-- Aside derecho -->
    <aside class="aside aside-right">
      <img src="../Imagenes/servicios.jpg" alt="Pedicura" class="aside-img">
      </aside>
  </div>
</main>
  
 
       
       

   

<script>
document.getElementById('especialista').addEventListener('change', cargarHoras);
document.getElementById('fecha').addEventListener('change', cargarHoras);
document.getElementById('servicio').addEventListener('change', actualizarTotal);

function actualizarTotal() {
    const servicio = document.getElementById('servicio');
    const precio = servicio.options[servicio.selectedIndex]?.getAttribute('data-precio') || '0';
    document.getElementById('total').textContent = '$' + parseFloat(precio).toFixed(2);
    document.getElementById('totalInput').value = precio;
}

function cargarHoras() {
    const especialista = document.getElementById('especialista').value;
    const fecha = document.getElementById('fecha').value;
    const selectHora = document.getElementById('hora');
    
    // Limpiar opciones actuales
    selectHora.innerHTML = '<option value="">-- Selecciona hora --</option>';
    
    // Validar que ambos campos estén llenos
    if (!especialista || !fecha) {
        return;
    }

    // Realizar la solicitud
    fetch(`obtener-horas.php?especialista=${encodeURIComponent(especialista)}&fecha=${encodeURIComponent(fecha)}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la red');
            }
            return response.json();
        })
        .then(data => {
            // Asegurar que data sea un array
            if (!Array.isArray(data)) {
                throw new Error('Formato de respuesta inválido');
            }

            if (data.length === 0) {
                const opt = document.createElement('option');
                opt.text = 'No hay horarios disponibles';
                opt.disabled = true;
                selectHora.appendChild(opt);
            } else {
                data.forEach(hora => {
                    const opt = document.createElement('option');
                    opt.value = hora;

                    // Formatear hora: 10:00 → 10:00 AM
                    const [h, m] = hora.split(':');
                    const hour = parseInt(h, 10);
                    const displayHour = hour % 12 || 12;
                    const ampm = hour < 12 ? 'AM' : 'PM';
                    opt.text = `${displayHour}:${m} ${ampm}`;

                    selectHora.appendChild(opt);
                });
            }
        })
        .catch(error => {
            console.error('Error al cargar horarios:', error);
            selectHora.innerHTML = '<option value="">Error al cargar horarios</option>';
        });
}

// Inicializar total si hay datos anteriores
actualizarTotal();
</script>

</body>

</html>

<?php if (!empty($_SESSION['booking_old'])) unset($_SESSION['booking_old']); ?>