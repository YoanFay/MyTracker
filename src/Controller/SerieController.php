<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Form\SerieType;
use App\Form\SerieEditType;
use App\Form\SerieAnimeEditType;
use App\Repository\SerieRepository;
use App\Repository\EpisodeShowRepository;
use App\Repository\SerieTypeRepository;
use App\Service\TVDBService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\StrSpecialCharsLower;

class SerieController extends AbstractController
{

    #[Route('/serie/{id}', name: 'serie')]
    public function index(SerieRepository $serieRepository, EpisodeShowRepository $episodeShowRepository, SerieTypeRepository $serieTypeRepository, $id = -1): Response
    {

        if ($id < 0) {
            $series = $serieRepository->findAll();
        } else if($id === 404) {

            $serieType = $serieTypeRepository->findOneBy(['name' => 'Anime']);

            $series = $serieRepository->noThemeGenre($serieType);
        } else {

            $serieType = $serieTypeRepository->find($id);

            $series = $serieRepository->findBy(['serieType' => $serieType]);
        }

        $serieTab = [];

        foreach ($series as $serie) {

            $lastEpisode = $episodeShowRepository->findLastEpisode($serie);

            $serieTab[] = [
                'id' => $serie->getId(),
                'name' => $serie->getName(),
                'serieType' => $serie->getSerieType()->getName(),
                'artwork' => $serie->getArtwork(),
                'lastDate' => $lastEpisode?->getShowDate(),
            ];

        }

        uasort($serieTab, function ($a, $b) {

            // Utilise strtotime pour convertir les dates en timestamps pour une comparaison facile
            $dateA = $a['lastDate'];
            $dateB = $b['lastDate'];

            // Retourne -1 si $dateA est inférieur à $dateB, 1 si supérieur, 0 si égal
            return $dateB <=> $dateA;
        });

        return $this->render('serie/index.html.twig', [
            'controller_name' => 'SerieController',
            'series' => $serieTab,
            'navLinkId' => 'serie_list',
        ]);
    }


    #[Route('/serie/detail/{id}', name: 'serie_detail')]
    public function detail(SerieRepository $serieRepository, EpisodeShowRepository $episodeShowRepository, $id): Response
    {

        $serie = $serieRepository->findOneBy(['id' => $id]);
        $totalDuration = $episodeShowRepository->getDurationBySerie($id);
        $countEpisode = $episodeShowRepository->getCountBySerie($id);

        $genres = $serie->getGenres();
        $animeGenres = $serie->getAnimeGenres();
        $animeThemes = $serie->getAnimeThemes();

        $tagTypes = [];

        foreach ($serie->getTags() as $tag) {

            if (!array_key_exists($tag->getTagsType()->getNameFra(), $tagTypes)) {
                $tagTypes[$tag->getTagsType()->getNameFra()] = [];
            }

            array_push($tagTypes[$tag->getTagsType()->getNameFra()], $tag);
        }

        return $this->render('serie/details.html.twig', [
            'controller_name' => 'SerieController',
            'serie' => $serie,
            'totalDuration' => $totalDuration['COUNT'],
            'countEpisode' => $countEpisode['COUNT'],
            'genres' => $genres,
            'tagTypes' => $tagTypes,
            'animeGenres' => $animeGenres,
            'animeThemes' => $animeThemes,
            'navLinkId' => 'serie_list',
        ]);
    }


    #[Route('/serie/add', name: 'serie_add')]
    public function addSerie(ManagerRegistry $managerRegistry, Request $request, StrSpecialCharsLower $strSpecialCharsLower, TVDBService $TVDBService, KernelInterface $kernel): Response
    {

        $serie = new Serie();

        $form = $this->createForm(SerieType::class, $serie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($serie->getTvdbId()) {
                $TVDBService->updateSerieName($serie);
            }

            $serie->setSlug($strSpecialCharsLower->serie($serie->getName()));

            if ($serie->getTvdbId()) {
                $TVDBService->updateArtwork($serie, $kernel);
            }

            $managerRegistry->getManager()->persist($serie);
            $managerRegistry->getManager()->flush();

            return $this->redirectToRoute('episode');
        }

        return $this->render('serie/add.html.twig', [
            'controller_name' => 'SerieController',
            'form' => $form->createView(),
            'navLinkId' => 'serie_add',
        ]);
    }


    #[Route('/serie/edit/{id}', name: 'serie_edit')]
    public function editSerie(ManagerRegistry $managerRegistry, SerieRepository $serieRepository, Request $request, $id): Response
    {

        $serie = $serieRepository->findOneBy(['id' => $id]);

        if ($serie->getSerieType()->getName() === "Anime") {
            return $this->redirectToRoute('serie_edit_anime', ['id' => $id]);
        }

        $form = $this->createForm(SerieEditType::class, $serie);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $managerRegistry->getManager()->persist($serie);
            $managerRegistry->getManager()->flush();

            return $this->redirectToRoute('serie_detail', ['id' => $id]);
        }

        return $this->render('serie/edit.html.twig', [
            'controller_name' => 'SerieController',
            'form' => $form->createView(),
            'serie' => $serie,
            'navLinkId' => 'serie_add',
        ]);
    }


    #[Route('/serie/editAnime/{id}', name: 'serie_edit_anime')]
    public function editSerieAnime(ManagerRegistry $managerRegistry, SerieRepository $serieRepository, Request $request, $id): Response
    {

        $serie = $serieRepository->findOneBy(['id' => $id]);

        if ($serie->getSerieType()->getName() !== "Anime") {
            return $this->redirectToRoute('serie_edit', ['id' => $id]);
        }

        $oldAnimeGenre = $serie->getAnimeGenres()->getValues();
        $oldAnimeTheme = $serie->getAnimeThemes()->getValues();

        $form = $this->createForm(SerieAnimeEditType::class, $serie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $animeGenres = $serie->getAnimeGenres()->getValues();

            foreach ($oldAnimeGenre as $genre) {
                $serie->addAnimeGenre($genre);
                $serie->removeAnimeGenre($genre);
            }

            $managerRegistry->getManager()->persist($serie);
            $managerRegistry->getManager()->flush();

            foreach ($animeGenres as $genre) {
                $serie->removeAnimeGenre($genre);
                $serie->addAnimeGenre($genre);
            }

            $animeThemes = $serie->getAnimeThemes()->getValues();

            foreach ($oldAnimeTheme as $theme) {
                $serie->addAnimeTheme($theme);
                $serie->removeAnimeTheme($theme);
            }

            $managerRegistry->getManager()->persist($serie);
            $managerRegistry->getManager()->flush();

            foreach ($animeThemes as $theme) {
                $serie->removeAnimeTheme($theme);
                $serie->addAnimeTheme($theme);
            }

            $managerRegistry->getManager()->persist($serie);
            $managerRegistry->getManager()->flush();

            return $this->redirectToRoute('serie_detail', ['id' => $id]);
        }

        return $this->render('serie/editAnime.html.twig', [
            'controller_name' => 'SerieController',
            'form' => $form->createView(),
            'serie' => $serie,
            'navLinkId' => 'serie_add',
        ]);
    }
}
