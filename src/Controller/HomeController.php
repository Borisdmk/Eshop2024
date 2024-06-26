<?php

namespace App\Controller;


use App\Entity\Category;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Translation\TranslatableMessage;

class HomeController extends AbstractController
{
    // symfony dit : si tu as /home tu appelles cette fonction, et tu affiches ceci
    // controller_name = homeController (variables de vues)
    #[Route('/', name: 'app_home')]
    public function index(ProductRepository $productRepository, CategoryRepository $categoryRepository, PaginatorInterface $paginator, Request $request, CommentRepository $commentRepository): Response
    {

        // $msg_acceuil = new TranslatableMessage('its the message to translate');
        $products = $paginator->paginate(
            $productRepository->findAll(), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            2 /*limit per page*/
        );

        // Récupérer les commentaires à afficher
        $comments = $commentRepository->findAll();

        return $this->render('home/index.html.twig', [
            // 'msg_acceuil' => $msg_acceuil,
            'categories' => $categoryRepository->findAll(),
            'products' => $products,
            'comments' => $comments, 
        ]);
    }

    #[Route('/search', name: 'app_search_product', methods: ['GET'])]
    public function getProductBySearch(ProductRepository $productRepository, PaginatorInterface $paginator, Request $request): Response
    {

        // si j'ai un param GET search
        if ($request->query->has("search")) {

            $search = strtolower($request->query->get("search"));

            $products = $paginator->paginate(
                $productRepository->findProductBySearch($search), /* query NOT result */
                $request->query->getInt('page', 1), /*page number*/
                2 /*limit per page*/
            );



            return $this->render('product/index.html.twig', [
                'products' => $products,
            ]);

        } else {
            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }


    }
}
