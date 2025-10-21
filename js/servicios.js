// Minimal servicios.js
// Adds client-side filtering for service cards by data-tipo
document.addEventListener('DOMContentLoaded', function(){
  const buttons = document.querySelectorAll('.filtro-btn');
  const items = document.querySelectorAll('.servicio-item');

  if (!buttons.length || !items.length) return;

  buttons.forEach(btn => {
    btn.addEventListener('click', function(){
      buttons.forEach(b=>b.classList.remove('active'));
      this.classList.add('active');
      const tipo = this.dataset.tipo;
      items.forEach(it => {
        if (tipo === 'todos' || (it.dataset.tipo && it.dataset.tipo.toLowerCase() === tipo.toLowerCase())) {
          it.style.display = '';
        } else {
          it.style.display = 'none';
        }
      });
    });
  });
});
