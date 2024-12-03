// public/js/animation.js
document.addEventListener('DOMContentLoaded', function () {
    var animationContainer = document.querySelector('.animation-container');
    if (animationContainer) {
        setTimeout(function () {
            animationContainer.classList.add('show');
        }, 500); // Attendre 500ms avant de montrer l'animation

        setTimeout(function () {
            animationContainer.classList.remove('show');
        }, 5000); // Cacher l'animation après 5 secondes
    }
});
