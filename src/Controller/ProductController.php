<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Service\ImageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/product')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'app_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    // #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    // public function new(Request $request, EntityManagerInterface $entityManager, ImageService $imageService): Response
    // {
    //     $product = new Product();
    //     $form = $this->createForm(ProductType::class, $product);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {

    //         $fileName = $imageService->copyImage("picture", $this->getParameter("article_picture_directory"), $form);
    //         $product->setPicture($fileName);
    //         $entityManager->persist($product);
    //         $entityManager->flush();

    //         $this->addFlash(
    //             'succes',
    //             'Votre produit a bien été ajouté'
    //         );

    //         return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->render('product/new.html.twig', [
    //         'product' => $product,
    //         'form' => $form,
    //     ]);
    // }

    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product, Request $request, EntityManagerInterface $entityManager): Response
    {


        return $this->render('product/show_product.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    // #[Route('/{id}', name: 'app_product_delete', methods: ['POST'])]
    // public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    // {
    //     if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
    //         $entityManager->remove($product);
    //         $entityManager->flush();
    //     }

    //     return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    // }

    #[Route('/category/{id_category}', name: 'app_get_product_by_category', methods: ['GET'])]
    public function getProductByCategory(EntityManagerInterface $entityManager, int $id_category): Response
    {
        //findBy methode prédefini, permet de recuperer des donées en filtrant,
        $products = $entityManager->getRepository(Product::class)->findBy(array("category" => $id_category));
        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }
}