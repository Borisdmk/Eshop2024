<?php

namespace App\Service;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
class ImageService {

    
// injection de dépendane depuis le constructeur
// car j'ai besoin de l'interface Slugger
    public function __construct(private SluggerInterface $slugger) {

    }

    public function copyImage($name, $directory, $form) {

        // Récupère le fichier image à partir du formulaire
        $imageFile = $form->get('picture')->getData();

        // Cette condition est nécessaire car le champ 'image' n'est pas requis
        // donc le fichier PDF doit être traité uniquement lorsqu'un fichier est téléchargé
        if ($imageFile) {

            // Obtient le nom du fichier d'origine sans l'extension
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            
            // Convertit le nom de fichier en un format sûr pour l'inclure dans l'URL
            $safeFilename = $this->slugger->slug($originalFilename);

            // Génère un nouveau nom de fichier unique en ajoutant un identifiant unique et l'extension du fichier
            $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

            // Déplace le fichier vers le répertoire où les images sont stockées
            try {
                $imageFile->move(
                    $directory,
                    $newFilename
                );

            } catch (FileException $e) {
                 // ... gère l'exception s'il se passe quelque chose pendant le téléchargement du fichier
            }
            

            // Met à jour la propriété 'imageFilename' pour stocker le nom du fichier PDF
            // au lieu de son contenu


            return $newFilename;
        }
    }
}


?>