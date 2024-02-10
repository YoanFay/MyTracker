<?php

namespace App\Controller\Manga;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MangaGenreController extends AbstractController
{
    #[Route('/manga/genre', name: 'manga_genre')]
    public function index(): Response
    {
        return $this->render('manga/manga_genre/index.html.twig', [
            'controller_name' => 'MangaGenreController',
        ]);
    }
}
