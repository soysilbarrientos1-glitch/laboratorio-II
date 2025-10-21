document.addEventListener('DOMContentLoaded', function () {
    const filterButtons = document.querySelectorAll('.filtro-btn');
    const serviceCards = document.querySelectorAll('.servicio-item');

    filterButtons.forEach(button => {
        button.addEventListener('click', () => {
            filterButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');

            const tipo = button.dataset.tipo;

            serviceCards.forEach(card => {
                if (tipo === 'todos' || card.dataset.tipo === tipo) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
});