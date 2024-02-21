<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnimeThemeController extends AbstractController
{
    #[Route('/anime/theme', name: 'anime_theme')]
    public function index(): Response
    {
        return $this->render('anime_theme/index.html.twig', [
            'controller_name' => 'AnimeThemeController',
            'navLinkId' => 'anime-theme',
        ]);
    }
}
