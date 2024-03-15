<?php

namespace App\Controller\Manga;

use App\Entity\Manga;
use App\Form\MangaFormType;
use App\Repository\MangaRepository;
use App\Repository\MangaTomeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\StrSpecialCharsLower;

class MangaController extends AbstractController
{
    #[Route('/manga', name: 'manga')]
    public function index(MangaRepository $mangaRepository, MangaTomeRepository $mangaTomeRepository): Response
    {
        $mangas = $mangaRepository->findAll();

        $mangasInfo = [];

        foreach ($mangas as $manga) {
            $tomeInfo = $mangaTomeRepository->getTomeCountInfo($manga);

            $mangasInfo[] = [
                'info' => $manga,
                'tomeRead' => $tomeInfo['tomeRead'],
                'tomeRelease' => $tomeInfo['tomeRelease'],
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

    #[Route('/manga/statistique', name: 'manga_stat')]
    public function mangaStat(MangaRepository $mangaRepository): Response
    {

        $mangaTomesByGenre = $mangaRepository->getMangaTomeByGenre();
        $mangaTomesByTheme = $mangaRepository->getMangaTomeByTheme();

        $labelGenreChart = "[";
        $genreChart = "[";

        foreach ($mangaTomesByGenre as $count) {

            $labelGenreChart .= '"'.$count['name'] . '", ';
            $genreChart .= $count['COUNT'] . ", ";
        }

        $labelGenreChart = rtrim($labelGenreChart, ", ") . "]";
        $genreChart = rtrim($genreChart, ", ") . "]";

        $labelThemeChart = "[";
        $themeChart = "[";

        foreach ($mangaTomesByTheme as $count) {

            $labelThemeChart .= '"'.$count['name'] . '", ';
            $themeChart .= $count['COUNT'] . ", ";
        }


        $labelThemeChart = rtrim($labelThemeChart, ", ") . "]";
        $themeChart = rtrim($themeChart, ", ") . "]";

        return $this->render('manga/manga/stat.html.twig', [
            'labelGenreChart' => $labelGenreChart,
            'genreChart' => $genreChart,
            'labelThemeChart' => $labelThemeChart,
            'themeChart' => $themeChart,
            'navLinkId' => 'manga-stat',
        ]);
    }
}
