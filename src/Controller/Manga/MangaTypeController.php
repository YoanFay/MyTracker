<?php

namespace App\Controller\Manga;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MangaTypeController extends AbstractController
{
    #[Route('/manga/type', name: 'manga_type')]
    public function index(): Response
    {
        return $this->render('manga/manga_type/index.html.twig', [
            'controller_name' => 'MangaTypeController',
            'navLinkId' => 'manga_type',
        ]);
    }
}
