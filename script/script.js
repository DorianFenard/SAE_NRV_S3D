document.addEventListener('DOMContentLoaded', function () {
    // Associer les boutons à leurs sections de filtres
    const toggleMap = {
        toggleButtonDate: '.filtersDate',
        toggleButtonLieu: '.filtersLieu',
        toggleButtonGenre: '.filtersGenre'
    };

    // Ajouter un écouteur d'événements à chaque bouton
    Object.keys(toggleMap).forEach(buttonId => {
        const button = document.getElementById(buttonId);
        const targetSelector = toggleMap[buttonId];
        const targetElement = document.querySelector(targetSelector);

        button.addEventListener('click', function () {
            // Basculer l'affichage en ajoutant ou supprimant la classe "active"
            targetElement.classList.toggle('active');
        });
    });
});
