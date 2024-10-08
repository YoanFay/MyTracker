<?php

namespace App\Controller\Manga;

use App\Entity\Manga;
use App\Form\MangaFormType;
use App\Repository\MangaRepository;
use App\Repository\MangaTomeRepository;
use App\Service\TimeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\StrSpecialCharsLower;

class MangaController extends AbstractController
{
    #[Route('/manga', name: 'manga')]
    public function index(MangaRepository $mangaRepository, MangaTomeRepository $mangaTomeRepository, TimeService $timeService): Response
    {

        $mangas = $mangaRepository->findAll();

        $mangasInfo = [];

        foreach ($mangas as $manga) {

            $tomeInfo = $mangaTomeRepository->getTomeCountInfo($manga);

            $firstTomeCover = $mangaTomeRepository->getFirstCover($manga);

            if ($firstTomeCover) {
                $firstTomeCover = $firstTomeCover['COVER'];
            }

            $startDate = $timeService->frenchFormatDateNoDay($manga->getReleaseDate());
            $endDate = "En cours";

            if ($manga->getEndDate()){
                $endDate = $timeService->frenchFormatDateNoDay($manga->getEndDate());
            }

            $staff = "<li>Auteur : ".$manga->getAuthor()->getName()." </li>";

            if ($manga->getDesigner()){

                $staff = "<li>Scénariste : ".$manga->getAuthor()->getName()." </li><li>Dessinateur : ".$manga->getDesigner()->getName()." </li>";

            }

            $tooltip = "
                <ul class='text-start'>
                ".$staff."
                <li>Éditeur : ".$manga->getEditor()->getName()." </li>
                <li>Date de publication : ".$startDate." - ".$endDate." </li>
                <li>Tome lu : ".$tomeInfo['tomeRead']." / ".$tomeInfo['tomeRelease']."</li>
                </ul>";

            $mangasInfo[] = [
                'info' => $manga,
                'tomeRead' => $tomeInfo['tomeRead'],
                'tomeRelease' => $tomeInfo['tomeRelease'],
                'firstTomeCover' => $firstTomeCover,
                'tooltip' => $tooltip,
            ];
        }

        return $this->render('manga/manga/index.html.twig', [
            'controller_name' => 'MangaController',
            'mangas' => $mangasInfo,
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

        if ($form->isSubmitted() && $form->isValid()) {

            $manga->setSlug($strSpecialCharsLower->main($manga->getName()));

            $managerRegistry->getManager()->persist($manga);
            $managerRegistry->getManager()->flush();

            return $this->redirectToRoute('manga');
        }
        return $this->render('manga/manga/add.html.twig', [
            'form' => $form->createView(),
            'controller_name' => 'MangaController',
            'navLinkId' => 'manga',
        ]);
    }
}
