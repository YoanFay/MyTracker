<?php

namespace App\Controller\Manga;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MangaThemeController extends AbstractController
{
    #[Route('/manga/theme', name: 'manga_theme')]
    public function index(): Response
    {
        return $this->render('manga/manga_theme/index.html.twig', [
            'controller_name' => 'MangaThemeController',
            'navLinkId' => 'manga_theme',
        ]);
    }
}
