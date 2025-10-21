// JavaScrip/booking.js
document.addEventListener('DOMContentLoaded', function () {
    const servicioSelect = document.getElementById('servicio');
    const totalSpan = document.getElementById('total');
    const totalInput = document.getElementById('totalInput');
    const bookingForm = document.getElementById('bookingForm');

    if (!servicioSelect || !totalSpan || !totalInput) {
        console.warn('Faltan elementos para el cálculo del total en agendar-cita.php');
        return;
    }

    // Formateador para la visualización (ajustalo si necesitas otra localización)
    const nf = new Intl.NumberFormat('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    function parsePrecio(raw) {
        if (raw == null) return 0;
        let s = String(raw).trim();
        // Quitar signo $ y espacios
        s = s.replace(/\$/g, '').replace(/\s/g, '');
        // Si usan punto como separador de miles y coma decimal, normalizamos: eliminar puntos y convertir comas a puntos
        // Ej: "20.000,50" -> "20000.50"
        if (s.indexOf(',') > -1 && s.indexOf('.') > -1) {
            s = s.replace(/\./g, '').replace(/,/g, '.');
        } else {
            // Si sólo hay comas, convertir a punto ("5,00" -> "5.00"); si sólo hay puntos, dejarlos
            s = s.replace(/,/g, '.');
        }
        const n = parseFloat(s);
        return isNaN(n) ? 0 : n;
    }

    function actualizarTotal() {
        const option = servicioSelect.options[servicioSelect.selectedIndex];
        const rawPrecio = option ? option.getAttribute('data-precio') : null;
        const precio = parsePrecio(rawPrecio);

        // Mostrar formateado
        totalSpan.textContent = '$' + nf.format(precio);

        // Guardar valor consistente para enviar al servidor (2 decimales, punto decimal)
        totalInput.value = precio.toFixed(2);
    }

    servicioSelect.addEventListener('change', actualizarTotal);
  
        // Availability check elements
        const especialistaSelect = document.getElementById('especialista');
        const fechaInput = document.getElementById('fecha');
        const horaSelect = document.getElementById('hora');
        const availabilityMessage = document.getElementById('availabilityMessage');

        async function checkAvailability() {
            if (!especialistaSelect || !fechaInput || !horaSelect || !availabilityMessage) return;
            const especialista_id = especialistaSelect.value;
            const fecha = fechaInput.value;
            const hora = horaSelect.value;
            // only check when all values present
            if (!especialista_id || !fecha || !hora) {
                availabilityMessage.style.display = 'none';
                if (bookingForm) bookingForm.querySelector('button[type="submit"]').disabled = false;
                return;
            }
            try {
                const data = new URLSearchParams();
                data.append('especialista_id', especialista_id);
                data.append('fecha', fecha);
                data.append('hora', hora);
                const res = await fetch('../cliente/check-disponibilidad.php', { method: 'POST', body: data });
                const json = await res.json();
                if (json && json.available === true) {
                    availabilityMessage.style.display = 'block';
                    availabilityMessage.style.color = '#138000';
                    availabilityMessage.textContent = 'Horario disponible ✓';
                    if (bookingForm) bookingForm.querySelector('button[type="submit"]').disabled = false;
                } else {
                    availabilityMessage.style.display = 'block';
                    availabilityMessage.style.color = '#a30b2a';
                    availabilityMessage.textContent = 'Horario no disponible. Por favor elegí otro horario.';
                    if (bookingForm) bookingForm.querySelector('button[type="submit"]').disabled = true;
                }
            } catch (err) {
                console.warn('Error verificando disponibilidad', err);
            }
        }

        // Attach listeners
        if (especialistaSelect) especialistaSelect.addEventListener('change', checkAvailability);
        if (fechaInput) fechaInput.addEventListener('change', checkAvailability);
        if (horaSelect) horaSelect.addEventListener('change', checkAvailability);

        // run on load as well
        checkAvailability();

    // Validación antes de enviar: evitar total 0
    if (bookingForm) {
        bookingForm.addEventListener('submit', function (e) {
            const total = parseFloat(totalInput.value || '0');
            if (!isFinite(total) || total <= 0) {
                e.preventDefault();
                alert('Por favor seleccioná un servicio válido antes de confirmar la cita.');
                return false;
            }
            // evitar envíos dobles: desactivar botón submit opcional
            const btn = bookingForm.querySelector('button[type="submit"]');
            if (btn) btn.disabled = true;
        });
    }

    // Ejecutar al cargar (por si hay opción preseleccionada)
    actualizarTotal();
});