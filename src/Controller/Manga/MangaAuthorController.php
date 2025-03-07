<?php

namespace App\Controller\Manga;

use App\Entity\MangaAuthor;
use App\Form\MangaAuthorType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MangaAuthorController extends AbstractController
{
    #[Route('/manga/author', name: 'manga_author')]
    public function index(): Response
    {
        return $this->render('manga/manga_author/index.html.twig', [
            'controller_name' => 'MangaAuthorController',
            'navLinkId' => 'manga_author',
        ]);
    }
    
    #[Route('/manga/author/add', name: 'manga_author_add')]
    public function add(ManagerRegistry $managerRegistry, Request $request): Response
    {

        $author = new MangaAuthor();

        $form = $this->createForm(MangaAuthorType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $managerRegistry->getManager()->persist($author);
            $managerRegistry->getManager()->flush();

            return $this->redirectToRoute('manga');
        }
        return $this->render('manga/manga_author/add.html.twig', [
            'form' => $form->createView(),
            'controller_name' => 'MangaAuthorController',
            'navLinkId' => 'manga_author',
        ]);
    }
}
