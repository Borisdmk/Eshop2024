<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SearchController extends AbstractController
{
    #[Route('/search-async', name: 'app_search')]
    public function index(Request $request, ProductRepository $productRepository): JsonResponse
    {
        $query = $request->query->get('search', '');         // Récupère la valeur du paramètre 'search' dans la requête, ou une chaîne vide par défaut
        $products = $productRepository->createQueryBuilder('p') // Crée une requête pour rechercher des produits dont le titre ou la description contient la valeur de 'query'
            ->where('p.title LIKE :query')
            ->orWhere('p.description LIKE :query')
            ->setParameter('query', '%' . $query . '%') // Définit le paramètre query avec des caractères de pourcentage pour une recherche partielle.
            ->getQuery()
            ->getResult(); // Exécute la requête et retourne les résultats.

        $data = []; // Initialise un tableau pour stocker les données des produits

        // Parcourt chaque produit trouvé et extrait les informations souhaitées
        foreach ($products as $product) {
            $data[] = [
                'id' => $product->getId(),
                'title' => $product->getTitle(),
                'description' => $product->getDescription(),
                'picture' => $product->getPicture(),
                'price' => $product->getPrice(),
                'stock' => $product->getStock(),
            ];
        }

        return new JsonResponse($data); // Retourne les données des produits en réponse JSON
    }
}
