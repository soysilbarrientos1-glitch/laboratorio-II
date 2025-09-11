// footer.js
document.addEventListener("DOMContentLoaded", function () {
  // Añade efecto hover a los iconos
  const icons = document.querySelectorAll(".social-icons a");

  icons.forEach(icon => {
    icon.addEventListener("mouseenter", () => {
      icon.style.transform = "scale(1.3)";
    });

    icon.addEventListener("mouseleave", () => {
      icon.style.transform = "scale(1)";
    });
  });
});
