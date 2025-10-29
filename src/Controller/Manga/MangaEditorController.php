<?php

namespace App\Controller\Manga;

use App\Entity\MangaEditor;
use App\Form\MangaEditorType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MangaEditorController extends AbstractController
{
    #[Route('/manga/editor/add', name: 'manga_editor_add')]
    public function add(ManagerRegistry $managerRegistry, Request $request): Response
    {

        $editor = new MangaEditor();

        $form = $this->createForm(MangaEditorType::class, $editor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $managerRegistry->getManager()->persist($editor);
            $managerRegistry->getManager()->flush();

            return $this->redirectToRoute('manga');
        }
        return $this->render('manga/manga_editor/add.html.twig', [
            'form' => $form->createView(),
            'navLinkId' => 'manga_editor',
        ]);
    }
}
