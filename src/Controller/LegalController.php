<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LegalController extends AbstractController
{
    #[Route('/mentions-legales', name: 'app_mentions_legales', methods: ['GET'])]
    public function mentionsLegales(): Response
    {
        return $this->render('legal/mentions_legales.html.twig');
    }

    #[Route('/politique-cookies', name: 'app_politique_cookies', methods: ['GET'])]
    public function politiqueCookies(): Response
    {
        return $this->render('legal/politique_cookies.html.twig');
    }

    #[Route('/cgv', name: 'app_cgv', methods: ['GET'])]
    public function cgv(): Response
    {
        return $this->render('legal/cgv.html.twig');
    }
}
