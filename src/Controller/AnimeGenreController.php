<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnimeGenreController extends AbstractController
{
    #[Route('/anime/genre', name: 'anime_genre')]
    public function index(): Response
    {
        return $this->render('anime_genre/index.html.twig', [
            'controller_name' => 'AnimeGenreController',
        ]);
    }
}
