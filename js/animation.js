document.addEventListener('DOMContentLoaded', function() {
    // Animation des messages de confirmation ou d'erreur
    const message = document.querySelector('p');
    if (message) {
        message.classList.add('fade-in');
    }

    // Animation de la page au chargement
    document.body.style.animation = "fadeIn 1s ease-out";
    
    // Ajouter une animation de survol du bouton
    const button = document.querySelector('button');
    button.addEventListener('mouseover', function() {
        button.style.transform = 'scale(1.1)';
    });

    button.addEventListener('mouseout', function() {
        button.style.transform = 'scale(1)';
    });
});
