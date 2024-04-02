<?php

namespace App\Controller;

use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(): Response
    {
        return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController',
        ]);
    }

    
    #[Route('/profile/edit', name: 'app_profile_edit')]
    public function modify(Request $request, EntityManagerInterface $entityManager): Response
    {


        $form = $this->createForm(UserType::class, $this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $entityManager->persist($this->getUser()); // insérer en base
                $entityManager->flush(); // fermer la transaction executée par la bdd

                $this->addFlash(
                    'success',
                    'Votre profile a bien été mis a jour !'
                );

                return $this->redirectToRoute('app_profile', [], Response::HTTP_SEE_OTHER);

            }
        }

        return $this->render('profile/modify-profile.html.twig', [
            "profileForm" => $form,
        ]);
    }
}
