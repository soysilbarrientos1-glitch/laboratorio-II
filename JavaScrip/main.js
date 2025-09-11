// main.js - Funciones del cliente

function editarPerfil() {
  alert("Función en desarrollo. Aquí se permitirá editar datos personales.");
}

// Manejo básico de formulario de contacto
document.getElementById("formulario-contacto")?.addEventListener("submit", function(e) {
  e.preventDefault();
  alert("Gracias por contactarnos. Nos pondremos en contacto contigo pronto.");
  this.reset();
});
// Simulación de autenticación
const usuarioAutenticado = false;

// Mostrar/ocultar elementos del menú según el estado de autenticación
if (usuarioAutenticado) {
  document.getElementById("user-menu").textContent = "Mi Cuenta";
  document.getElementById("user-submenu").style.display = "block";
} else {
  document.getElementById("user-menu").textContent = "Iniciar Sesión";
  document.getElementById("user-submenu").style.display = "none";
}