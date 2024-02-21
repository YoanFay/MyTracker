<?php

namespace App\Controller\Manga;

use App\Entity\Manga;
use App\Form\MangaFormType;
use App\Repository\MangaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\StrSpecialCharsLower;

class MangaController extends AbstractController
{
    #[Route('/manga', name: 'manga')]
    public function index(MangaRepository $mangaRepository): Response
    {
        $mangas = $mangaRepository->getCountOfReadingTomesPerManga();
        
        return $this->render('manga/manga/index.html.twig', [
            'controller_name' => 'MangaController',
            'mangas' => $mangas,
            'navLinkId' => 'manga',
        ]);
    }
    
    #[Route('/manga/details/{id}', name: 'manga_details')]
    public function details(MangaRepository $mangaRepository, $id): Response
    {
        $manga = $mangaRepository->findOneBy(['id' => $id]);
        
        return $this->render('manga/manga/details.html.twig', [
            'controller_name' => 'MangaController',
            'manga' => $manga,
            'navLinkId' => 'manga',
        ]);
    }
    
    #[Route('/manga/add', name: 'manga_add')]
    public function add(ManagerRegistry $managerRegistry, Request $request, StrSpecialCharsLower $strSpecialCharsLower): Response
    {

        $manga = new Manga();

        $form = $this->createForm(MangaFormType::class, $manga);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
        
            $manga->setSlug($strSpecialCharsLower->main($manga->getName()));

            $managerRegistry->getManager()->persist($manga);
            $managerRegistry->getManager()->flush();

            return $this->redirectToRoute('manga_editor');
        }
        return $this->render('manga/manga/add.html.twig', [
            'form' => $form->createView(),
            'controller_name' => 'MangaController',
            'navLinkId' => 'manga',
        ]);
    }
}
