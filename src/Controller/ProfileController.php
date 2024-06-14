<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\User;
use App\Form\ChangePasswordFormType;
use App\Form\CommentType;
use App\Form\UserType;
use App\Repository\CommentRepository;
use App\Service\ImageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(Request $request, EntityManagerInterface $entityManager, CommentRepository $commentRepository): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrer le commentaire en base de données
            $comment->setUser($this->getUser()); // Assurez-vous que l'utilisateur actuel est lié au commentaire
            $comment->setDate(new \DateTime);
            $entityManager->persist($comment);
            $entityManager->flush();
    
            $this->addFlash('success', 'Votre commentaire a été ajouté avec succès.');
            return $this->redirectToRoute('app_profile');
        }
    
        // Récupérer les commentaires de l'utilisateur actuel
        $userComments = $commentRepository->findBy(['user' => $this->getUser()]);
    
        return $this->render('profile/index.html.twig', [
            'profileForm' => $form->createView(),
            'userComments' => $userComments,
        ]);
    }

    
    #[Route('/profile/edit', name: 'app_profile_edit')]
    public function modify(Request $request, EntityManagerInterface $entityManager, ImageService $imageService, User $user): Response
    {


        $form = $this->createForm(UserType::class, $this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                // Copie l'image et récupère le nom du nouveau fichier
                $fileName = $imageService->copyImage("picture", $this->getParameter("article_picture_directory") ,$form);
                // Met à jour le nom de l'image de l'article
                $this->getUser()->setPicture($fileName);
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

    #[Route('/profile/password/edit', name: 'app_profile_password_edit')]
    public function modifyPassword(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordEncoder): Response
    {

        $user = $this->getUser(); // recupere l'user actuellement connecté
        $form = $this->createForm(ChangePasswordFormType::class, $user); // Cela crée un formulaire en utilisant le type de formulaire 'ChangePasswordFormType' et l'associe à l'utilisateur actuel.
        $form->handleRequest($request); // Cela indique au formulaire de gérer la requête HTTP. Cela vérifie si le formulaire a été soumis et effectue les actions appropriées en conséquence.

        if ($form->isSubmitted()) {
            if ($passwordEncoder->isPasswordValid($user, $form['oldpassword']->getData())) {
                // Cela vérifie si l'ancien mot de passe fourni dans le formulaire correspond au mot de passe actuel de l'utilisateur.

                if ($form->isValid()) {
                    // Cela vérifie si le formulaire est valide, c'est-à-dire si toutes les contraintes de validation sont satisfaites.

                    $newEncodedPassword = $passwordEncoder->hashPassword($user, $form->get('password')->getData()); // Cela génère un nouveau mot de passe encodé à partir du mot de passe fourni dans le formulaire.
                    $user->setPassword($newEncodedPassword); // Cela définit le nouveau mot de passe pour l'utilisateur.

                    $entityManager->persist($this->getUser()); // update en base Cela indique à l'EntityManager de suivre l'utilisateur pour tout enregistrement ultérieur en base de données.
                    $entityManager->flush(); // fermer la transaction executée par la bdd Cela enregistre tous les changements dans la base de données.

                    $this->addFlash( // Cela ajoute un message flash de succès qui sera affiché sur la prochaine page.
                        'success',
                        'Votre mot de passe a bien ete mis a jour !'
                    );

                    return $this->redirectToRoute('app_profile', [], Response::HTTP_SEE_OTHER); // Cela redirige l'utilisateur vers la page de profil après avoir mis à jour le mot de passe.

                }
            } else {
                $this->addFlash('message', 'La saisie de lancien mot de passe est incorrecte'); // Cela ajoute un message flash pour indiquer que l'ancien mot de passe fourni est incorrect.
            }
        }

        return $this->render('profile/modify-password.html.twig', [ // Cela rend le modèle 'profile/modify-password.html.twig' et passe le formulaire de modification de mot de passe à ce modèle.
            'passwordForm' => $form,
        ]);
    }
}
