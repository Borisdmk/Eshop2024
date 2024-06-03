<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function index(Request $request): Response
    {
        $session = $request->getSession();
        $cart = $session->get('cart', []);

        $cartTotal = array_reduce($cart, function ($total, $item) {
            return $total + $item['price'] * $item['quantity'];
        }, 0);

        return $this->render('cart/index.html.twig', [
            'cartItems' => $cart,
            'cartTotal' => $cartTotal,
        ]);
    }

    #[Route('/cart/add/{id}', name: 'app_cart_add', methods: ['POST'])]
    public function addProduct(Request $request, ProductRepository $productRepository, int $id): Response
    {
        $session = $request->getSession();
        $cart = $session->get('cart', []);

        $product = $productRepository->find($id);

        if ($product) {
            $cartItem = [
                'id' => $product->getId(),
                'title' => $product->getTitle(),
                'description' => $product->getDescription(),
                'picture' => $product->getPicture(),
                'price' => $product->getPrice(),
                'quantity' => 1,
            ];

            // Vérifier si le produit est déjà dans le panier
            $found = false;
            foreach ($cart as &$item) {
                if ($item['id'] === $product->getId()) {
                    $item['quantity'] += 1;
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $cart[] = $cartItem;
            }
        }

        $session->set('cart', $cart);

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/delete', name: 'app_cart_delete', methods: ['GET'])]
    public function deleteCart(Request $request): Response
    {
        $session = $request->getSession();
        $session->remove('cart');

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/checkout', name: 'app_cart_checkout', methods: ['POST'])]
    public function checkout(Request $request, EntityManagerInterface $entityManager): Response
    {
        $session = $request->getSession();
        $cart = $session->get('cart', []);

        if (!$cart) {
            return $this->redirectToRoute('app_cart');
        }

        $order = new Order();
        $order->setUser($this->getUser());
        $order->setDate(new \DateTime());
        $order->setStatus('pending');
        $order->setPdf(false);

        $total = 0;

        foreach ($cart as $item) {
            $orderDetail = new OrderDetails();
            $orderDetail->setIdOrder($order);
            $orderDetail->setProduct($entityManager->getRepository(Product::class)->find($item['id']));
            $orderDetail->setQuantity($item['quantity']);

            $entityManager->persist($orderDetail);

            $total += $item['price'] * $item['quantity'];
        }

        $order->setTotal($total);
        $entityManager->persist($order);
        $entityManager->flush();

        $session->remove('cart');

        return $this->redirectToRoute('app_cart');
    }
}
