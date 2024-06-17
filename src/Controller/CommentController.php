<?php

// src/Controller/CommentController.php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface; // Utilisation de l'EntityManager pour les opérations de persistance
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; // Utilisation du contrôleur de base de Symfony
use Symfony\Component\HttpFoundation\Request; // Utilisation de l'objet Request pour gérer les requêtes HTTP
use Symfony\Component\HttpFoundation\Response; // Utilisation de l'objet Response pour envoyer des réponses HTTP
use Symfony\Component\Routing\Annotation\Route; // Utilisation des annotations de routage
use Symfony\Component\Security\Core\Exception\AccessDeniedException; // Utilisation de l'exception AccessDeniedException pour gérer les accès non autorisés

#[Route('/comment')]
class CommentController extends AbstractController
{
    #[Route('/new', name: 'comment_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $comment = new Comment(); // Création d'une nouvelle instance de Comment
        $form = $this->createForm(CommentType::class, $comment); // Création du formulaire pour l'entité Comment
        $form->handleRequest($request); // Gestion de la requête par le formulaire

        if ($form->isSubmitted() && $form->isValid()) { // Vérification si le formulaire a été soumis et est valide
            $comment->setDate(new \DateTime()); // Définition de la date du commentaire à la date actuelle
            $comment->setUser($this->getUser()); // Associer le commentaire à l'utilisateur connecté
            $entityManager->persist($comment); // Persistance du commentaire
            $entityManager->flush(); // Envoi des modifications à la base de données

            return $this->redirectToRoute('app_profile_show', [], Response::HTTP_SEE_OTHER); // Redirection vers la route 'app_profile_show'
        }

        return $this->render('comment/new.html.twig', [
            'comment' => $comment, // Passage de l'entité Comment à la vue
            'form' => $form, // Passage du formulaire à la vue
        ]);
    }

    #[Route('/', name: 'comment_index', methods: ['GET'])]
    public function index(CommentRepository $commentRepository): Response
    {
        return $this->render('comment/index.html.twig', [ // Rendu de la vue pour afficher tous les commentaires
            'comments' => $commentRepository->findAll(), // Passage de tous les commentaires à la vue
        ]);
    }

    #[Route('/user', name: 'comment_user', methods: ['GET'])]
    public function userComments(CommentRepository $commentRepository): Response
    {
        $user = $this->getUser(); // Récupération de l'utilisateur connecté
        return $this->render('comment/user.html.twig', [ // Rendu de la vue pour afficher les commentaires de l'utilisateur
            'comments' => $commentRepository->findBy(['user' => $user]), // Passage des commentaires de l'utilisateur à la vue
        ]);
    }

    #[Route('/delete/{id}', name: 'comment_delete', methods: ['POST'])]
    public function delete(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        if ($comment->getUser() !== $this->getUser()) { // Vérification si l'utilisateur connecté est bien l'auteur du commentaire
            throw new AccessDeniedException('Vous n\'avez pas le droit de supprimer ce commentaire.');
        }

        $entityManager->remove($comment); // Suppression du commentaire
        $entityManager->flush();  // Envoi des modifications à la base de données

 
        return $this->redirectToRoute('app_profile'); // Redirection vers la route 'app_profile'
    }
}
