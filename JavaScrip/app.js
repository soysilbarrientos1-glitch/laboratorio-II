// =========================
// AGENDAR CITA
// =========================
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('citaForm');
  const successMessage = document.getElementById('successMessage');

  if (form) {
    form.addEventListener('submit', function(e) {
      e.preventDefault();

      const nombre = document.getElementById('nombre').value.trim();
      const servicio = document.getElementById('servicio').value;
      const fecha = document.getElementById('fecha').value;
      const hora = document.getElementById('hora').value;

      // Validar que no sea una fecha pasada
      const ahora = new Date();
      const cita = new Date(`${fecha}T${hora}`);
      if (cita < ahora) {
        alert("❌ No puedes agendar una cita en el pasado.");
        return;
      }

      // Generar ID único
      const id = Date.now();

      // Guardar en localStorage
      const citas = JSON.parse(localStorage.getItem('citas') || '[]');
      citas.push({ id, nombre, servicio, fecha, hora });
      localStorage.setItem('citas', JSON.stringify(citas));

      // Mostrar mensaje
      successMessage.textContent = `✅ ¡Cita confirmada, ${nombre}! Te esperamos el ${formatearFecha(fecha)} a las ${hora}.`;
      successMessage.style.color = '#d63384';
      setTimeout(() => {
        successMessage.textContent = '';
      }, 5000);

      // Limpiar formulario
      form.reset();
    });
  }
});

// =========================
// FORMATEAR FECHA (ej: 15/04/2025)
// =========================
function formatearFecha(fechaISO) {
  const [año, mes, día] = fechaISO.split('-');
  return `${día}/${mes}/${año}`;
}

// =========================
// MOSTRAR CITAS EN PANEL DE CONTROL
// =========================
function mostrarCitas() {
  const citas = JSON.parse(localStorage.getItem('citas') || '[]');
  const citasList = document.getElementById('citasList');

  if (!citasList) return;

  if (citas.length === 0) {
    citasList.innerHTML = '<p>No hay citas programadas.</p>';
    return;
  }

  // Ordenar por fecha y hora
  citas.sort((a, b) => new Date(a.fecha + 'T' + a.hora) - new Date(b.fecha + 'T' + b.hora));

  let html = '<h2>Citas Programadas</h2><ul>';
  citas.forEach(cita => {
    html += `
      <li>
        <div>
          <strong>${cita.nombre}</strong> - ${cita.servicio}
          <br><small>📅 ${formatearFecha(cita.fecha)} a las ${cita.hora}</small>
        </div>
        <button onclick="eliminarCita(${cita.id})">Eliminar</button>
      </li>
    `;
  });
  html += '</ul>';
  citasList.innerHTML = html;
}

// =========================
// ELIMINAR CITA
// =========================
function eliminarCita(id) {
  let citas = JSON.parse(localStorage.getItem('citas') || '[]');
  citas = citas.filter(cita => cita.id !== id);
  localStorage.setItem('citas', JSON.stringify(citas));
  mostrarCitas(); // Actualizar lista
}

// Si estamos en panel-control.html, mostramos las citas
if (window.location.pathname.includes('panel-control.html')) {
  mostrarCitas();
}