import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.scss';

// this "modifies" the jquery module: adding behavior to it
// the bootstrap module doesn't export/return anything
require('bootstrap');

// loads the jquery package from node_modules
// import $ from 'jquery';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

document.addEventListener('DOMContentLoaded', (event) => {
    const carousel = document.querySelector('.carousel');
    let currentIndex = 0;

    function nextSlide() {
        currentIndex = (currentIndex + 1) % carousel.children.length;
        updateCarousel();
    }

    function updateCarousel() {
        carousel.style.transform = `translateX(-${currentIndex * 100}%)`;
    }

    setInterval(nextSlide, 3000); // Change d'image toutes les 3 secondes
});