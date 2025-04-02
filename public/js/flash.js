// Gestion des notifications flash
document.querySelectorAll('.flash-message').forEach(notification => {
    // Disparaît après 5 secondes
    setTimeout(() => {
        notification.classList.add('hide');
    }, 5000);
    
    // Supprime après l'animation
    notification.addEventListener('animationend', function(e) {
        if (e.animationName === 'slideOut') {
            this.remove();
        }
    });
});
