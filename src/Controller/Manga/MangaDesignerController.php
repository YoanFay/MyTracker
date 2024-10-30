<?php

namespace App\Controller\Manga;

use App\Entity\MangaDesigner;
use App\Form\MangaDesignerType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MangaDesignerController extends AbstractController
{
    #[Route('/manga/designer', name: 'manga_designer')]
    public function index(): Response
    {
        return $this->render('manga/manga_designer/index.html.twig', [
            'controller_name' => 'MangaDesignerController',
            'navLinkId' => 'manga_designer',
        ]);
    }
    
    #[Route('/manga/designer/add', name: 'manga_designer_add')]
    public function add(ManagerRegistry $managerRegistry, Request $request): Response
    {

        $author = new MangaDesigner();

        $form = $this->createForm(MangaDesignerType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $managerRegistry->getManager()->persist($author);
            $managerRegistry->getManager()->flush();

            return $this->redirectToRoute('manga');
        }
        return $this->render('manga/manga_designer/add.html.twig', [
            'form' => $form->createView(),
            'controller_name' => 'MangaDesignerController',
            'navLinkId' => 'manga_designer',
        ]);
    }
}
