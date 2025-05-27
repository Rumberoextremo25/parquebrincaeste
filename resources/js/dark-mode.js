document.addEventListener('DOMContentLoaded', function() {
    const darkModeCheckbox = document.getElementById('dark_mode');
    const body = document.body;

    // Aplicar el modo oscuro inicial
    if (darkModeCheckbox.checked) {
        body.classList.add('dark');
    } else {
        body.classList.remove('dark');
    }

    // Escuchar el cambio en el checkbox
    darkModeCheckbox.addEventListener('change', function() {
        body.classList.toggle('dark');
    });
});