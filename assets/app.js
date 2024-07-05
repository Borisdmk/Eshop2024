import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.scss';

// Importation des styles Font Awesome
import '@fortawesome/fontawesome-free/css/all.min.css';

// Optionnel : Importation des scripts Font Awesome (si vous avez besoin des fonctionnalités JavaScript de Font Awesome)
import '@fortawesome/fontawesome-free/js/all.min.js';

// this "modifies" the jquery module: adding behavior to it
// the bootstrap module doesn't export/return anything
require('bootstrap');

// loads the jquery package from node_modules
// import $ from 'jquery';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

// document.addEventListener('DOMContentLoaded', (event) => {
//     const carousel = document.querySelector('.carousel');
//     let currentIndex = 0;

//     function nextSlide() {
//         currentIndex = (currentIndex + 1) % carousel.children.length;
//         updateCarousel();
//     }

//     function updateCarousel() {
//         carousel.style.transform = `translateX(-${currentIndex * 100}%)`;
//     }

//     setInterval(nextSlide, 3000); // Change d'image toutes les 3 secondes
// });


// RECHERCHE ASYNC 

// SELECTION DU FORMULAIRE
//  Ajoute un écouteur d'événement pour le formulaire avec l'ID search-products qui se déclenche lors de la soumission du formulaire
document.querySelector('#search-products').addEventListener('submit', function (event) {

    // PREVENTION DE LA SOUMISSION DU FORMULAIRE
        event.preventDefault(); // Empêche le formulaire de soumettre de manière classique, permettant de gérer la soumission via JavaScript.

    // RECUPERATION DE LA VALEUR DE RECHERCHE
        const searchInput = document.querySelector('input[name="search"]');
        const query = searchInput.value; // Récupère la valeur de l'input de recherche.

    // ENVOIE DE LA REQUETE ASYNCHRONE
    // Envoie une requête HTTP GET à l'URL '/search-async' avec la valeur de 'query' comme paramètre
        fetch(`/search-async?search=${encodeURIComponent(query)}`)
        .then(response => response.json()) // Parse la réponse JSON.

        // TRAITEMENT DES PRODUITS RETOURNES
        .then(products => {
            // Récupère l'élément qui contiendra les résultats
            const resultsContainer = document.querySelector('#results'); // Sélectionne le conteneur qui affichera les résultats.
            let productHtml = '<h1>Tous les produits</h1> <div class="row">'; //  Initialise une chaîne HTML avec un titre et une division contenant une classe de rangée.

            // GENERATION DU HTML
            // Parcourt chaque produit reçu et crée un bloc HTML pour chacun
            products.forEach(product => {

                //Génère le HTML pour chaque produit en ajoutant dynamiquement des éléments de carte contenant les détails du produit.
                productHtml += ` 
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <img src="/uploads/products/${product.picture}" class="card-img-top" alt="${product.title}">
                            <div class="card-body">
                                <h5 class="card-title">${product.title}</h5>
                                <p class="card-text">${product.description}</p>
                                <p class="card-text">Prix: ${product.price}</p>
                                <p class="card-text">Stock: ${product.stock}</p>
                                <a href="/product/${product.id}" class="btn btn-info">Voir</a>
                                <form action="/cart/${product.id}" method="POST">
                                    <button type="submit" class="btn btn-outline-success">Ajouter au panier</button>
                                </form>
                            </div>
                        </div>
                    </div>
                `;
            });
            productHtml += "</div>";
            console.log(productHtml);
            // Affiche le HTML généré dans le conteneur des résultats
            resultsContainer.innerHTML = productHtml; // Remplace le contenu du conteneur des résultats par le HTML généré.
        });
});