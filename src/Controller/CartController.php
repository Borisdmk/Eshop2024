<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderDetail;
use App\Entity\OrderDetails;
use App\Entity\Product;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{



    #[Route('/cart', name: 'app_cart')]
    public function index(Request $request): Response
    {

        $session = $request->getSession();

        $carTotal = 0;

        if(!is_null($session->get('cart')) && count($session->get('cart')) > 0) {
            for($i = 0; $i < count($session->get('cart')["id"]); $i++) {
                $carTotal += (float) $session->get('cart')["price"][$i] * $session->get('cart')["quantity"][$i];
            }
        }   

        return $this->render('cart/index.html.twig', [
            'cartItems' => $session->get('cart'),
            'cartTotal' => $carTotal,
        ]);
    }
    
   


    #[Route('/cart/{idProduct}', name: 'app_cart_add', methods: ['POST', 'GET'])]
    public function addProduct(Request $request, ProductRepository $productRepository, int $idProduct): Response
    {

        // créer la session
        $session = $request->getSession();
        // $cart = $session->get('cart', []); // permet de videt la session et d'en relancer une

        //si la session existe pas je la crée
        if(!$session->get('cart')) {
            $session->set('cart', [
                "id" => [],
                "title" => [],
                "description" => [],
                "picture" => [],
                "price" => [],
                "quantity" => [],
                "priceIdStripe" => [],  
            ]);
        }

        $cart = $session->get('cart');

        // ajouter le produit au panier
        // recupérer les infos du produit en BDD et l'ajouter a mon panier
        $product = $productRepository->find($idProduct);
        $cart["id"][] = $product->getId();
        $cart["title"][] = $product->getTitle();
        $cart["description"][] = $product->getDescription();
        $cart["picture"][] = $product->getPicture();
        $cart["price"][] = $product->getPrice();
        $cart["quantity"][] = 1;
        $cart["priceIdStripe"][] = $product->getPriceIdStripe();  

        $session->set('cart', $cart);

        
        // calculer le montant total de mon panier
        $cartTotal = 0;

        for($i = 0; $i < count($session->get('cart')['id']); $i++) {
            $cartTotal += (float) $session->get('cart')["price"][$i] * $session->get('cart')["quantity"][$i];
        }

        // afficher la page panier

        return $this->render('cart/index.html.twig', [
            'cartItems' => $session->get('cart'),
            'cartTotal' => $cartTotal,
        ]);
    }

    #[Route('/cart/delete/all', name: 'app_cart_delete', methods: ['POST'])]
    public function deleteCart(Request $request): Response
    {
        $session = $request->getSession();
        $session->remove('cart');

        return new JsonResponse(['status' => 'success']);
    }
}
    